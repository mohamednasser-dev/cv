<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cv_course extends Model
{
    protected $fillable = ['course_name','degree', 'collage_name','start_date','end_date','user_id','cv_id'];

    public function User() {
        return $this->belongsTo('App\User', 'user_id');
    }
    public function CV() {
        return $this->belongsTo('App\cv', 'cv_id');
    }
}
