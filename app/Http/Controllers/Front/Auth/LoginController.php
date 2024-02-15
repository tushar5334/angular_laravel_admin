<?php

namespace App\Http\Controllers\Front\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Front\Auth\LoginRequest;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Where to redirect users after login.
     *
     * @var string
     */

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('frontPostLogout');
    }

    /**
     * Display Login password form
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->intended(route('front.get.dashboard'));
        } else {
            return view('front.auth.login');
        }
    }

    public function postLogin(LoginRequest $request)
    {
        $postData = $request->all();
        try {
            $remember_me  = (!empty($request->remember_me)) ? TRUE : FALSE;

            // create our user data for the authentication
            $userdata = array(
                'email' => $postData['email'],
                'password' => $postData['password'],
                //'status' => 1,
                //'user_type' => "SuperAdmin",
            );
            // attempt to do the login

            if (Auth::attempt($userdata, $remember_me)) {
                return redirect()->route('front.get.dashboard');
            } else {
                return back()->with('error', 'Email address or password is not valid.');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Something went wrong.');
        }
    }
    public function frontPostLogout()
    {
        Auth::logout();
        return redirect(route('front.get.login'));
    }
}
