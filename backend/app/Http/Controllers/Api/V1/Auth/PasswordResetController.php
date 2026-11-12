<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    /**
     * Issue password reset token for administrative account.
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $email = $request->email;
        $user = User::where('email', $email)->first();

        // Create secure reset token
        $rawToken = Str::random(64);
        $hashedToken = Hash::make($rawToken);

        // Store or update in password_reset_tokens table
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => $hashedToken,
                'created_at' => now(),
            ]
        );

        AuditService::log('PASSWORD_RESET_REQUESTED', $user, null, ['email' => $email]);

        return $this->successResponse([
            'email' => $email,
            'reset_token' => $rawToken, // Provided for direct verification / local testing
        ], 'Password recovery token generated. Please use this token to establish a new password.');
    }

    /**
     * Reset password using provided token.
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (! $record || ! Hash::check($request->token, $record->token)) {
            return $this->errorResponse('Invalid or expired password reset token.', 400);
        }

        // Check expiration (60 minutes)
        if (now()->subMinutes(60)->gt($record->created_at)) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return $this->errorResponse('Password reset token has expired. Please request a new one.', 400);
        }

        $user = User::where('email', $request->email)->first();
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Revoke tokens
        $user->tokens()->delete();

        // Remove token record
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        AuditService::log('PASSWORD_RESET_COMPLETED', $user);

        return $this->successResponse(null, 'Password has been successfully reset. Please log in with your new password.');
    }
}
