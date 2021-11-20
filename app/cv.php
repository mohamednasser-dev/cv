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
        return $this->hasOne('App\cv_personal_data', 'cv_id')->select('id','full_name','image','cv_id','city_id')->with('City');
    }
    public function Personal_data_all() {
        return $this->hasOne('App\cv_personal_data', 'cv_id');
    }

    public function Certificats() {
        return $this->hasMany('App\Cv_certificat', 'cv_id');
    }

    public function Courses() {
        return $this->hasMany('App\Cv_course', 'cv_id');
    }

    public function Design() {
        return $this->hasOne('App\Cv_design', 'cv_id');
    }

    public function Hobbies() {
        return $this->hasMany('App\Cv_hobby', 'cv_id');
    }

    public function Job_experiences() {
        return $this->hasMany('App\Cv_job_experience', 'cv_id');
    }

    public function Personal_experiences() {
        return $this->hasMany('App\Cv_personal_experience', 'cv_id');
    }
}
