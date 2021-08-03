<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cv_design extends Model
{
    protected $fillable = ['design_number','user_id','cv_id'];

    public function User() {
        return $this->belongsTo('App\User', 'user_id');
    }
    public function CV() {
        return $this->belongsTo('App\cv', 'cv_id');
    }
}
