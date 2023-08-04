<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;
use App\Mail\VerificationEmail;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $credentials = $request->only('email', 'password');

            if (Auth::attempt($credentials)) {
                /** @var User $user */
                $user = Auth::user();

                if ($user->is_verified) {
                    $token = JWTAuth::fromUser($user);

                    return response()->json(['token' => $token]);
                } else {
                    $verification_code = Str::random(8); // Generate a verification code

                    $user->update([
                        'verification_code' => $verification_code,
                    ]);

                    // Send verification email to the user's email
                    Mail::to($user->email)->send(new VerificationEmail($user));
                    return response()->json(['error' => 'User is not verified we send verification email again please verify it.'], 401);
                }
            }

            return response()->json(['error' => 'Invalid credentials'], 401);

        }catch (\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }

    }

    public function logout()
    {
        try {
            $token = JWTAuth::getToken();
            // Check if the token is valid
            if (JWTAuth::check($token)) {
                // Invalidate the token
                JWTAuth::invalidate($token);
                return response()->json(['message' => 'Logout successful'],201);
            } else {
                return response()->json(['message' => 'Unauthorized : Token is missing or Invalid'], 401);
            }
        }catch (\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }

    }

    public function verify($code)
    {
        $user = User::where('verification_code', $code)->first();

        if (!$user) {
            return response()->json(['message' => 'Invalid verification code.'], 401);
        }

        $user->is_verified = true;
        $user->verification_code = null;
        $user->save();

        return response()->json(['message' => 'Account verified successfully. You can now log in.'], 201);
    }


    public function register(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|unique:users',
                'password' => 'required|string|min:6|confirmed',
            ]);

            $data['password'] = Hash::make($data['password']);
            $data['verification_code'] = Str::random(8); // Generate a verification code

            $user = User::create($data);

            // Send verification email to the user's email
            Mail::to($user->email)->send(new VerificationEmail($user));

            return response()->json(['message' => 'Registration successful. Please check your email for verification.'], 201);

        }catch (\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }

    }
}
