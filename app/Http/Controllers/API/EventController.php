<?php

namespace App\Http\Controllers\API;

use App\Event;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File; 

class EventController extends BaseController
{
    public function index(Request $request){
        $userID = $request->user()->id;
        $events = Event::where('eventOrganizer', $userID)->orderByRaw('created_at DESC')->get();
        return $this->sendResponse($events->toArray(), 'Events retrieved successfully!');
    }


    public function create(Request $request){
        $input = $request->all();
        $userID = $request->user()->id;
        $input['eventOrganizer'] = $userID;
        $input['event_id'] = uniqid();

        $validator = Validator::make($input, [
            'eventOrganizer' => 'required',
            'eventName' => 'required',
            'startDate' => 'required',
            'endDate' => 'required',
            'eventDescription' => 'required',
            'email1' => 'required',
            'phone1' => 'required',
            'picture' => 'required',
            'venue' => 'required',
            'city' => 'required',
            'event_id' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error, incomplete data', $validator->errors());
        }

        $eventName = $input['eventName'];
        $startDate = $input['startDate'];
        $endDate = $input['endDate'];

        $filename = $input['eventOrganizer'].'_'.$input['event_id'].'_'.$input['eventName'].'_event_poster.jpg';
        $path = $request->file('picture')->move(public_path('/event_posters'), $filename);
        $photoURL = url('/event_posters/'.$filename);

        $input['picture'] = urlencode($photoURL);

        $event = Event::create($input);

        return $this->sendResponse($event->toArray(), 'Event Created!');
    }

    public function show($id){
        $event = Event::where('event_id', $id)->get();

        if(is_null($event)){
            return $this->sendError('Event does not exist');
        }

        return $this->sendResponse($event->toArray(), 'Event found!');
    }

    public function update(Request $request, $id){
        $input = $request->all();
        $userID = $request->user()->id;
        $input['eventOrganizer'] = $userID;

        $validator = Validator::make($input, [
            'eventOrganizer' => 'required',
            'eventName' => 'required',
            'startDate' => 'required',
            'endDate' => 'required',
            'eventDescription' => 'required',
            'email1' => 'required',
            'phone1' => 'required',
            'picture' => 'required|file|image',
            'venue' => 'required',
            'city' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('incomplete data', $validator->errors());
        }

        $userID = $request->user()->id;
        $event = Event::where('eventOrganizer', $userID)->where('event_id', $id)->get()->first();

        if(is_null($event)){
            return $this->sendError('Event does not exist');
        }

        $oldfilename = $event['eventOrganizer'].'_'.$event['event_id'].'_'.$event['eventName'].'_event_poster.jpg';

        $event->eventOrganizer = $input['eventOrganizer'];
        $event->eventName =$input['eventName'];
        $event->startDate =$input['startDate'];
        $event->endDate =$input['endDate'];
        $event->eventDescription =$input['eventDescription'];
        $event->email1 =$input['email1'];
        if($event->$email2 != null){
            $event->email2 =$input['email2'];
            if($event->$email3 != null){
            $event->email3 =$input['email3'];
            }
        }
        $event->phone1 =$input['phone1'];
        $event->phone2 =$input['phone2'];
        $event->phone3 =$input['phone3'];
        $event->venue = $input['venue'];
        $event->city = $input['city'];
        $event->numTickets = $input['numTickets'];

        $filename = $input['eventOrganizer'].'_'.$event['event_id'].'_'.$input['eventName'].'_event_poster.jpg';
        if($filename != $oldfilename){
            File::delete(public_path('/event_posters').'/'.$oldfilename);
        }
        $path = $request->file('picture')->move(public_path('/event_posters'), $filename);

        $event->save();
        return $this->sendResponse($event->toArray(), 'Event has been updated');
    }

    public function destroy(Request $request, $id){
        $userID = $request->user()->id;
        $event = Event::where('eventOrganizer', $userID)->where('event_id', $id)->get()->first();

        if(is_null($event)){
            return $this->sendError('Event does not exist');
        }

        try {
            $event->delete();
        } catch (\Exception $e) {
        }

        return $this->sendResponse($event->toArray(), 'Event has been deleted');
    }
}
