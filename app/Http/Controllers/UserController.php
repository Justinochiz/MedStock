<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Validator;

class UserController extends Controller
{
    public function editProfile()
    {
        /** @var User $user */
        $user = Auth::user();
        $customer = Customer::where('user_id', $user->id)->first();

        return view('users.profile', compact('user', 'customer'));
    }

    public function updateProfile(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'fname' => 'nullable|string|max:255',
            'lname' => 'nullable|string|max:255',
            'addressline' => 'nullable|string|max:255',
            'town' => 'nullable|string|max:255',
            'zipcode' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:30',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            if (!empty($user->photo_path) && Storage::disk('public')->exists($user->photo_path)) {
                Storage::disk('public')->delete($user->photo_path);
            }

            $user->photo_path = $request->file('photo')->store('profile_photos', 'public');
        }

        $user->save();

        $customerPayload = [
            'fname' => $request->fname,
            'lname' => $request->lname,
            'addressline' => $request->addressline,
            'town' => $request->town,
            'zipcode' => $request->zipcode,
            'phone' => $request->phone,
        ];

        $hasCustomerData = collect($customerPayload)->filter(function ($value) {
            return !is_null($value) && trim((string) $value) !== '';
        })->isNotEmpty();

        $existingCustomer = Customer::where('user_id', $user->id)->first();
        if ($existingCustomer || $hasCustomerData) {
            Customer::updateOrCreate(
                ['user_id' => $user->id],
                $customerPayload
            );
        }

        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully.');
    }

    public function update_role(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'role' => 'required|in:user,admin',
            'is_active' => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        // Prevent admin from deactivating their own account
        if (Auth::id() == $id && $request->is_active == '0') {
            return redirect()->back()->with('error', 'You cannot deactivate your own account.');
        }

        User::where('id', $id)->update([
            'role' => $request->role,
            'is_active' => (bool) $request->is_active,
        ]);

        return redirect()->back()->with('success', 'User updated successfully.');
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}
