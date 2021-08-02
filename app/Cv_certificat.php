<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cv_certificat extends Model
{
    protected $fillable = ['certificate_name','degree_specialization', 'collage_name','start_date','end_date','user_id','cv_id'];
}
