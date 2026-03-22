@extends('layouts.base')

@section('body')
    <div class="container py-5">
        @include('layouts.flash-messages')

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-5">
                        <h2 class="mb-4 fw-bold">Edit Service</h2>

                        <form action="{{ route('services.update', $service->service_id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="mb-4">
                                <label for="name" class="form-label fw-bold">Service Name</label>
                                <input type="text" name="name" id="name"
                                    class="form-control form-control-lg @error('name') is-invalid @enderror"
                                    value="{{ old('name', $service->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="description" class="form-label fw-bold">Description</label>
                                <textarea name="description" id="description" rows="4"
                                    class="form-control form-control-lg @error('description') is-invalid @enderror">{{ old('description', $service->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="type" class="form-label fw-bold">Type (Optional)</label>
                                <input type="text" name="type" id="type"
                                    class="form-control form-control-lg @error('type') is-invalid @enderror"
                                    value="{{ old('type', $service->type ?? '') }}">
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="price" class="form-label fw-bold">Price</label>
                                <input type="number" name="price" id="price"
                                    class="form-control form-control-lg @error('price') is-invalid @enderror"
                                    min="0" step="0.01" value="{{ old('price', $service->price) }}" required>
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="images" class="form-label fw-bold">Replace Photos</label>
                                <input type="file" name="images[]" id="images"
                                    class="form-control form-control-lg @error('images') is-invalid @enderror"
                                    accept="image/*" multiple>
                                @error('images')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @error('images.*')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-5">
                                <small class="text-muted">Current photos:</small>
                                <div class="d-flex flex-wrap gap-2 mt-2">
                                    @php
                                        $gallery = $service->imageGallery();
                                    @endphp
                                    @forelse ($gallery as $photo)
                                        <img src="{{ asset('storage/' . str_replace('public/', '', $photo)) }}" alt="service photo" width="80" height="80" style="object-fit: cover; border-radius: 4px;">
                                    @empty
                                        <span class="text-muted">No image</span>
                                    @endforelse
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg fw-bold">
                                    <i class="fas fa-save me-2"></i>Update Service
                                </button>
                                <a href="{{ route('services.index') }}" class="btn btn-outline-secondary btn-lg">
                                    <i class="fas fa-arrow-left me-2"></i>Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
