<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class cv extends Model
{
    protected $fillable = ['design_number','user_id', 'deleted'];

    public function Personal_experience() {
        return $this->hasOne('App\Cv_personal_experience', 'cv_id')->select('id','job_name','cv_id');
    }

    public function Personal_data() {
        return $this->hasOne('App\cv_personal_data', 'cv_id')->select('id','full_name','image','cv_id');
    }
}
