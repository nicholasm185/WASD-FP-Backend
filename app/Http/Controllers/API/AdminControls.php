<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\User;

class AdminControls extends BaseController
{
    public function imAdmin(Request $request){
        $user = $request->user();
        return $this->sendResponse($user, 'you have admin privileges!');
    }

    public function test(Request $request){
        return $request->all();
    }

    public function getUsers(){
        $users = DB::table('users')->paginate(1);
        return $users;
    }

    public function banUser(Request $request){
        $input = $request->all();
        $validator = Validator::make($input, [
            'id' => 'required',
            'email' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error, please input all correct data', $validator->errors());
        }

        $user = User::where('id', $input['id'])->where('email', $input['email'])->first();

        if(is_null($user)){
            return $this->sendError('User not found', 'User returns null from DB');
        }

        $user->banned = 1;
        $user->save();

        return $this->sendResponse($user, 'User has been banned');
    }

    public function unbanUser(Request $request){
        $input = $request->all();
        $validator = Validator::make($input, [
            'id' => 'required',
            'email' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error, please input all correct data', $validator->errors());
        }

        $user = User::where('id', $input['id'])->where('email', $input['email'])->first();

        if(is_null($user)){
            return $this->sendError('User not found', 'User returns null from DB');
        }

        $user->banned = 0;
        $user->save();

        return $this->sendResponse($user, 'User has been unbanned');
    }
}
