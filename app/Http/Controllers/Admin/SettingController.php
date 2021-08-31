<?php
namespace App\Http\Controllers\Admin;

//use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
//use JD\Cloudder\Facades\Cloudder;
use Cloudinary;
use Illuminate\Http\Request;
use App\Setting;

class SettingController extends AdminController{

    // get setting
    public function GetSetting(){
        $data['setting'] = Setting::find(1);
        return view('admin.setting' , ['data' => $data]);
    }

    // post setting
    public function PostSetting(Request $request){
        $setting = Setting::find(1);
        if($request->file('logo')){
            $image_name = $request->file('logo')->getRealPath();
            $logoreturned = Cloudinary::upload($image_name) ;
            $logo_id = $logoreturned->getPublicId();;
            $logo_format = $logoreturned->getExtension();;
            $logo_new_name = $logo_id.'.'.$logo_format;
            $setting->logo = $logo_new_name;
        }
        $setting->app_name_en = $request->app_name_en;
        $setting->app_name_ar = $request->app_name_ar;
        $setting->email = $request->email;
        $setting->phone = $request->phone;
        $setting->address_en = $request->address_en;
        $setting->address_ar = $request->address_ar;
        $setting->app_name_ar = $request->app_name_ar;
        $setting->facebook = $request->facebook;
        $setting->twitter = $request->twitter;
        $setting->snap_chat = $request->snap_chat;
        $setting->expier_days = $request->expier_days;

		$setting->save();
        return  back();
    }
}
