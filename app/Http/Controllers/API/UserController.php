<?php

namespace App\Http\Controllers\API;


use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;


class UserController extends BaseController
{
    public function GetUsers()
    {
        if(\Auth::check() && \Auth::user()->isAdmin() && \Auth::user()->isActivated()){

            $users = User::all();
            return $this->sendResponse($users->toArray(), 'Users retrieved successfully.');

        }

        return $this->sendError('Unauthorized!');
    }

    public function DeActivateUser($id)
    {

        if(\Auth::check() && \Auth::user()->isAdmin() && \Auth::user()->isActivated()){

            $user = User::find($id);

            if (is_null($user)) {
                return $this->sendError('User not found.');
            }

            $user->status = $user->status == 0 ? 1 : 0;
            $user->save();

            return $this->sendResponse($user->toArray(), 'Users updated successfully.');

        }

        return $this->sendError('Unauthorized!');

    }

    public function SetAsAdmin($id)
    {


        if(\Auth::check() && \Auth::user()->isAdmin() && \Auth::user()->isActivated()){

            $user = User::find($id);

            if (is_null($user)) {
                return $this->sendError('User not found.');
            }

            $user->role = $user->role == 0 ? 1 : 0;
            $user->save();

            return $this->sendResponse($user->toArray(), 'Users updated successfully.');

        }

        return $this->sendError('Unauthorized!');

    }

}
