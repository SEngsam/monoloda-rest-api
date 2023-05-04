<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

class VehicleController extends BaseController
{
    //
    //update certificate of vehicle registration

    public function updateVehicleRegistration(Request $request){

        dd(auth()->guard('driver-api')->user()->id);
    }

}
