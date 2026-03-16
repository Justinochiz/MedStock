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


class ItemController extends Controller
{
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
            'description' => 'required|min:4',
            'image' => 'nullable|mimes:jpg,png,jpeg',
            'images' => 'nullable|array',
            'images.*' => 'nullable|mimes:jpg,png,jpeg'
        ];
        $validator = Validator::make($request->all(), $rules);

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

        return view('item.show', compact('item', 'stock'));
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
            'description' => 'required|min:4',
            'image' => 'nullable|mimes:jpg,png,jpeg',
            'images' => 'nullable|array',
            'images.*' => 'nullable|mimes:jpg,png,jpeg',
            'remove_photos' => 'nullable|array',
            'remove_photos.*' => 'string',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $item->description = $request->description;
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

        $oldCart = Session::has('cart') ? Session::get('cart') : null;

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

        Session::put('cart', $cart);

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

        if (Session::has('cart')) {
            Session::put('cart_backup_for_buy_now', Session::get('cart'));
        }

        $cart = new Cart(null);
        $cart->add($item, $id, $requestedQty);

        Session::put('cart', $cart);
        Session::put('is_buy_now', true);

        return redirect()->route('checkout');
    }

    public function getCart()
    {
        // dump(Session::get('cart'));
        if (!Session::has('cart')) {
            return view('shop.shopping-cart');
        }
        $oldCart = Session::get('cart');
        $cart = new Cart($oldCart);
        // dd($cart);
        return view('shop.shopping-cart', ['products' => $cart->items, 'totalPrice' => $cart->totalPrice]);
    }

    public function showCheckout(Request $request)
    {
        if (!Session::has('cart')) {
            return redirect()->route('getCart');
        }

        $customer = Customer::where('user_id', Auth::id())->first();
        if (!$customer) {
            return redirect()->route('profile.edit')->with('error', 'Please complete your profile before checkout.');
        }

        $oldCart = Session::get('cart');
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
        $oldCart = Session::has('cart') ? Session::get('cart') : null;
        $cart = new Cart($oldCart);
        $cart->reduceByOne($id);
        if (count($cart->items) > 0) {
            Session::put('cart', $cart);
        } else {
            Session::forget('cart');
        }
        return redirect()->route('getCart');
    }

    public function getRemoveItem($id)
    {
        $oldCart = Session::has('cart') ? Session::get('cart') : null;
        $cart = new Cart($oldCart);
        $cart->removeItem($id);
        if (count($cart->items) > 0) {
            Session::put('cart', $cart);
        } else {
            Session::forget('cart');
        }
        return redirect()->route('getCart');
    }

    public function postCheckout()
    {

        if (!Session::has('cart')) {
            return redirect()->route('getCart');
        }

        $request = request();
        $request->validate([
            'discount_code' => 'nullable|string|max:50',
            'payment_method' => 'required|in:cash_on_delivery,gcash,credit_card,debit_card',
        ]);

        $oldCart = Session::get('cart');
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
            $order->customer_name = $this->formatCustomerName($customer, Auth::user()->name);
            $order->customer_phone = $customer->phone;
            $order->shipping_address = $this->formatCustomerAddress($customer);
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
            if (Session::pull('is_buy_now', false)) {
                Session::forget('cart');
                if (Session::has('cart_backup_for_buy_now')) {
                    Session::put('cart', Session::pull('cart_backup_for_buy_now'));
                }
            }
            return redirect()->route('getCart')->with('error', $e->getMessage());
        }

        DB::commit();
        if (Session::pull('is_buy_now', false)) {
            Session::forget('cart');
            if (Session::has('cart_backup_for_buy_now')) {
                Session::put('cart', Session::pull('cart_backup_for_buy_now'));
            }
        } else {
            Session::forget('cart');
            Session::forget('cart_backup_for_buy_now');
        }
        return redirect('/')->with('success', 'Successfully Purchased Your Products!!!');
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
