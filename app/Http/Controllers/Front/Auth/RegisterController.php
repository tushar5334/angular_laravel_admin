<?php

namespace App\Http\Controllers\Front\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Front\Auth\RegisterRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class RegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showSignupForm()
    {
        return view('front.auth.register');
    }

    public function processSignup(RegisterRequest $request)
    {
        $requestData = $request->all();
        DB::beginTransaction();
        try {
            User::create($requestData);
            DB::commit();
            return redirect()->route('front.get.login')->with('success', 'Registration has been done successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Something went wrong.');
        }
    }
}
