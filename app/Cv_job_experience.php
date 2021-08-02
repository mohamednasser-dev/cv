<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cv_job_experience extends Model
{

    protected $fillable = ['job_name','job_distination', 'start_date','end_date','user_id','cv_id'];

}
