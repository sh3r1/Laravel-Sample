<?php


namespace App\Http\Controllers\API;


use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Validator;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\Storage;

class FileAccessController extends BaseController
{
    /**
     * Display the specified resource.
     *
     * @param  string  $filename
     * @return \Illuminate\Http\Response
     */
    public function show($filename)
    {
        $storagePath = storage_path('uploads/' . $filename );
       // dd(file_exists(storage_path('uploads/' . $filename )));
        if(file_exists(storage_path('uploads/' . $filename )) === false){
            return $this->sendError('File not found');
        }

        return Image::make($storagePath)->response();
    }
}
