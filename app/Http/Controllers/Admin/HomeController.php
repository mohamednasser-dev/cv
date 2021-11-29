<?php
namespace App\Http\Controllers\Admin;

use App\ContactUs;
use App\Cv_certificat;
use App\Cv_course;
use App\Cv_design;
use App\Cv_hobby;
use App\Cv_job_experience;
use App\cv_personal_data;
use App\Cv_personal_experience;
use App\Product;
use App\User;
use App\Plan;
use App\Ad;
use Illuminate\Support\Facades\Session;

class HomeController extends AdminController{

    // get all contact us messages
    public function show(){
        $data['users'] = User::count();
        $data['products'] = Product::where('deleted',0)->count();
        $data['plans'] = Plan::count();
        $data['ads'] = Ad::count();
        $data['contact_us'] = ContactUs::count();
        return view('admin.home' , ['data' => $data]);
    }

  public function test_design(){

      ini_set('max_execution_time', 300);

      Session::put('api_lang', 'ar');
      $id = 28 ;
      if($id == 0){
          $data['design'] = Cv_design::select('id','design_number')->where('user_id',116)->where('cv_id',null)->get();
          $data['personal_data'] = cv_personal_data::with('Nationality')->with('City')->where('user_id',116)->where('cv_id',null)->first();
          $data['job_experience'] = Cv_job_experience::select('id','job_name','job_distination','start_date','end_date')->where('user_id',116)->where('cv_id',null)->get();
          $data['certificat'] = Cv_certificat::select('id','certificate_name','degree_specialization','collage_name','start_date','end_date')->where('user_id',116)->where('cv_id',null)->get();
          $data['hobby'] = Cv_hobby::select('id','name')->where('user_id',116)->where('cv_id',null)->get();
          $data['personal_experience'] = Cv_personal_experience::select('id','job_name','job_distination','start_date','end_date')->where('user_id',116)->where('cv_id',null)->get();
          $data['course'] = Cv_course::select('id','course_name','degree','collage_name','start_date','end_date')->where('user_id',116)->where('cv_id',null)->get();
      }else{
          $data['design'] = Cv_design::select('id','design_number')->where('user_id',116)->where('cv_id',$id)->get();
          $data['personal_data'] = cv_personal_data::with('Nationality')->with('City')->where('user_id',116)->where('cv_id',$id)->first();
          $data['job_experience'] = Cv_job_experience::select('id','job_name','job_distination','start_date','end_date')->where('user_id',116)->where('cv_id',$id)->get();
          $data['certificat'] = Cv_certificat::select('id','certificate_name','degree_specialization','collage_name','start_date','end_date')->where('user_id',116)->where('cv_id',$id)->get();
          $data['hobby'] = Cv_hobby::select('id','name')->where('user_id',116)->where('cv_id',$id)->get();
          $data['personal_experience'] = Cv_personal_experience::select('id','job_name','job_distination','start_date','end_date')->where('user_id',116)->where('cv_id',$id)->get();
          $data['course'] = Cv_course::select('id','course_name','degree','collage_name','start_date','end_date')->where('user_id',116)->where('cv_id',$id)->get();
      }

        return view('webview.app_cv_design.test_print',compact('data'));
//        return view('webview.app_cv_design.first_design',compact('data'));
    }

}
