<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UserRequest;
use Exception;

class AuthController extends Controller
{
     public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password)
        ]);

        $token = $user->createToken('authToken')->plainTextToken;

        if($user) {
            return response()->json([
                'token_type' => 'Bearer',
                'access_token' => $token,
                'user' => $user,
                'message' => "berhasil register"
            ], 201);
        }

        return response()->json([
            'success' => false,
        ], 409);
    }

    public function login(Request $request)
    {
        try {
            // validasi input
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            // cek Credentials Login
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return response()->json(['message' => 'Email User Salah'], 400);
            }


            // jika hash tidak sesuai muncul alert
            if (!Hash::check($request->password, $user->password, [])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Password salah',
                ], 401);
            }

            // jika berhasil
            $token = $user->createToken('authToken')->plainTextToken;

            return response()->json([
                'token_type' => 'Bearer',
                'access_token' => $token,
                'user' => $user,
                'message' => "success login"
            ]);
        } catch (Exception $error) {
            return response()->json([
                'status' => false,
                'message' => $error->getMessage(),
            ],  500);
        }
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();
        return response()->json([
            'user' => $user,
            'message' => "berhasil logout"
        ]);
    }
}
