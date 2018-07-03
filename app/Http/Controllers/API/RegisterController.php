<?php


namespace App\Http\Controllers\API;


use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;


class RegisterController extends BaseController
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
            'position'  => 'required',
            'deviceType' => 'required',
            'deviceId' => 'required',
            'firebaseToken' => 'required'
        ]);


        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $input = [];
        $input['name'] = $request['name'];
        $input['email'] = $request['email'];
        $input['password'] = bcrypt($request['password']);
        $input['position'] = $request['position'];
        $input['deviceType'] = $request['deviceType'];
        $input['deviceId'] = $request['deviceID'];
        $input['firebaseToken'] = $request['firebaseToken'];
        $user = User::create($input);
        //$success['token'] =  $user->createToken('MyApp')->accessToken;
        $success['name'] =  $user->name;


        return $this->sendResponse($success, 'User successfully registered.');
    }
}
