<?php

namespace App\Http\Controllers;

use App\DataTables\ServicesDataTable;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Validator;

class ServiceController extends Controller
{
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
}
