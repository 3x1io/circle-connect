<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return redirect()->to('/admin');
})->name('home');

Route::any('/webhook', function (\Illuminate\Http\Request $request){
    \Log::info("Incoming Call",$request->all());

    $request->validate([
        "phone" => "required",
    ]);

    broadcast(new \App\Events\IncomingCall($request->get('phone'), $request->get('user')));

    return response()->json([
        "message" => "Event dispatched"
    ]);
});

