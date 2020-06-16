<?php
// THIS API IS NOT USED, ONLY AS TEMPLATE
namespace App\Http\Controllers\API;

//use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;
use App\User;
use Illuminate\Support\Facades\Auth;

class RegisterController extends BaseController
{
    public function login(){
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){
            $user = Auth::user();
            if($user['banned'] == 1){
                return response()->json(['error'=>'Unauthorised'], 401);
            }
            $success['token'] =  $user->createToken('MyApp')-> accessToken;
            $success['name'] = $user['name'];
            $success['id'] = $user['id'];
            $success['email'] = $user['email'];
            $success['created_at'] = $user['created_at'];
            return response()->json(['success' => $success], 200);
        }
        else{
            return response()->json(['error'=>'Unauthorised'], 401);
        }
    }

    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
            'c_password' => 'required',
        ]);

        if($request->password != $request->c_password){
            return $this->sendError('Validation Error!', 'Password confirmation failed');
        }

        if ($validator->fails() || $request['password'] != $request['c_password']){
            return $this->sendError('Validation Error!', $validator->errors());
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] = $user->createToken('MyApp')->accessToken;
        $success['name'] = $user->name;
        $success['id'] = $user['id'];
        $success['email'] = $user['email'];
        $success['created_at'] = $user['created_at'];

        return $this->sendResponse($success, 'User registered');

    }

    public function logout(Request $request){
        $request->user()->token()->revoke();
        return $this->sendResponse("logged out", "you have been logged out");
    }
}
