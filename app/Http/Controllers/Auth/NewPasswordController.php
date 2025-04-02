<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use App\Helpers\ApiResponse;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordMail;

class NewPasswordController extends Controller
{
    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): JsonResponse{
        try {
            $request->validate([
                'email' => 'required|email'
            ]);

            $user = User::where('email', $request->email)->first();
            
            if (!$user) {
                return ApiResponse::error('Usuario no encontrado.', 404);
            }

            $newPassword = Str::random(8);
            $user->password = Hash::make($newPassword);
            $user->save();

            // Send email with new password
            Mail::to($user->email)->send(new ResetPasswordMail($user, $newPassword));
            return ApiResponse::success('Contraseña restablecida correctamente. Se ha enviado un correo con la nueva contraseña.', 200);
        } catch (ValidationException $e) {
            return ApiResponse::error($e->getMessage(), 422);
        } catch (\Exception $e) {
            return ApiResponse::error('An error occurred while resetting password', 500);
        }
    }
}
