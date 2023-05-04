<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ClientResource;
use App\Http\Resources\DriverResource;
use App\Models\Client;
use App\Models\Driver;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisterController extends BaseController
{
    //

    public function driverRegister(Request $request)
    {
        dd(1);
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'firstname' => 'required',
                'lastname' => 'required',
                'phone' => 'required',
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Unauthorised.', ['error' => $validator->errors()->all()]);
            }

            $chkephone =  Driver::where('phone', $request->get('phone'))->first();

            if (!empty($chkephone)) {
                $response = "phone number is exist";
                return $this->sendError($response);
            } else {
                $data = $request->all();

                $data['password'] = Hash::make($request->password);

                $driver = Driver::create($data);

                $token = $driver->createToken('Monoloda', ['driver'])->accessToken;
                $driver = $driver->update(["api_token" => $token]);
                $response['driver'] = [
                    'user' => new DriverResource($driver),
                    'token' => $token
                ];
                DB::commit();
                $msg = 'User register successfully.';
            }
            return $this->sendResponse($response, $msg);
        } catch (Exception $e) {
            DB::rollback();
            return $this->sendError(__('auth.some_error'), $this->exMessage($e));
        }
    }

    public function clientRegister(Request $request)
    {
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'firstname' => 'required',
                'lastname' => 'required',
                'phone' => 'required',
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Unauthorised.', ['error' => $validator->errors()->all()]);
            }

            $chkephone =  Client::where('phone', $request->get('phone'))->first();

            if (!empty($chkephone)) {
                $response = "phone number is exist";
                return $this->sendError($response);
            } else {
                $data = $request->all();

                $data['password'] = Hash::make($request->password);

                $client = Client::create($data);

                $token = $client->createToken('Monoloda', ['client'])->accessToken;

                $response['Client'] = [
                    'user' => new ClientResource($client),
                    'token' => $token
                ];
                DB::commit();
                $msg = 'User register successfully.';
            }
            return $this->sendResponse($response, $msg);
        } catch (Exception $e) {
            DB::rollback();
            return $this->sendError(__('auth.some_error'), $this->exMessage($e));
        }
    }
}
