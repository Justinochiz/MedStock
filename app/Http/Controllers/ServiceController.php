<?php

namespace App\Http\Controllers;

use App\DataTables\ServicesDataTable;
use App\Imports\ServiceImport;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Review;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Validator;
use App\Mail\SendOrderStatus;

class ServiceController extends Controller
{
    private const SERVICE_CART_KEY_PREFIX = 'service_cart_user_';

    public function shop()
    {
        $services = Service::query()->orderByDesc('service_id')->get();

        return view('service.shop', compact('services'));
    }

    public function showPublic(int $id)
    {
        $service = Service::findOrFail($id);

        $reviews = Review::with('user')
            ->where('service_id', $service->service_id)
            ->orderByDesc('created_at')
            ->get();

        $averageRating = round((float) $reviews->avg('rating'), 1);
        $canReview = false;
        $userReview = null;

        if (Auth::check()) {
            $canReview = $this->hasPurchasedService((int) Auth::id(), (int) $service->service_id);
            $userReview = $reviews->firstWhere('user_id', Auth::id());
        }

        return view('service.show', compact('service', 'reviews', 'averageRating', 'canReview', 'userReview'));
    }

    public function buyNow(Request $request, int $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:99',
        ]);

        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('message', 'Please login or register to continue with service purchase.');
        }

        $service = Service::findOrFail($id);

        return redirect()->route('services.checkout', [
            'service' => $service->service_id,
            'quantity' => (int) $request->quantity,
        ]);
    }

    public function addToCart(Request $request, int $id)
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('message', 'Please login or register to continue with service purchase.');
        }

        $request->validate([
            'quantity' => 'required|integer|min:1|max:99',
        ]);

        $service = Service::findOrFail($id);
        $requestedQty = (int) $request->input('quantity', 1);
        $cart = $this->getServiceCart();

        if (isset($cart[$id])) {
            $cart[$id]['qty'] += $requestedQty;
        } else {
            $cart[$id] = [
                'service_id' => $service->service_id,
                'name' => $service->name,
                'price' => (float) $service->price,
                'img_path' => $service->img_path,
                'gallery_paths' => $service->gallery_paths,
                'qty' => $requestedQty,
            ];
        }

        session()->put($this->serviceCartSessionKey(), $cart);

        return redirect()->back()->with('success', $requestedQty . ' service(s) added to cart.');
    }

    public function getCart()
    {
        $cart = $this->getServiceCart();

        if (empty($cart)) {
            return view('service.shopping-cart', [
                'services' => [],
                'totalPrice' => 0,
                'totalQty' => 0,
            ]);
        }

        $services = array_values($cart);
        $totalQty = array_sum(array_map(function (array $line) {
            return (int) ($line['qty'] ?? 0);
        }, $services));
        $totalPrice = array_sum(array_map(function (array $line) {
            return ((float) ($line['price'] ?? 0)) * ((int) ($line['qty'] ?? 0));
        }, $services));

        return view('service.shopping-cart', compact('services', 'totalPrice', 'totalQty'));
    }

    public function getReduceByOne(int $id)
    {
        $cart = $this->getServiceCart();

        if (!isset($cart[$id])) {
            return redirect()->route('services.cart');
        }

        $cart[$id]['qty'] = max(0, ((int) $cart[$id]['qty']) - 1);
        if ($cart[$id]['qty'] < 1) {
            unset($cart[$id]);
        }

        $this->persistServiceCart($cart);

        return redirect()->route('services.cart');
    }

    public function getRemoveItem(int $id)
    {
        $cart = $this->getServiceCart();
        if (isset($cart[$id])) {
            unset($cart[$id]);
            $this->persistServiceCart($cart);
        }

        return redirect()->route('services.cart');
    }

    public function showCartCheckout(Request $request)
    {
        $cart = $this->getServiceCart();
        if (empty($cart)) {
            return redirect()->route('services.cart');
        }

        $request->validate([
            'payment_method' => 'nullable|in:cash_on_delivery,gcash,credit_card,debit_card',
        ]);

        $customer = Customer::where('user_id', Auth::id())->first();
        if (!$customer) {
            return redirect()->route('profile.edit')->with('error', 'Please complete your profile before checkout.');
        }

        $services = array_values($cart);
        $totalQty = array_sum(array_map(function (array $line) {
            return (int) ($line['qty'] ?? 0);
        }, $services));
        $subtotal = array_sum(array_map(function (array $line) {
            return ((float) ($line['price'] ?? 0)) * ((int) ($line['qty'] ?? 0));
        }, $services));

        return view('service.checkout-cart', [
            'customer' => $customer,
            'services' => $services,
            'totalQty' => $totalQty,
            'subtotal' => $subtotal,
            'total' => $subtotal,
            'selectedPaymentMethod' => $request->query('payment_method', 'cash_on_delivery'),
        ]);
    }

    public function postCartCheckout(Request $request)
    {
        $cart = $this->getServiceCart();
        if (empty($cart)) {
            return redirect()->route('services.cart');
        }

        $request->validate([
            'payment_method' => 'required|in:cash_on_delivery,gcash,credit_card,debit_card',
        ]);

        $customer = Customer::where('user_id', Auth::id())->first();
        if (!$customer) {
            return redirect()->route('profile.edit')->with('error', 'Please complete your profile before checkout.');
        }

        $services = array_values($cart);
        $subtotal = array_sum(array_map(function (array $line) {
            return ((float) ($line['price'] ?? 0)) * ((int) ($line['qty'] ?? 0));
        }, $services));

        try {
            DB::beginTransaction();

            $order = new Order();
            $order->customer_id = $customer->customer_id;
            $order->date_placed = now();
            $order->date_shipped = now()->addDays(5);
            $order->shipping = 0;
            $order->discount_code = null;
            $order->discount_amount = 0;
            $order->subtotal_amount = $subtotal;
            $order->total_amount = $subtotal;
            $order->payment_method = $request->input('payment_method');
            $order->status = 'Processing';
            $order->save();

            foreach ($services as $serviceLine) {
                DB::table('service_orderline')->insert([
                    'service_id' => (int) $serviceLine['service_id'],
                    'orderinfo_id' => $order->orderinfo_id,
                    'quantity' => (int) $serviceLine['qty'],
                ]);
            }

            DB::commit();

            $orderLines = collect($services)->map(function (array $serviceLine) {
                return (object) [
                    'description' => (string) ($serviceLine['name'] ?? 'Service'),
                    'quantity' => (int) ($serviceLine['qty'] ?? 0),
                    'sell_price' => (float) ($serviceLine['price'] ?? 0),
                    'img_path' => $serviceLine['img_path'] ?? null,
                ];
            });

            try {
                $receiptData = [
                    'order_number' => $order->orderinfo_id,
                    'customer_name' => $this->formatCustomerName($order->customer, Auth::user()->name),
                    'customer_email' => Auth::user()->email,
                    'customer_phone' => $order->customer->phone,
                    'shipping_address' => $this->formatCustomerAddress($order->customer),
                    'payment_method' => $order->payment_method,
                    'status' => $order->status,
                    'date_placed' => $order->date_placed,
                    'total_amount' => $order->total_amount,
                ];

                Mail::to((string) Auth::user()->email)->send(new SendOrderStatus($orderLines, $receiptData));
            } catch (\Throwable $mailException) {
                // Do not fail a successful transaction because of a mail transport issue.
            }

            session()->forget($this->serviceCartSessionKey());

            return redirect()->route('home')->with('success', 'Service order placed successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Unable to process service checkout right now. Please try again.');
        }
    }

    public function showCheckout(Request $request, int $id)
    {
        $service = Service::findOrFail($id);

        $request->validate([
            'quantity' => 'nullable|integer|min:1|max:99',
            'payment_method' => 'nullable|in:cash_on_delivery,gcash,credit_card,debit_card',
        ]);

        $customer = Customer::where('user_id', Auth::id())->first();
        if (!$customer) {
            return redirect()->route('profile.edit')->with('error', 'Please complete your profile before checkout.');
        }

        $quantity = (int) $request->query('quantity', 1);
        $subtotal = (float) $service->price * $quantity;

        return view('service.checkout', [
            'service' => $service,
            'customer' => $customer,
            'quantity' => $quantity,
            'subtotal' => $subtotal,
            'total' => $subtotal,
            'selectedPaymentMethod' => $request->query('payment_method', 'cash_on_delivery'),
        ]);
    }

    public function postCheckout(Request $request, int $id)
    {
        $service = Service::findOrFail($id);

        $request->validate([
            'quantity' => 'required|integer|min:1|max:99',
            'payment_method' => 'required|in:cash_on_delivery,gcash,credit_card,debit_card',
        ]);

        $customer = Customer::where('user_id', Auth::id())->first();
        if (!$customer) {
            return redirect()->route('profile.edit')->with('error', 'Please complete your profile before checkout.');
        }

        $quantity = (int) $request->input('quantity');
        $subtotal = (float) $service->price * $quantity;

        try {
            DB::beginTransaction();

            $order = new Order();
            $order->customer_id = $customer->customer_id;
            $order->date_placed = now();
            $order->date_shipped = now()->addDays(5);
            $order->shipping = 0;
            $order->discount_code = null;
            $order->discount_amount = 0;
            $order->subtotal_amount = $subtotal;
            $order->total_amount = $subtotal;
            $order->payment_method = $request->input('payment_method');
            $order->status = 'Processing';
            $order->save();

            DB::table('service_orderline')->insert([
                'service_id' => $service->service_id,
                'orderinfo_id' => $order->orderinfo_id,
                'quantity' => $quantity,
            ]);

            DB::commit();

            $orderLines = collect([
                (object) [
                    'description' => (string) $service->name,
                    'quantity' => $quantity,
                    'sell_price' => (float) $service->price,
                    'img_path' => $service->img_path,
                ],
            ]);

            try {
                $receiptData = [
                    'order_number' => $order->orderinfo_id,
                    'customer_name' => $this->formatCustomerName($order->customer, Auth::user()->name),
                    'customer_email' => Auth::user()->email,
                    'customer_phone' => $order->customer->phone,
                    'shipping_address' => $this->formatCustomerAddress($order->customer),
                    'payment_method' => $order->payment_method,
                    'status' => $order->status,
                    'date_placed' => $order->date_placed,
                    'total_amount' => $order->total_amount,
                ];

                Mail::to((string) Auth::user()->email)->send(new SendOrderStatus($orderLines, $receiptData));
            } catch (\Throwable $mailException) {
                // Do not fail a successful transaction because of a mail transport issue.
            }

            return redirect()->route('home')->with('success', 'Service order placed successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Unable to process service checkout right now. Please try again.');
        }
    }

    private function formatCustomerName(Customer $customer, string $fallbackName): string
    {
        $fullName = trim(($customer->fname ?? '') . ' ' . ($customer->lname ?? ''));

        return $fullName !== '' ? $fullName : $fallbackName;
    }

    private function formatCustomerAddress(Customer $customer): string
    {
        return implode(', ', array_filter([
            $customer->addressline,
            $customer->town,
            $customer->zipcode,
        ]));
    }

    private function getServiceCart(): array
    {
        $cart = session()->get($this->serviceCartSessionKey(), []);

        return is_array($cart) ? $cart : [];
    }

    private function persistServiceCart(array $cart): void
    {
        $cartKey = $this->serviceCartSessionKey();

        if (empty($cart)) {
            session()->forget($cartKey);
            return;
        }

        session()->put($cartKey, $cart);
    }

    private function serviceCartSessionKey(): string
    {
        $userId = Auth::id();

        return self::SERVICE_CART_KEY_PREFIX . ($userId ?: 'guest');
    }

    private function hasPurchasedService(int $userId, int $serviceId): bool
    {
        return DB::table('service_orderline')
            ->join('orderinfo', 'service_orderline.orderinfo_id', '=', 'orderinfo.orderinfo_id')
            ->join('customer', 'orderinfo.customer_id', '=', 'customer.customer_id')
            ->where('customer.user_id', $userId)
            ->where('service_orderline.service_id', $serviceId)
            ->where('orderinfo.status', '!=', 'Canceled')
            ->exists();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(ServicesDataTable $dataTable)
    {
        return $dataTable->render('service.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('service.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|min:3|max:255',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
        ];

        $messages = [
            'name.required' => 'Service name is required.',
            'name.min' => 'Service name must be at least 3 characters.',
            'name.max' => 'Service name may not be greater than 255 characters.',
            'description.max' => 'Description may not be greater than 1000 characters.',
            'price.required' => 'Price is required.',
            'price.numeric' => 'Price must be a valid number.',
            'price.min' => 'Price cannot be negative.',
            'image.image' => 'Main photo must be a valid image file.',
            'image.mimes' => 'Main photo must be a JPG, JPEG, or PNG file.',
            'image.max' => 'Main photo may not be greater than 2MB.',
            'images.array' => 'Additional photos format is invalid.',
            'images.*.image' => 'Each additional photo must be a valid image file.',
            'images.*.mimes' => 'Each additional photo must be a JPG, JPEG, or PNG file.',
            'images.*.max' => 'Each additional photo may not be greater than 2MB.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $uploadedImages = $this->storeUploadedImages($request);
        $path = $uploadedImages[0] ?? 'default.jpg';
        $galleryPaths = !empty($uploadedImages) ? $uploadedImages : [$path];

        Service::create([
            'name' => trim($request->name),
            'description' => $request->description,
            'price' => $request->price,
            'img_path' => $path,
            'gallery_paths' => $galleryPaths,
        ]);

        return redirect()->route('services.index')->with('success', 'Service added successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $service = Service::find($id);

        if (!$service) {
            return redirect()->route('services.index')->with('error', 'Service not found.');
        }

        return view('service.edit', compact('service'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $service = Service::find($id);

        if (!$service) {
            return redirect()->route('services.index')->with('error', 'Service not found.');
        }

        $rules = [
            'name' => 'required|string|min:3|max:255',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
        ];

        $messages = [
            'name.required' => 'Service name is required.',
            'name.min' => 'Service name must be at least 3 characters.',
            'name.max' => 'Service name may not be greater than 255 characters.',
            'description.max' => 'Description may not be greater than 1000 characters.',
            'price.required' => 'Price is required.',
            'price.numeric' => 'Price must be a valid number.',
            'price.min' => 'Price cannot be negative.',
            'image.image' => 'Main photo must be a valid image file.',
            'image.mimes' => 'Main photo must be a JPG, JPEG, or PNG file.',
            'image.max' => 'Main photo may not be greater than 2MB.',
            'images.array' => 'Additional photos format is invalid.',
            'images.*.image' => 'Each additional photo must be a valid image file.',
            'images.*.mimes' => 'Each additional photo must be a JPG, JPEG, or PNG file.',
            'images.*.max' => 'Each additional photo may not be greater than 2MB.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $service->name = trim($request->name);
        $service->description = $request->description;
        $service->price = $request->price;

        $uploadedImages = $this->storeUploadedImages($request);
        if (!empty($uploadedImages)) {
            $service->img_path = $uploadedImages[0];
            $service->gallery_paths = $uploadedImages;
        } elseif (empty($service->gallery_paths) && !empty($service->img_path)) {
            $service->gallery_paths = [$service->img_path];
        }

        $service->save();

        return redirect()->route('services.index')->with('success', 'Service updated successfully.');
    }

    private function storeUploadedImages(Request $request): array
    {
        $storedPaths = [];

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $storedPaths[] = Storage::putFileAs(
                'public/images',
                $request->file('image'),
                $request->file('image')->hashName()
            );
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                if ($image && $image->isValid()) {
                    $storedPaths[] = Storage::putFileAs(
                        'public/images',
                        $image,
                        $image->hashName()
                    );
                }
            }
        }

        return array_values(array_unique($storedPaths));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $service = Service::find($id);

        if (!$service) {
            return redirect()->route('services.index')->with('error', 'Service not found.');
        }

        $service->delete();

        return redirect()->route('services.index')->with('success', 'Service deleted successfully.');
    }

    /**
     * Restore the specified soft deleted resource.
     */
    public function restore(string $id)
    {
        $service = Service::withTrashed()->find($id);

        if (!$service) {
            return redirect()->route('services.index')->with('error', 'Service not found.');
        }

        $service->restore();

        return redirect()->route('services.index')->with('success', 'Service restored successfully.');
    }

    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_upload' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Excel::import(
            new ServiceImport,
            $request->file('service_upload')->storeAs(
                'files',
                $request->file('service_upload')->getClientOriginalName()
            )
        );

        return redirect()->route('services.index')->with('success', 'Service Excel file imported successfully.');
    }
}
