<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attendee extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable=[
        'event_id',
        'name',
        'email',
        'paymentMethod',
        'numTickets',
        'paid',
        'cancel',
        'paymentProof',
    ];
}
