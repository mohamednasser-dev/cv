<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class cv_personal_data extends Model
{
    protected $fillable = ['license','image','full_name','nationality_id', 'date_of_birth','social_status','email', 'phone','web_site', 'address','mail','user_id', 'city_id','cv_id'];

    public function Nationality() {

        return $this->belongsTo('App\Nationality', 'nationality_id')->select('id','title_'.session('api_lang').' as title');
    }
    public function Nationality_web() {

        return $this->belongsTo('App\Nationality', 'nationality_id');
    }

    public function User() {
        return $this->belongsTo('App\User', 'user_id');
    }
    public function CV() {
        return $this->belongsTo('App\cv', 'cv_id');
    }

    public function City() {
        return $this->belongsTo('App\City', 'city_id')->select('id','title_'.session('api_lang').' as title');
    }

    public function City_web() {
        return $this->belongsTo('App\City', 'city_id');
    }





}
