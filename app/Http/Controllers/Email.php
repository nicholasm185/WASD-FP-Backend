<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Mail;
use Illuminate\Http\Request;
use App\Attendee;

class Email extends Controller
{
    public static function sendPaymentProofInstructions(Attendee $request)
    {
        try{
            Mail::send('email', ['name' => $request->name, 'event_id' => $request->event_id, 'id' => $request->id, 'email' => $request->email, ], function ($message) use ($request)
            {
                $message->subject('Upload Payment Proof');
                $message->from('donotreply@ezticket.com', 'EzTicket');
                $message->to($request->email);
            });
            return true;
        }
        catch (Exception $e){
            return false;
        }
    }
}
