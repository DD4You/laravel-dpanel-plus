<?php

namespace DD4You\Dpanel\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use DD4You\Dpanel\Mail\ForgotPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        if ($request->method() === 'GET') return view('dpanel::auth.login');

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {

            if (auth()->user()->can('dpanel-access')) {

                $request->session()->regenerate();

                return redirect()->route(config('dpanel.prefix') . '.dashboard');
            }
            Auth::logout();

            $request->session()->invalidate();

            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.'
            ]);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function forgot(Request $request)
    {
        if ($request->method() === 'GET') return view('dpanel::auth.forgot');

        $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user && $user->can('dpanel-access')) {

            $token = Str::random(64);
            $user->remember_token = $token;
            $user->save();

            # Send Reset Email
            $reset_link = route(config('dpanel.prefix') . '.reset', ['token' => $token]);
            Mail::to($request->email)->send(new ForgotPassword($reset_link));

            return back()->withSuccess('Password Reset Link send Successfully.');
        }

        return back()->withError('We do not recognize your email address')->onlyInput('email');
    }

    public function reset(Request $request)
    {
        if ($request->method() === 'GET') return view('dpanel::auth.reset');

        # Validate requests 
        $request->validate([
            'token' => 'required',
            'password' => [
                'required', 'confirmed',
                Password::min(8)->mixedCase()->symbols()
            ],
        ]);

        # Update Password
        $user = User::where('remember_token', $request->token)->first();
        if ($user && $user->can('dpanel-access')) {
            $user->password = bcrypt($request->password);
            $user->remember_token = null;
            $user->save();
            return redirect()->route(config('dpanel.prefix') . '.login')->withSuccess('Password reset successfully.');
        }
        return back()->withError('Invalid Token');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route(config('dpanel.prefix') . '.login');
    }
}
