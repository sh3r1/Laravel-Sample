<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'description','category_id','image_path','reported_by','status','created_at','thumbnail',
    ];

}
