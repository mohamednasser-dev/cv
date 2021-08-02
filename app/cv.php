<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class cv extends Model
{
    protected $fillable = ['design_number','user_id', 'deleted'];
}
