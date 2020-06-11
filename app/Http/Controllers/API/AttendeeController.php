<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Attendee;
use App\Event;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Email;

use App\Exports\AttendeeExport;
use Maatwebsite\Excel\Facades\Excel;

use File;
use ZipArchive;


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
            Email::sendPaymentProofInstructions($attendee);
        } catch(Exception $e){
            return $this->sendError('Error inputing data', $e->errors());
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
            'paymentProof' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error, please input all correct data', $validator->errors());
        }

        $event_id = $input['event_id'];
        $id = $input['id'];
        $email = $input['email'];

        $attendee = Attendee::where('event_id', $event_id)->where('id', $id)->where('email', $email)->get()->first();
        if(is_null($attendee)){
            return $this->sendError('Registration not found', 'attendee returns null from DB');
        }

        $filename = $event_id.'_'.$id.'_'.$email.'_payment.jpg';
        $path = $request->file('paymentProof')->move(public_path('/payment_proof/'.$event_id),$filename);
        $photoURL = urlencode(url('/payment_proof/'.$event_id.'/'.$filename));

        $attendee->paid = 1;
        $attendee->paymentProof = $photoURL;
        $attendee->save();

        return $this->sendResponse($attendee->toArray(), 'Proof uploaded!');
    }

    public function downloadProof(Request $request, $event_id){
        $userID = $request->user()->id;

        $checker = Event::where("event_id", $event_id)->where("eventOrganizer", $userID)->first();
        if($checker == null){
            return $this->sendError('You are not owner of this event', 'contact event owner for this data');
        }

        $zip = new ZipArchive;
   
        $fileName = $event_id.'.zip';
   
        if ($zip->open(public_path('/proof_archive/'.$fileName), ZipArchive::CREATE) === TRUE)
        {
            $files = File::files(public_path('payment_proof/'.$event_id));
   
            foreach ($files as $key => $value) {
                $relativeNameInZipFile = basename($value);
                $zip->addFile($value, $relativeNameInZipFile);
            }
             
            $zip->close();
        }
    
        return response()->download(public_path('/proof_archive/'.$fileName));
        // return $this->sendResponse('test', 'test');

    }

    public function exportCSV(Request $request){
        $userID = $request->user()->id;
        $input = $request->all();
        $validator = Validator::make($input, [
            'event_id' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error, missing event_id', $validator->errors());
        }

        $checker = Event::where("event_id", $request->event_id)->where("eventOrganizer", $userID)->first();
        if($checker == null){
            return $this->sendError('You are not owner of this event', 'contact event owner for this data');
        }

        $headings = [
            'id',
            'event_id',
            'name',
            'email',
            'paymentMethod',
            'numTickets',
            'paid',
            'cancel',
            'paymentProof',
            'createdAt',
            'updatedAt',
        ];

        return Excel::download(new AttendeeExport($request->event_id, $headings), $request->event_id.'-'.'attendees'.'.xlsx');
    }
}
