<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        $this->rememberPrevious();

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($data, $request->boolean('remember'))) {
            return back()->withErrors(['email' => __('auth.failed')])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('home'));
    }

    public function showRegister()
    {
        $this->rememberPrevious();

        return view('auth.register');
    }

    public function register(Request $request)
    {
        if ($request->filled('website')) { // honeypot
            return redirect()->route('home');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:40', 'unique:users,name'],
            'email' => ['required', 'email', 'max:190', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = User::create($data);
        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->intended(route('forum.index'))->with('status', __('Добро пожаловать, :name!', ['name' => $user->name]));
    }

    protected function rememberPrevious(): void
    {
        $previous = url()->previous();
        if (! session()->has('url.intended') && str_starts_with($previous, url('/'))
            && ! str_contains($previous, '/login') && ! str_contains($previous, '/register')) {
            session()->put('url.intended', $previous);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
