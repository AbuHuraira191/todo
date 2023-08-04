<?php

namespace App\Http\Middleware;

use App\Mail\VerificationEmail;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;


class CheckUserVerification
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
//        $user = Auth::user();
//
//        /** @var User $user */
//        if (!$user->is_verified) {
//            return response()->json(['error' => 'User is not verified'], 401);
//        }
//
//        return $next($request);

        try {
            // Attempt to parse the token from the request
            $token = JWTAuth::parseToken();

            // Authenticate the user with the token
            $user = $token->authenticate();

            // If the user is not found, throw an exception
            if (!$user) {
                throw new \Exception('Unauthorized : user is not found against this token', 401);
            }

            // Check if the user is verified
            if (!$user->is_verified) {
                $verification_code = Str::random(8); // Generate a verification code

                $user->update([
                    'verification_code' => $verification_code,
                ]);

                // Send verification email to the user's email
                Mail::to($user->email)->send(new VerificationEmail($user));
                return response()->json(['message' => 'User is not verified we send verification email again please verify it.'], 401);
            }
        } catch (\Exception $e) {

            // If the token is missing
            if ($e->getCode() == 0){
                return response()->json(['message' => 'Unauthorized : Token is missing or Invalid'], 401);
            }
            return response()->json(['message' => $e->getMessage()], 401);
        }

        // Pass the authenticated user along with the request
        $request->merge(['user' => $user]);

        return $next($request);
    }
}
