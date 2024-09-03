<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use app\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class LoginController  extends Controller
{
    public function login(Request $request)
    {
        $credenciales = [
            "email"=> $request->email,
            "password"=> $request->password 
        ];

        if(Auth::attempt($credenciales))
        {
            $request->session()->regenerate();
            return redirect()->intended('orders/checkout');

        }
        else{
            return redirect('orders');
        }
    }
}
