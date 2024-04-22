<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Models\User;

use App\Mail\ResetPassword;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Carbon\Carbon;
use DB;
use Exception;
use Session;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */

    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);
    
        $email = $request->email;
        $emailExist = User::where('email', $email)->first();
    
        if ($emailExist == null) {
            return view('forgot-password')->with('error', 'The email does not exist.');
        } else {
            $token = rand(100000, 999999);
    
            DB::table('users')
                ->where('email', $email)
                ->update([
                    'otp' => $token,
                    'otp_expire_at' => Carbon::now()->addMinutes(15)
                ]);
    
            Mail::to($email)->send(new ResetPassword($token));
    
            return view('auth.reset-password', compact('email'));
        }
    }
    
}
