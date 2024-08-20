<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminLoginController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', 'exists:admins'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if (!Auth::guard('admin')->attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            return response()->json(['message' => 'your credentials are not correct'], 401);
        }

        $request->session()->regenerate();

        return response()->noContent();
    }

    public function destroy(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->noContent();
    }
}
