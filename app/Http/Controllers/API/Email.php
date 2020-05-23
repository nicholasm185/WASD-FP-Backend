<?php

namespace App\Http\Controllers\API;

use Mail;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;

class Email extends BaseController
{
    public function sendPaymentProofInstructions(Request $request)
{
    try{
        Mail::send('email', ['name' => $request->name, 'event_id' => $request->event_id, 'id' => $request->id, 'email' => $request->email, ], function ($message) use ($request)
        {
            $message->subject('Upload Payment Proof');
            $message->from('donotreply@kiddy.com', 'EzTicket');
            $message->to($request->email);
        });
        return $this->sendResponse('mail sent', 'please check your email!');
    }
    catch (Exception $e){
        return $this->sendError('Exception in sending email', $e);
    }
}
}
