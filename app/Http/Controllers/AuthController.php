<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Auth;

class AuthController extends Controller
{
    public function createUser(CreateUserRequest $request)
    {
        try{
            // Verificar si los campos requeridos están presentes en la solicitud
            if (!$request->filled(['name', 'email', 'password'])) {
                return response()->json([
                    'status' => false,
                    'message' => 'Please provide name, email, and password.',
                ], 400);
            }
        
            // Crear el usuario solo si los campos requeridos están presentes
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
        
            return response()->json([
                'status' => true,
                'message' => 'User created successfully',
                'token' => $user->createToken("API TOKEN")->plainTextToken,
            ], 200);
        }catch (\Exception $e){
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
    public function loginUser(LoginRequest $request){
        try{
            // la clase auth permite acceder a los datos del usuario que se encuentra logueado
            // Auth::user()->email con este ejemplo accedemos al email del usuario
            // con auth attemp tratamos de loguear a nuestro usuario
            if(!Auth::attempt($request->only(['email', 'password']))){
                return response ()->json([
                    'status' => false,
                    'message' => 'email o password no coincide con nuestros registros'
                ], 401);
            };
            $user = User::where('email', $request->email)->first();
            return response ()->json([
                'status' => true,
                'message' => 'usuario logueado succefuly',
                'token' => $user->createToken("API TOKEN")->plainTextToken,
            ],200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 500);
        }
    }
}
