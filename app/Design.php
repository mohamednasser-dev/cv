<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Design extends Model
{
    protected $fillable = ['title_en','title_ar','image','type'];
}
