<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Auth;
use App\Models\Customer;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users.profile');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'title' => 'required|min:4',
            'lname' => 'required|min:4',
            'addressline' => 'required|min:4',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $customer = Customer::create([
            'title' => trim($request->title),
            'fname' => $request->fname,
            'lname' => $request->lname,
            'addressline' => $request->addressline,
            'town' => $request->town,
            'zipcode' => $request->zipcode,
            'phone' => $request->phone,
            'user_id' => Auth::id()

        ]);



        return redirect('/')->with('success', 'profile created');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return view('users.profile');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
