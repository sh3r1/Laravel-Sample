<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\User;

class LoginController extends BaseController
{
    public function login(){
        if(\Auth::user()->isActivated() === false)
        {
           return $this->sendError('The user is not activated, Please Contact the administrator.');
        }

        if(\Auth::check() === false)
        {
            $this->sendResponse('You are not authorized to access this resource, Please Contact the administrator.');
        }


        $user_details = User::find(\Auth::user()->id);


        return $this->sendResponse($user_details->toArray(),'Login Successfully!');
    }
}
