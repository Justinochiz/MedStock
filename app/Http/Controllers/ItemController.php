<?php

namespace App\Http\Controllers;

use App\DataTables\ItemsDataTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\Item;
use App\Models\Stock;
use App\Models\Order;
use App\Imports\ItemImport;
use App\Imports\ItemStockImport;
use Maatwebsite\Excel\Facades\Excel;
use Session;
use App\Cart;
use Carbon\Carbon;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use App\Models\Review;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendOrderStatus;


class ItemController extends Controller
{
    private const PRODUCT_CART_KEY_PREFIX = 'cart_user_';
    private const BUY_NOW_BACKUP_KEY_PREFIX = 'cart_backup_for_buy_now_user_';
    private const BUY_NOW_FLAG_KEY_PREFIX = 'is_buy_now_user_';

    private const DISCOUNT_CODES = [
        'MEDSAVE5' => 5,
        'CARE10' => 10,
        'HEALTH15' => 15,
        'WELL20' => 20,
        'IMED25' => 25,
    ];

    /**
     * Display a listing of the resource.
     */
    public function index(ItemsDataTable $dataTable)
    {
        return $dataTable->render('item.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('item.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'description' => 'required|string|min:4|max:255',
            'category' => 'required|string|min:2|max:100',
            'brand' => 'nullable|string|min:2|max:100',
            'cost_price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0|gte:cost_price',
            'quantity' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpg,png,jpeg|max:2048'
        ];
        $messages = [
            'description.required' => 'Item name is required.',
            'description.min' => 'Item name must be at least 4 characters.',
            'description.max' => 'Item name may not be greater than 255 characters.',
            'category.required' => 'Category is required.',
            'category.min' => 'Category must be at least 2 characters.',
            'category.max' => 'Category may not be greater than 100 characters.',
            'brand.min' => 'Brand must be at least 2 characters.',
            'brand.max' => 'Brand may not be greater than 100 characters.',
            'cost_price.required' => 'Cost price is required.',
            'cost_price.numeric' => 'Cost price must be a valid number.',
            'cost_price.min' => 'Cost price cannot be negative.',
            'sell_price.required' => 'Selling price is required.',
            'sell_price.numeric' => 'Selling price must be a valid number.',
            'sell_price.min' => 'Selling price cannot be negative.',
            'sell_price.gte' => 'Selling price must be greater than or equal to cost price.',
            'quantity.required' => 'Quantity is required.',
            'quantity.integer' => 'Quantity must be a whole number.',
            'quantity.min' => 'Quantity cannot be negative.',
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
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $uploadedImages = $this->storeUploadedImages($request);
        $path = $uploadedImages[0] ?? 'default.jpg';
        $galleryPaths = !empty($uploadedImages) ? $uploadedImages : [$path];

        $item = Item::create([
            'description' => trim($request->description),
            'category' => trim($request->category),
            'brand' => trim((string) $request->input('brand', '')) !== '' ? trim((string) $request->input('brand')) : null,
            'cost_price' => $request->cost_price,
            'sell_price' => $request->sell_price,
            'img_path' => $path,
            'gallery_paths' => $galleryPaths,
        ]);

        $stock = new Stock();
        $stock->item_id = $item->item_id;
        $stock->quantity = $request->quantity;
        $stock->save();

        return redirect()->route('items.create')->with('success', 'item added');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $item = Item::find($id);
        $stock = Stock::where('item_id', $id)->first();
        
        if (!$item) {
            abort(404, 'Item not found');
        }

        $reviews = Review::with('user')
            ->where('item_id', $item->item_id)
            ->orderByDesc('created_at')
            ->get();

        $averageRating = round((float) $reviews->avg('rating'), 1);
        $canReview = false;
        $userReview = null;

        if (Auth::check()) {
            $canReview = $this->hasPurchasedItem((int) Auth::id(), (int) $item->item_id);
            $userReview = $reviews->firstWhere('user_id', Auth::id());
        }

        return view('item.show', compact('item', 'stock', 'reviews', 'averageRating', 'canReview', 'userReview'));
    }

    private function hasPurchasedItem(int $userId, int $itemId): bool
    {
        return DB::table('orderline')
            ->join('orderinfo', 'orderline.orderinfo_id', '=', 'orderinfo.orderinfo_id')
            ->join('customer', 'orderinfo.customer_id', '=', 'customer.customer_id')
            ->where('customer.user_id', $userId)
            ->where('orderline.item_id', $itemId)
            ->whereRaw('LOWER(orderinfo.status) = ?', ['delivered'])
            ->exists();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $item = Item::find($id);
        $stock = Stock::find($id);
        // dd($stock);
        return view('item.edit', compact('item', 'stock'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $item = Item::find($id);

        if (!$item) {
            return redirect()->route('items.index')->with('error', 'Item not found.');
        }

        $rules = [
            'description' => 'required|string|min:4|max:255',
            'category' => 'required|string|min:2|max:100',
            'brand' => 'nullable|string|min:2|max:100',
            'cost_price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0|gte:cost_price',
            'quantity' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'remove_photos' => 'nullable|array',
            'remove_photos.*' => 'string',
        ];

        $messages = [
            'description.required' => 'Item name is required.',
            'description.min' => 'Item name must be at least 4 characters.',
            'description.max' => 'Item name may not be greater than 255 characters.',
            'category.required' => 'Category is required.',
            'category.min' => 'Category must be at least 2 characters.',
            'category.max' => 'Category may not be greater than 100 characters.',
            'brand.min' => 'Brand must be at least 2 characters.',
            'brand.max' => 'Brand may not be greater than 100 characters.',
            'cost_price.required' => 'Cost price is required.',
            'cost_price.numeric' => 'Cost price must be a valid number.',
            'cost_price.min' => 'Cost price cannot be negative.',
            'sell_price.required' => 'Selling price is required.',
            'sell_price.numeric' => 'Selling price must be a valid number.',
            'sell_price.min' => 'Selling price cannot be negative.',
            'sell_price.gte' => 'Selling price must be greater than or equal to cost price.',
            'quantity.required' => 'Quantity is required.',
            'quantity.integer' => 'Quantity must be a whole number.',
            'quantity.min' => 'Quantity cannot be negative.',
            'image.image' => 'Main photo must be a valid image file.',
            'image.mimes' => 'Main photo must be a JPG, JPEG, or PNG file.',
            'image.max' => 'Main photo may not be greater than 2MB.',
            'images.array' => 'Additional photos format is invalid.',
            'images.*.image' => 'Each additional photo must be a valid image file.',
            'images.*.mimes' => 'Each additional photo must be a JPG, JPEG, or PNG file.',
            'images.*.max' => 'Each additional photo may not be greater than 2MB.',
            'remove_photos.array' => 'Selected photos to remove are invalid.',
            'remove_photos.*.string' => 'Selected photo path is invalid.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $item->description = $request->description;
        $item->category = trim($request->category);
        $item->brand = trim((string) $request->input('brand', '')) !== '' ? trim((string) $request->input('brand')) : null;
        $item->cost_price = $request->cost_price;
        $item->sell_price = $request->sell_price;

        $uploadedImages = $this->storeUploadedImages($request);
        $existingGallery = $item->imageGallery();
        $photosToRemove = array_values(array_unique((array) $request->input('remove_photos', [])));
        $keptGallery = array_values(array_filter($existingGallery, function ($photoPath) use ($photosToRemove) {
            return !in_array($photoPath, $photosToRemove, true);
        }));

        if (!empty($photosToRemove)) {
            $this->deleteImagesFromStorage($photosToRemove);
        }

        if (!empty($uploadedImages)) {
            // Keep existing images and prepend new uploads so the latest upload is the main photo.
            $mergedGallery = array_values(array_unique(array_merge($uploadedImages, $keptGallery)));
            $item->img_path = $mergedGallery[0];
            $item->gallery_paths = $mergedGallery;
        } elseif (!empty($keptGallery)) {
            $item->img_path = $keptGallery[0];
            $item->gallery_paths = $keptGallery;
        } else {
            $item->img_path = 'default.jpg';
            $item->gallery_paths = ['default.jpg'];
        }
        
        $item->save();

        $stock = Stock::where('item_id', $item->item_id)->first();
        if ($stock) {
            $stock->quantity = $request->quantity;
            $stock->save();
        } else {
            $stock = new Stock;
            $stock->item_id = $item->item_id;
            $stock->quantity = $request->quantity;
            $stock->save();
        }
        
        return redirect()->route('items.index')->with('success', 'item updated successfully');
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

    private function deleteImagesFromStorage(array $photoPaths): void
    {
        foreach ($photoPaths as $path) {
            $normalizedPath = str_replace('public/', '', (string) $path);

            if ($normalizedPath === '' || $normalizedPath === 'default.jpg') {
                continue;
            }

            if (str_starts_with($normalizedPath, 'images/') && Storage::disk('public')->exists($normalizedPath)) {
                Storage::disk('public')->delete($normalizedPath);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $item = Item::find($id);

            if (!$item) {
                return redirect()->route('items.index')->with('error', 'Item not found.');
            }

            $item->delete();

            return redirect()->route('items.index')->with('success', 'Item deleted successfully.');
        } catch (\Throwable $e) {
            return redirect()->route('items.index')->with('error', 'Unable to delete item.');
        }
    }

    public function restore(string $id)
    {
        try {
            $item = Item::withTrashed()->find($id);

            if (!$item) {
                return redirect()->route('items.index')->with('error', 'Item not found.');
            }

            $item->restore();

            return redirect()->route('items.index')->with('success', 'Item restored successfully.');
        } catch (\Throwable $e) {
            return redirect()->route('items.index')->with('error', 'Unable to restore item.');
        }
    }

    public function import()
    {

        Excel::import(
            new ItemStockImport,
            request()
                ->file('item_upload')
                ->storeAs(
                    'files',
                    request()
                        ->file('item_upload')
                        ->getClientOriginalName()
                )
        );
        return redirect()->back()->with('success', 'Excel file Imported Successfully');
    }

    public function getItems()
    {
        // dump(Session::get('cart'));
        $items = DB::table('item')
            ->join('stock', 'item.item_id', '=', 'stock.item_id')
            ->whereNull('item.deleted_at')
            ->get();
        return view('shop.index', compact('items'));
    }

    public function addToCart(Request $request, $id)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('message', 'Please login or register to continue shopping and add items to cart.');
        }

        $item = Item::find($id);
        if (!$item) {
            return redirect()->back()->with('error', 'Item not found.');
        }

        $stock = Stock::where('item_id', $id)->first();
        $availableQty = (int) ($stock->quantity ?? 0);
        if ($availableQty < 1) {
            return redirect()->back()->with('error', 'This item is out of stock.');
        }

        $requestedQty = (int) $request->input('quantity', 1);
        if ($requestedQty < 1) {
            return redirect()->back()->with('error', 'Quantity must be at least 1.');
        }

        $cartKey = $this->productCartSessionKey();
        $oldCart = Session::has($cartKey) ? Session::get($cartKey) : null;

        $existingQtyInCart = 0;
        if ($oldCart && isset($oldCart->items[$id])) {
            $existingQtyInCart = (int) $oldCart->items[$id]['qty'];
        }

        if (($existingQtyInCart + $requestedQty) > $availableQty) {
            return redirect()->back()->with(
                'error',
                'Only ' . $availableQty . ' item(s) are available. You already have ' . $existingQtyInCart . ' in cart.'
            );
        }

        // dd($oldCart);
        $cart = new Cart($oldCart);
        // dd($cart);
        $cart->add($item, $id, $requestedQty);
        // dd($cart);

        Session::put($cartKey, $cart);

        return redirect()->back()->with('success', $requestedQty . ' item(s) added to cart.');
    }

    public function buyNow(Request $request, $id)
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('message', 'Please login or register to continue shopping and buy items.');
        }

        $item = Item::find($id);
        if (!$item) {
            return redirect()->back()->with('error', 'Item not found.');
        }

        $stock = Stock::where('item_id', $id)->first();
        $availableQty = (int) ($stock->quantity ?? 0);
        if ($availableQty < 1) {
            return redirect()->back()->with('error', 'This item is out of stock.');
        }

        $requestedQty = (int) $request->input('quantity', 1);
        if ($requestedQty < 1) {
            return redirect()->back()->with('error', 'Quantity must be at least 1.');
        }

        if ($requestedQty > $availableQty) {
            return redirect()->back()->with('error', 'Only ' . $availableQty . ' item(s) are available.');
        }

        $cartKey = $this->productCartSessionKey();
        $buyNowBackupKey = $this->buyNowBackupSessionKey();
        $buyNowFlagKey = $this->buyNowFlagSessionKey();

        if (Session::has($cartKey)) {
            Session::put($buyNowBackupKey, Session::get($cartKey));
        }

        $cart = new Cart(null);
        $cart->add($item, $id, $requestedQty);

        Session::put($cartKey, $cart);
        Session::put($buyNowFlagKey, true);

        return redirect()->route('checkout');
    }

    public function getCart()
    {
        // dump(Session::get('cart'));
        $cartKey = $this->productCartSessionKey();
        if (!Session::has($cartKey)) {
            return view('shop.shopping-cart');
        }
        $oldCart = Session::get($cartKey);
        $cart = new Cart($oldCart);
        // dd($cart);
        return view('shop.shopping-cart', ['products' => $cart->items, 'totalPrice' => $cart->totalPrice]);
    }

    public function showCheckout(Request $request)
    {
        $cartKey = $this->productCartSessionKey();
        if (!Session::has($cartKey)) {
            return redirect()->route('getCart');
        }

        $customer = Customer::where('user_id', Auth::id())->first();
        if (!$customer) {
            return redirect()->route('profile.edit')->with('error', 'Please complete your profile before checkout.');
        }

        $oldCart = Session::get($cartKey);
        $cart = new Cart($oldCart);
        $discountCode = strtoupper(trim((string) $request->query('discount_code', '')));
        $summary = $this->buildCheckoutSummary($cart->totalPrice, $discountCode);

        return view('shop.checkout', [
            'products' => $cart->items,
            'customer' => $customer,
            'summary' => $summary,
            'discountCodes' => self::DISCOUNT_CODES,
            'selectedPaymentMethod' => $request->query('payment_method', 'cash_on_delivery'),
        ]);
    }

    public function getReduceByOne($id)
    {
        $cartKey = $this->productCartSessionKey();
        $oldCart = Session::has($cartKey) ? Session::get($cartKey) : null;
        if (!$oldCart || empty($oldCart->items) || !isset($oldCart->items[$id])) {
            return redirect()->route('getCart');
        }

        $cart = new Cart($oldCart);
        $cart->reduceByOne($id);
        if (!empty($cart->items)) {
            Session::put($cartKey, $cart);
        } else {
            Session::forget($cartKey);
        }
        return redirect()->route('getCart');
    }

    public function getRemoveItem($id)
    {
        $cartKey = $this->productCartSessionKey();
        $oldCart = Session::has($cartKey) ? Session::get($cartKey) : null;
        if (!$oldCart || empty($oldCart->items) || !isset($oldCart->items[$id])) {
            return redirect()->route('getCart');
        }

        $cart = new Cart($oldCart);
        $cart->removeItem($id);
        if (!empty($cart->items)) {
            Session::put($cartKey, $cart);
        } else {
            Session::forget($cartKey);
        }
        return redirect()->route('getCart');
    }

    public function postCheckout()
    {
        $cartKey = $this->productCartSessionKey();
        $buyNowBackupKey = $this->buyNowBackupSessionKey();
        $buyNowFlagKey = $this->buyNowFlagSessionKey();

        if (!Session::has($cartKey)) {
            return redirect()->route('getCart');
        }

        $request = request();
        $request->validate([
            'discount_code' => 'nullable|string|max:50',
            'payment_method' => 'required|in:cash_on_delivery,gcash,credit_card,debit_card',
        ]);

        $oldCart = Session::get($cartKey);
        $cart = new Cart($oldCart);
        $customer = Customer::where('user_id', Auth::id())->first();
        if (!$customer) {
            return redirect()->route('profile.edit')->with('error', 'Please complete your profile before checkout.');
        }

        $discountCode = strtoupper(trim((string) $request->input('discount_code', '')));
        $summary = $this->buildCheckoutSummary($cart->totalPrice, $discountCode);
        // dd($cart, $cart->items);
        try {


            // dd($customer);
            DB::beginTransaction();
            $order = new Order();
            $order->customer_id = $customer->customer_id;
            $order->date_placed = now();
            $order->date_shipped = Carbon::now()->addDays(5);

            $order->shipping = 0.00;
            $order->discount_code = $summary['appliedCode'];
            $order->discount_amount = $summary['discountAmount'];
            $order->subtotal_amount = $summary['subtotal'];
            $order->total_amount = $summary['total'];
            $order->payment_method = $request->input('payment_method');
            $order->status = 'Processing';

            $order->save();
            // dd($cart->items);
            // $customer->orders()->save($order);
            foreach ($cart->items as $items) {
                $id = $items['item']['item_id'];
                // dd($id);
                DB::table('orderline')
                    ->insert(
                        [
                            'item_id' => $id,
                            'orderinfo_id' => $order->orderinfo_id,
                            'quantity' => $items['qty']
                        ]
                    );
                $stock = Stock::find($id);
                $stock->quantity = $stock->quantity - $items['qty'];
                $stock->save();
            }
            // dd($order);
        } catch (\Exception $e) {
            // dd($e->getMessage());
            DB::rollback();
            if (Session::pull($buyNowFlagKey, false)) {
                Session::forget($cartKey);
                if (Session::has($buyNowBackupKey)) {
                    Session::put($cartKey, Session::pull($buyNowBackupKey));
                }
            }
            return redirect()->route('getCart')->with('error', $e->getMessage());
        }

        DB::commit();

        $orderLines = collect($cart->items)->map(function ($line) {
            $item = $line['item'] ?? null;

            return (object) [
                'description' => (string) data_get($item, 'description', 'Item'),
                'quantity' => (int) ($line['qty'] ?? 0),
                'sell_price' => (float) data_get($item, 'sell_price', 0),
                'img_path' => data_get($item, 'img_path'),
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

        if (Session::pull($buyNowFlagKey, false)) {
            Session::forget($cartKey);
            if (Session::has($buyNowBackupKey)) {
                Session::put($cartKey, Session::pull($buyNowBackupKey));
            }
        } else {
            Session::forget($cartKey);
            Session::forget($buyNowBackupKey);
        }
        return redirect('/')->with('success', 'Successfully Purchased Your Products!!!');
    }

    private function productCartSessionKey(): string
    {
        $userId = Auth::id();

        return self::PRODUCT_CART_KEY_PREFIX . ($userId ?: 'guest');
    }

    private function buyNowBackupSessionKey(): string
    {
        $userId = Auth::id();

        return self::BUY_NOW_BACKUP_KEY_PREFIX . ($userId ?: 'guest');
    }

    private function buyNowFlagSessionKey(): string
    {
        $userId = Auth::id();

        return self::BUY_NOW_FLAG_KEY_PREFIX . ($userId ?: 'guest');
    }

    private function buildCheckoutSummary(float $subtotal, string $discountCode = ''): array
    {
        $normalizedCode = strtoupper(trim($discountCode));
        $discountPercent = self::DISCOUNT_CODES[$normalizedCode] ?? 0;
        $discountAmount = round($subtotal * ($discountPercent / 100), 2);
        $total = max(0, $subtotal - $discountAmount);

        return [
            'subtotal' => $subtotal,
            'discountPercent' => $discountPercent,
            'discountAmount' => $discountAmount,
            'total' => $total,
            'appliedCode' => $discountPercent > 0 ? $normalizedCode : null,
        ];
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
}
