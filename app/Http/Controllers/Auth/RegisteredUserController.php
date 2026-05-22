<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

use App\Http\Requests\Auth\UserRequest;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeEmail;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use App\Helpers\ApiResponse;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(UserRequest $request): JsonResponse|Response
    {
        try {
            DB::beginTransaction();
            $plainPassword = Str::random(10);
            $user = User::create([
                'username' => $request->username,
                'name' => $request->name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($plainPassword),
                'role_id' => $request->role_id,
                'is_active' => true,
            ]);
            if(!$user){
                DB::rollBack();
                return response()->json(['message' => 'Error creating user'], 500);
            }
            $employee = Employee::create([
                'user_id' => $user->id,
                'dpi' => $request->dpi,
                'nit' => $request->nit,
                'position' => $request->position,
                'salary' => $request->salary,
                'hire_date' => $request->hire_date,
                'termination_date' => null,
                'is_active' => true,
                'contract_type_id' => $request->contract_type_id,
            ]);
            if(!$employee){
                DB::rollBack();
                return response()->json(['message' => 'Error creating employee'], 500);
            }
            DB::commit();
            $this->sendEmail($user, $plainPassword);
            return ApiResponse::success(
                User::with('employee', 'role')->find($user->id),
                'Usuario creado exitosamente'
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => 'Error creating user: ' . $th->getMessage()], 500);
        }
    }

    private function sendEmail($user, $plainPassword)
    {
        $userData = [
            'name' => $user->name . ' ' . $user->last_name,
            'email' => $user->email,
            'password' => $plainPassword,
        ];
        Mail::to($user->email)->send(new WelcomeEmail($userData));
    }
}
