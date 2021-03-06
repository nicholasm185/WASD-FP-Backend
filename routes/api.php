<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
//Route::post('login','API\UserController@login');
//Route::post('register','API\UserController@register');

// Auth Routes *********************************************************************************************
Route::post('register', 'API\RegisterController@register');
Route::post('login','API\RegisterController@login');
Route::middleware('auth:api')->group(function() {
    Route::post('logout', 'API\RegisterController@logout');
});

Route::get('/email/resend', 'API\VerificationController@resend')->name('verification.resend');
Route::get('/email/verify/{id}/{hash}', 'API\VerificationController@verify')->name('verification.verify');
// *********************************************************************************************************


// Event routes ********************************************************************************************
Route::middleware('auth:api')->group(function() {
//    Route::post('details', 'API\UserController@details');
    Route::post('events/create', 'API\EventController@create');
    Route::post('events/update', 'API\EventController@update');
    Route::get('events/showAll', 'API\EventController@index');
    Route::post('events/update/{id}', 'API\EventController@update');
    Route::get('events/delete/{id}', 'API\EventController@destroy');
    Route::post('events/uploadImage/{id}', 'API\EventController@sendImage');
    Route::post('events/debugImage/{id}', 'API\EventController@debug_image');
});

Route::get('events/show/{id}', 'API\EventController@show');
// **********************************************************************************************************



// Attendee routes ******************************************************************************************
Route::post('attendee/register', 'API\AttendeeController@attendEvent');
Route::post('attendee/cancel', 'API\AttendeeController@cancel');
Route::post('attendee/upload', 'API\AttendeeController@uploadProof');
Route::post('attendee/instruction', 'API\Email@sendPaymentProofInstructions');

Route::middleware('auth:api')->group(function() {
    Route::post('attendee/downloadCSV', 'API\AttendeeController@exportCSV');
    Route::get('attendee/dproof/{event_id}', 'API\AttendeeController@downloadProof');
});
// **********************************************************************************************************

// Email Routes *********************************************************************************************


Route::middleware(['auth:api','isAdmin'])->group(function (){
    Route::get('/admin/amIAdmin', 'API\AdminControls@imAdmin');
    Route::get('/admin/getUsers','API\AdminControls@getUsers');
    Route::post('/admin/banUser', 'API\AdminControls@banUser');
    Route::post('/admin/unbanUser', 'API\AdminControls@unbanUser');
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
