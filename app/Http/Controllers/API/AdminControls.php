<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use http\Client\Curl\User;
use Illuminate\Http\Request;

class AdminControls extends BaseController
{
    public function imAdmin(Request $request){
        $user = $request->user();
        return $this->sendResponse($user, 'you have admin privileges!');
    }

    public function test(Request $request){
        return $request->all();
    }
}
