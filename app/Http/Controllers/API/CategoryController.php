<?php

namespace App\Http\Controllers\API;


use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Category;
use Illuminate\Support\Facades\Auth;
use Validator;

class CategoryController extends BaseController
{
    public function index()
    {
         $categories = Category::all();
         return $this->sendResponse($categories->toArray(), 'Categories retrieved successfully.');
    }
}
