<?php

namespace App\Http\Controllers\API;

use App\Event;
use App\Http\Controllers\API\BaseController as BaseController;
//use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class EventController extends BaseController
{
    public function index(Request $request){
        $userID = $request->user()->id;
        $events = Event::where('eventOrganizer', $userID)->get();
        return $this->sendResponse($events->toArray(), 'Events retrieved successfully!');
    }


    public function create(Request $request){
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
            'picture' => 'required',
            'venue' => 'required',
            'city' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error, unauthorised', $validator->errors());
        }

        $filename = $input['eventOrganizer'].'_'.$input['startDate'].'_'.$input['endDate'].'_'.$input['eventName'].'_event_poster.jpg';
        $path = $request->file('picture')->move(public_path('/event_posters'), $filename);
        $photoURL = url('/event_posters/'.$filename);

//        return $this->sendResponse($photoURL, 'debug');

        $input['picture'] = urlencode($photoURL);
//
        $event = Event::create($input);
//
        return $this->sendResponse($event->toArray(), 'Event Created!');
    }

    public function show($id){
        // $userID = $request->user()->id;
        $event = Event::where('id', $id)->get();
        // $event = Event::where('eventOrganizer', $userID)->where('id', $id)->get()->first();

        if(is_null($event)){
            return $this->sendError('Event does not exist');
        }

        return $this->sendResponse($event->toArray(), 'Event found!');
    }

    public function update(Request $request, $id){
        $input = $request->all();
        $input['eventOrganizer'] = $userID;

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
            'city' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('incomplete data', $validator->errors());
        }

        $userID = $request->user()->id;
        $event = Event::where('eventOrganizer', $userID)->where('id', $id)->get()->first();

        if(is_null($event)){
            return $this->sendError('Event does not exist');
        }

        $event->eventOrganizer = $input['eventOrganizer'];
        $event->eventName =$input['eventName'];
        $event->startDate =$input['startDate'];
        $event->endDate =$input['endDate'];
        $event->eventDescription =$input['eventDescription'];
        $event->email1 =$input['email1'];
        $event->email2 =$input['email2'];
        $event->email3 =$input['email3'];
        $event->phone1 =$input['phone1'];
        $event->phone2 =$input['phone2'];
        $event->phone3 =$input['phone3'];
        $event->venue = $input['venue'];
        $event->city = $input['city'];

        $filename = $input['eventOrganizer'].'_'.$input['startDate'].'_'.$input['endDate'].'_'.$input['eventName'].'_event_poster.jpg';
        $path = $request->file('picture')->move(public_path('/event_posters'), $filename);

//        $event->picture = $input['picture'];

        $event->save();
        return $this->sendResponse($event->toArray(), 'Event has been updated');
    }

    public function destroy(Request $request, $id){
        $userID = $request->user()->id;
        $event = Event::where('eventOrganizer', $userID)->where('id', $id)->get()->first();

        if(is_null($event)){
            return $this->sendError('Event does not exist');
        }

        try {
            $event->delete();
        } catch (\Exception $e) {
        }

        return $this->sendResponse($event->toArray(), 'Event has been deleted');
    }

//    public function sendImage($id, Request $request){
//
//        if($this->is_image($request['image'])){
//            $event = Event::find($id);
//
//            if(is_null($event)){
//                return $this->sendError('Event not found');
//            }
//
//            $event->picture = $request['image'];
//            $event->save();
//            return $this->sendResponse($event->toArray(), 'Event has been updated');
//        }
//
//        return $this->sendError('not image');
//
//    }
//
////    Silver Moon binarytides.com
//    function is_image($path)
//    {
//        $a = getimagesize($path);
//        $image_type = $a[2];
//
//        if(in_array($image_type , array(IMAGETYPE_GIF , IMAGETYPE_JPEG ,IMAGETYPE_PNG , IMAGETYPE_BMP)))
//        {
//            return true;
//        }
//        return false;
//    }
//
//    function debug_image($id){
//        $event = Event::find($id);
//        if(is_null($event)){
//            return $this->sendError('Event not found');
//        }
//        return response()->file($event['picture']);
//    }


}
