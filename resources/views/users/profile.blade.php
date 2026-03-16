@extends('layouts.base')

@section('body')
    <div class="container py-4">
        @include('layouts.flash-messages')
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-md-5">
                        <h3 class="mb-4">Update Profile</h3>

                        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="mb-4 text-center">
                                @php
                                    $photoUrl = (!empty($user->photo_path) && Storage::disk('public')->exists($user->photo_path))
                                        ? asset('storage/' . $user->photo_path)
                                        : asset('images/medstock-logo.png');
                                @endphp
                                <img src="{{ $photoUrl }}" alt="profile photo" width="110" height="110" style="object-fit: cover; border-radius: 50%; border: 2px solid #e9ecef;">
                            </div>

                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="photo" class="form-label">Profile Photo</label>
                                <input type="file" id="photo" name="photo" class="form-control @error('photo') is-invalid @enderror" accept="image/png,image/jpeg">
                                @error('photo')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                                <small class="text-muted">Optional. JPG/JPEG/PNG only, max 2MB.</small>
                            </div>

                            <h5 class="mb-3">Customer Details</h5>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="fname" class="form-label">First Name</label>
                                    <input type="text" id="fname" name="fname" class="form-control @error('fname') is-invalid @enderror"
                                        value="{{ old('fname', optional($customer)->fname) }}">
                                    @error('fname')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="lname" class="form-label">Last Name</label>
                                    <input type="text" id="lname" name="lname" class="form-control @error('lname') is-invalid @enderror"
                                        value="{{ old('lname', optional($customer)->lname) }}">
                                    @error('lname')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="addressline" class="form-label">Address</label>
                                <input type="text" id="addressline" name="addressline" class="form-control @error('addressline') is-invalid @enderror"
                                    value="{{ old('addressline', optional($customer)->addressline) }}">
                                @error('addressline')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="town" class="form-label">Town</label>
                                    <input type="text" id="town" name="town" class="form-control @error('town') is-invalid @enderror"
                                        value="{{ old('town', optional($customer)->town) }}">
                                    @error('town')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="zipcode" class="form-label">Zip Code</label>
                                    <input type="text" id="zipcode" name="zipcode" class="form-control @error('zipcode') is-invalid @enderror"
                                        value="{{ old('zipcode', optional($customer)->zipcode) }}">
                                    @error('zipcode')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="text" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                        value="{{ old('phone', optional($customer)->phone) }}">
                                    @error('phone')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">Save Profile</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
