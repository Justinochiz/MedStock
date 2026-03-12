@extends('layouts.base')

@section('body')
    <div class="container py-5">
        @include('layouts.flash-messages')

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-5">
                        <h2 class="mb-4 fw-bold">Add New Service</h2>

                        <form action="{{ route('services.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="mb-4">
                                <label for="name" class="form-label fw-bold">Service Name</label>
                                <input type="text" name="name" id="name"
                                    class="form-control form-control-lg @error('name') is-invalid @enderror"
                                    value="{{ old('name') }}" required placeholder="Enter service name">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="description" class="form-label fw-bold">Description</label>
                                <textarea name="description" id="description" rows="4"
                                    class="form-control form-control-lg @error('description') is-invalid @enderror"
                                    placeholder="Describe the service">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="price" class="form-label fw-bold">Price</label>
                                <input type="number" name="price" id="price"
                                    class="form-control form-control-lg @error('price') is-invalid @enderror"
                                    min="0" step="0.01" value="{{ old('price', '0.00') }}" required>
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-5">
                                <label for="images" class="form-label fw-bold">Upload Photos</label>
                                <input type="file" name="images[]" id="images"
                                    class="form-control form-control-lg @error('images') is-invalid @enderror"
                                    accept="image/*" multiple>
                                @error('images')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @error('images.*')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">You can upload one or more photos (JPG, JPEG, PNG).</small>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg fw-bold">
                                    <i class="fas fa-plus me-2"></i>Add Service
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
