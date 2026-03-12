@extends('layouts.base')

@section('body')
    <div class="container">
        <form action="{{ route('items.update', $item->item_id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="desc" class="form-label">Item Name</label>
                <input type="text" class="form-control @error('description') is-invalid @enderror" 
                       id="desc" name="description" value="{{ old('description', $item->description) }}" required>
                @error('description')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-3">
                <label for="cost" class="form-label">Cost Price</label>
                <input type="number" class="form-control @error('cost_price') is-invalid @enderror" 
                       id="cost" name="cost_price" value="{{ old('cost_price', $item->cost_price) }}" 
                       min="0.0" step="0.01" required>
                @error('cost_price')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-3">
                <label for="sell" class="form-label">Sell Price</label>
                <input type="number" class="form-control @error('sell_price') is-invalid @enderror" 
                       id="sell" name="sell_price" value="{{ old('sell_price', $item->sell_price) }}" 
                       min="0.0" step="0.01" required>
                @error('sell_price')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-3">
                <label for="qty" class="form-label">Quantity</label>
                <input type="number" class="form-control @error('quantity') is-invalid @enderror" 
                       id="qty" name="quantity" 
                       value="{{ old('quantity', empty($stock->quantity) ? 0 : $stock->quantity) }}" required>
                @error('quantity')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Upload Photos</label>
                <div id="photo-input-container">
                    <input type="file" class="form-control @error('images') is-invalid @enderror" 
                           id="images" name="images[]" accept="image/*">
                </div>
                <button type="button" id="add-photo-input" class="btn btn-outline-primary btn-sm mt-2">
                    <i class="fas fa-plus me-1"></i>Add More Photo
                </button>
                @error('images')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
                @error('images.*')
                    <span class="invalid-feedback d-block">{{ $message }}</span>
                @enderror
                <small class="text-muted d-block mt-1">New photos will be added to current gallery.</small>
            </div>

            <div class="mb-3">
                <label class="form-label">Current Photos</label>
                <div class="d-flex flex-wrap gap-2">
                    @php
                        $gallery = $item->imageGallery();
                    @endphp
                    @forelse ($gallery as $photo)
                        @php
                            $photoStoragePath = str_replace('public/', '', $photo);
                        @endphp
                        <div class="border rounded p-2" style="width: 120px;">
                            <img src="{{ asset('storage/' . $photoStoragePath) }}" alt="item photo" width="100" height="80" style="object-fit: cover; border-radius: 4px;">
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" value="{{ $photo }}" name="remove_photos[]" id="remove-photo-{{ $loop->index }}">
                                <label class="form-check-label small" for="remove-photo-{{ $loop->index }}">Delete</label>
                            </div>
                        </div>
                    @empty
                        <span class="text-muted">No photos</span>
                    @endforelse
                </div>
                <small class="text-muted d-block mt-2">Check a photo and click Update Item to delete selected photos.</small>
            </div>

            <button type="submit" class="btn btn-primary">Update Item</button>
            <a href="{{ route('items.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
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
                        '<input type="file" name="images[]" class="form-control" accept="image/*">' +
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
