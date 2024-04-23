<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function verify($user_id, Request $request)
    {
        try {
            if (!$request->hasValidSignature()) {
                return response()->json(["msg" => "Invalid/Expired url provided."], 401);
            }
            $user = User::findOrFail($user_id);
            $email_verification = false;
            if (!$user->hasVerifiedEmail()) {
                $user->markEmailAsVerified();
                $email_verification = true;
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
            return response()->json(['success' => false, 'message' => $error]);
        }
        return response()->json(['success' => true, 'email_verification' => $email_verification, 'message' => 'Email Verified successfully.']);
    }
}