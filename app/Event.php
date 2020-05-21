<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable=[
        'eventOrganizer', 
        'eventName', 
        'startDate', 
        'endDate', 
        'eventDescription', 
        'email1', 
        'email2', 
        'email3', 
        'phone1',
        'phone2',
        'phone3',
        'picture', 
        'venue', 
        'city'
    ];
}
