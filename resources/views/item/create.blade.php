@extends('layouts.base')

@section('body')
    <div class="container py-5">
        @include('layouts.flash-messages')
        
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-5">
                        <h2 class="mb-4 fw-bold">Add New Item</h2>
                        
                        <form action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="mb-4">
                                <label for="description" class="form-label fw-bold">Item Name</label>
                                <input type="text" name="description" id="description" 
                                    class="form-control form-control-lg @error('description') is-invalid @enderror" 
                                    value="{{ old('description') }}" required placeholder="Enter item name">
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="cost_price" class="form-label fw-bold">Cost Price</label>
                                <input type="number" name="cost_price" id="cost_price" 
                                    class="form-control form-control-lg @error('cost_price') is-invalid @enderror" 
                                    min="0" step="0.01" value="{{ old('cost_price', '0.00') }}" required 
                                    placeholder="0.00">
                                @error('cost_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="sell_price" class="form-label fw-bold">Selling Price</label>
                                <input type="number" name="sell_price" id="sell_price" 
                                    class="form-control form-control-lg @error('sell_price') is-invalid @enderror" 
                                    min="0" step="0.01" value="{{ old('sell_price', '0.00') }}" required 
                                    placeholder="0.00">
                                @error('sell_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="quantity" class="form-label fw-bold">Quantity</label>
                                <input type="number" name="quantity" id="quantity" 
                                    class="form-control form-control-lg @error('quantity') is-invalid @enderror" 
                                    min="0" value="{{ old('quantity', '0') }}" required 
                                    placeholder="0">
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-5">
                                <label class="form-label fw-bold">Upload Photos</label>
                                <div id="photo-input-container">
                                    <input type="file" name="images[]" id="images" 
                                        class="form-control form-control-lg @error('images') is-invalid @enderror" 
                                        accept="image/*">
                                </div>
                                <button type="button" id="add-photo-input" class="btn btn-outline-primary btn-sm mt-2">
                                    <i class="fas fa-plus me-1"></i>Add More Photo
                                </button>
                                @error('images')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @error('images.*')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="text-muted d-block mt-1">Use Add More Photo to upload multiple images (JPG, JPEG, PNG).</small>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg fw-bold">
                                    <i class="fas fa-plus me-2"></i>Add Item
                                </button>
                                <a href="{{ route('items.index') }}" class="btn btn-outline-secondary btn-lg">
                                    <i class="fas fa-arrow-left me-2"></i>Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const inputContainer = document.getElementById('photo-input-container');
                const addButton = document.getElementById('add-photo-input');

                if (!inputContainer || !addButton) {
                    return;
                }

                addButton.addEventListener('click', function () {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'input-group mt-2';
                    wrapper.innerHTML =
                        '<input type="file" name="images[]" class="form-control form-control-lg" accept="image/*">' +
                        '<button type="button" class="btn btn-outline-danger remove-photo-input" title="Remove">' +
                        '<i class="fas fa-times"></i>' +
                        '</button>';

                    inputContainer.appendChild(wrapper);
                });

                inputContainer.addEventListener('click', function (event) {
                    const removeButton = event.target.closest('.remove-photo-input');
                    if (!removeButton) {
                        return;
                    }

                    const row = removeButton.closest('.input-group');
                    if (row) {
                        row.remove();
                    }
                });
            });
        </script>
    @endpush
@endsection
