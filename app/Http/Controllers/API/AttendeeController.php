<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Attendee;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class AttendeeController extends BaseController
{
    public function attendEvent(Request $request){
        $input = $request->all();

        $validator = Validator::make($input, [
            'event_id' => 'required',
            'name' => 'required',
            'email' => 'required',
            'paymentMethod' => 'required',
            'numTickets' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error, please input all correct data', $validator->errors());
        }

        try{
            $attendee = Attendee::create($input);
        } catch(\Exception $e){
            return $this->sendError('Error inputing data', $e);
        }
        
        return $this->sendResponse($attendee->toArray(), 'Attendee has been registered!');
    }

    public function cancel(Request $request){
        $input = $request->all();
        $validator = Validator::make($input, [
            'id' => 'required',
            'event_id' => 'required',
            'email' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error, please input all correct data', $validator->errors());
        }

        $eventID = $input['event_id'];
        $id = $input['id'];
        $email = $input['email'];

        $attendee = Attendee::where('event_id', $eventID)->where('id', $id)->where('email', $email)->get()->first();

        if(is_null($attendee)){
            return $this->sendError('Registration not found', 'attendee returns null from DB');
        }

        if($attendee->cancel == 1){
            return $this->sendError('Registration already cancelled', 'attendee cancel = 1');
        }
        
        $attendee->cancel = 1;
        $attendee->save();

        // try{
        //     $attendee->delete();
        // } catch(\Exception $e){

        // }

        return $this->sendResponse($attendee->toArray(), 'Registration cancelled');
    }

    public function uploadProof(Request $request){
        $input = $request->all();
        $validator = Validator::make($input, [
            'id' => 'required',
            'event_id' => 'required',
            'email' => 'required',
        ]);

        $event_id = $input['event_id'];
        $id = $input['id'];
        $email = $input['email'];

        $attendee = Attendee::where('event_id', $event_id)->where('id', $id)->where('email', $email)->get()->first();
        if(is_null($attendee)){
            return $this->sendError('Registration not found', 'attendee returns null from DB');
        }

        $filename = $event_id.'_'.$id.'_'.$email.'_payment.jpg';
        $path = $request->file('paymentProof')->move(public_path('/payment_proof/'.$event_id),$filename);
        $photoURL = url('/payment_proof/'.$event_id.$filename);

        $attendee->paid = 1;
        $attendee->paymentProof = $photoURL;
        $attendee->save();

        return $this->sendResponse($attendee->toArray(), 'Proof uploaded!');
    }
}
