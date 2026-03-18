<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ], [
            'name.required' => 'Full name is required.',
            'name.max' => 'Full name may not be greater than 255 characters.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.max' => 'Email address may not be greater than 255 characters.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
            'photo.image' => 'Profile photo must be a valid image file.',
            'photo.mimes' => 'Profile photo must be a JPG, JPEG, or PNG file.',
            'photo.max' => 'Profile photo may not be greater than 2MB.',
        ]);
    }

    /**
     * Handle a registration request for the application.
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        $existingUser = User::where('email', $request->email)->first();

        if ($existingUser && $existingUser->hasVerifiedEmail()) {
            throw ValidationException::withMessages([
                'email' => [trans('validation.unique', ['attribute' => 'email'])],
            ]);
        }

        if ($existingUser && !$existingUser->hasVerifiedEmail()) {
            $photoPath = $existingUser->photo_path;

            if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
                if ($photoPath && Storage::disk('public')->exists($photoPath)) {
                    Storage::disk('public')->delete($photoPath);
                }

                $photoPath = $request->file('photo')->store('profile_photos', 'public');
            }

            $existingUser->name = $request->name;
            $existingUser->password = Hash::make($request->password);
            $existingUser->photo_path = $photoPath;
            $existingUser->email_verified_at = null;
            $existingUser->save();

            event(new Registered($existingUser));

            $this->guard()->login($existingUser);

            return redirect()->route('verification.notice')
                ->with('status', 'We resent your verification email. Please check your inbox.');
        }

        event(new Registered($user = $this->create($request->all())));

        $this->guard()->login($user);

        return $this->registered($request, $user)
            ?: redirect($this->redirectPath());
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $photoPath = null;

        if (request()->hasFile('photo') && request()->file('photo')->isValid()) {
            $photoPath = request()->file('photo')->store('profile_photos', 'public');
        }

        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'photo_path' => $photoPath,
        ]);
    }
}
