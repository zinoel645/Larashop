<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function register()
    {
        return view('auth.register');
    }

    public function store(StoreUserRequest $request)
    {
        $userData = $request->validated();
        $userData['password'] = Hash::make($userData['password']); //mã hóa mk

        User::create($userData);
        return redirect()->route('login');
    }

    public function process_login(Request $request)
    {
        $request->validate([
            'user_name' => 'required',
            'password' => 'required|min:8|regex:/[0-9]/',
        ]);

        $user = User::where('user_name', $request->input('user_name'))->first();

        if ($user && Hash::check($request->input('password'), $user->password)) {
            // Đăng nhập thành công...
            Auth::loginUsingId($user->id);
            if ($user->utype === 'ADM') {
                return redirect()->route('admin.index');
            } elseif ($user->utype === 'USR') {
                return redirect()->route('main');
            }
        }

        // Đăng nhập thất bại...
        return redirect()->route('login')->with('error', 'Tên người dùng hoặc mật khẩu không đúng');
    }




}