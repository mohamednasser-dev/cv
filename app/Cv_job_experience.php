<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cv_job_experience extends Model
{

    protected $fillable = ['job_name','job_distination','job_description', 'start_date','end_date','user_id','cv_id'];

    public function User() {
        return $this->belongsTo('App\User', 'user_id');
    }
    public function CV() {
        return $this->belongsTo('App\cv', 'cv_id');
    }

}
