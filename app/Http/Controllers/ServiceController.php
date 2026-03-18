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
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Validator;

class ServiceController extends Controller
{
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
        $customer = Customer::where('user_id', Auth::id())->first();

        if (!$customer) {
            return redirect()->route('profile.edit')->with('error', 'Please complete your profile before purchasing a service.');
        }

        try {
            DB::beginTransaction();

            $order = new Order();
            $order->customer_id = $customer->customer_id;
            $order->customer_name = trim(($customer->fname ?? '') . ' ' . ($customer->lname ?? '')) ?: Auth::user()->name;
            $order->customer_phone = $customer->phone;
            $order->shipping_address = implode(', ', array_filter([
                $customer->addressline,
                $customer->town,
                $customer->zipcode,
            ]));
            $order->date_placed = now();
            $order->date_shipped = now()->addDays(5);
            $order->shipping = 0;
            $order->discount_code = null;
            $order->discount_amount = 0;
            $order->subtotal_amount = (float) $service->price * (int) $request->quantity;
            $order->total_amount = (float) $service->price * (int) $request->quantity;
            $order->payment_method = 'cash_on_delivery';
            $order->status = 'Processing';
            $order->save();

            DB::table('service_orderline')->insert([
                'service_id' => $service->service_id,
                'orderinfo_id' => $order->orderinfo_id,
                'quantity' => (int) $request->quantity,
            ]);

            DB::commit();

            return redirect()->route('shop.services.show', $service->service_id)
                ->with('success', 'Service purchased successfully. You can post a review after this order is recorded.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Unable to process service purchase right now. Please try again.');
        }
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
            'name' => 'required|min:3',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|mimes:jpg,png,jpeg',
            'images' => 'nullable|array',
            'images.*' => 'nullable|mimes:jpg,png,jpeg',
        ];

        $validator = Validator::make($request->all(), $rules);

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
            'name' => 'required|min:3',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|mimes:jpg,png,jpeg',
            'images' => 'nullable|array',
            'images.*' => 'nullable|mimes:jpg,png,jpeg',
        ];

        $validator = Validator::make($request->all(), $rules);

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
