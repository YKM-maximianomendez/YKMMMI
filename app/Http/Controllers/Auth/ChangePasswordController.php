<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ChangePasswordController extends Controller
{
    public function showChangePasswordForm()
    {
        return view('auth.passwords.change');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed'
        ]);

        $user = Auth::user();
        $user->password = Hash::make($request->password);
        $user->password_change_required = false;
        $user->password_change_datetime = now()->toDateTimeString();
        $user->save();

        return redirect()->route('home');
    }
}
