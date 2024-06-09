<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $response = $this->default_response;

        try{

            $data = $request->validated();

            DB::BeginTransaction();

            $user = new User();
            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->password = bcrypt($data['password']);
            $user->save();

            DB::Commit();

            $response ['success'] = true;
            $response ['data'] = $user;
            $response ['message'] = 'Register Success';

        }catch (\Exception $e){

            DB::rollBack();

            $response ['message'] = $e->getMessage();
        }

        return response()->json($response);
    }
    

    public function login(LoginRequest $request)
    {
        $response = $this->default_response;

        try{

            $data = $request->validated();

            if(!Auth::attempt($data)){
                throw new Exception('Email or Password does not match');
            }

            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            $response ['success'] = true;
            $response ['message'] = 'Login Berhasil';
            $response['data'] = [
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer',
            ];


        }catch (\Exception $e){
            $response ['message'] = $e->getMessage();
        }
        return response()->json($response);
    }

    public function logout()
    {
        $response = $this-> default_response;

        try {

            $user = Auth::user();
            $user->tokens()->delete();
            
            $response['success'] = true;
            $response['message'] = 'Logout Berhasil';
            $response['data'] = [
                'user' => $user,
            ];
        } catch (\Exception $e){
            $response['message'] = $e->getMessage();
        }

        return response()->json($response);
    }
}
