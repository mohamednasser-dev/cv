<?php

namespace App\Http\Controllers;

use App\cv;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Helpers\APIHelpers;
use App\Balance_package;
use App\SubTwoCategory;
use App\ProductImage;
use App\Participant;
use App\SubCategory;
use Carbon\Carbon;
use App\Favorite;
use App\Category;
use App\Product;
use App\Main_ad;
use App\Ad;


class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['city_filter','balance_packages', 'gethome', 'home_page', 'delete_cv','check_ad', 'main_ad']]);
        //        --------------------------------------------- begin scheduled functions --------------------------------------------------------
            $expired = Product::where('status', 1)->whereDate('expiry_date', '<', Carbon::now())->get();
            foreach ($expired as $row) {
                $product = Product::find($row->id);
                $product->status = 2;
                $product->re_post = '0';
                $product->save();
            }
        //        --------------------------------------------- end scheduled functions --------------------------------------------------------
    }

    public function gethome(Request $request)
    {
        $data['slider'] = Ad::select('id', 'image', 'type', 'content')->where('place', 1)->get();
        $data['ads'] = Ad::select('id', 'image', 'type', 'content')->where('place', 2)->get();
        $data['categories'] = Category::select('id', 'image', 'title_ar as title')->where('deleted', 0)->get();
        $data['offers'] = Product::where('offer', 1)->where('status', 1)->where('deleted', 0)->where('publish', 'Y')->select('id', 'title', 'price', 'type')->get();
        for ($i = 0; $i < count($data['offers']); $i++) {
            $data['offers'][$i]['image'] = ProductImage::where('product_id', $data['offers'][$i]['id'])->select('image')->first()['image'];
            $user = auth()->user();
            if ($user) {
                $favorite = Favorite::where('user_id', $user->id)->where('product_id', $data['offers'][$i]['id'])->first();
                if ($favorite) {
                    $data['offers'][$i]['favorite'] = true;
                } else {
                    $data['offers'][$i]['favorite'] = false;
                }
            } else {
                $data['offers'][$i]['favorite'] = false;
            }
            // $data['offers'][$i]['favorite'] = false;

        }
        $response = APIHelpers::createApiResponse(false, 200, '', '', $data, $request->lang);
        return response()->json($response, 200);
    }

    public function home_page(Request $request){
        $data['ads'] = Ad::select('id', 'image', 'type', 'content')->where('place', 1)->inRandomOrder()->take(1)->get();
        $lang = $request->lang;
        Session::put('api_lang', $lang);
        $user = auth()->user();

        $data['cv'] = cv::select('id','design_number','created_at')->with('Personal_experience')->with('Personal_data')
                ->where('deleted','0')->where('user_id',$user->id)->orderBy('created_at', 'desc')
                ->simplePaginate(12);
        $response = APIHelpers::createApiResponse(false, 200, '', '', $data, $request->lang);
        return response()->json($response, 200);
    }
    public function delete_cv(Request $request,$id){
        $data['deleted'] = '1';
        cv::where('id',$id)->update($data);
        $response = APIHelpers::createApiResponse(false, 200, 'deleted', 'تم الحذف بنجاح', null, $request->lang);
        return response()->json($response, 200);
    }

//nasser code
    // main ad page
    public function main_ad(Request $request)
    {
        $data = Main_ad::select('image')->where('deleted', '0')->inRandomOrder()->take(1)->get();
        if (count($data) == 0) {
            $response = APIHelpers::createApiResponse(true, 406, 'no ads available',
                'لا يوجد اعلانات', null, $request->lang);
            return response()->json($response, 406);
        }
        foreach ($data as $image) {
            $image['image'] = $image->image;
        }
        $response = APIHelpers::createApiResponse(false, 200, '', '', $image, $request->lang);
        return response()->json($response, 200);
    }
//    public function home_page(Request $request)
//    {
//        $data = Main_ad::select('image')->where('deleted', '0')->inRandomOrder()->take(1)->get();
//        if (count($data) == 0) {
//            $response = APIHelpers::createApiResponse(true, 406, 'no ads available',
//                'لا يوجد اعلانات', null, $request->lang);
//            return response()->json($response, 406);
//        }
//        foreach ($data as $image) {
//            $image['image'] = $image->image;
//        }
//        $response = APIHelpers::createApiResponse(false, 200, '', '', $image, $request->lang);
//        return response()->json($response, 200);
//    }
    public function city_filter(Request $request,$area_id)
    {
        $user = auth()->user();
        $lang = $request->lang;
        Session::put('api_lang', $lang);
        $lang = $request->lang;
        $products = Product::where('status', 1)
                            ->with('Publisher')
                            ->where('publish', 'Y')
                            ->where('deleted', 0)
                            ->where('area_id', $area_id)
                            ->select('id', 'title', 'main_image as image', 'created_at', 'user_id','city_id','area_id')
                            ->orderBy('created_at', 'desc')
                            ->simplePaginate(12);
        for ($i = 0; $i < count($products); $i++) {
            if($lang == 'ar'){
                $products[$i]['address'] = $products[$i]['City']->title_ar .' , '.$products[$i]['Area']->title_ar;
            }else{
                $products[$i]['address'] = $products[$i]['City']->title_en .' , '.$products[$i]['Area']->title_en;
            }

            if ($user) {
                $favorite = Favorite::where('user_id', $user->id)->where('product_id', $products[$i]['id'])->first();
                if ($favorite) {
                    $products[$i]['favorite'] = true;
                } else {
                    $products[$i]['favorite'] = false;
                }

                $conversation = Participant::where('ad_product_id', $products[$i]['id'])->where('user_id', $user->id)->first();
                if ($conversation == null) {
                    $products[$i]['conversation_id'] = 0;
                } else {
                    $products[$i]['conversation_id'] = $conversation->conversation_id;
                }
            } else {
                $products[$i]['favorite'] = false;
                $products[$i]['conversation_id'] = 0;
            }
            $products[$i]['time'] = APIHelpers::get_month_day($products[$i]['created_at'], $lang);
        }
        $response = APIHelpers::createApiResponse(false, 200, '', '', $products, $request->lang);
        return response()->json($response, 200);
    }

    public function check_ad(Request $request)
    {
        $ads = Main_ad::select('image')->where('deleted', '0')->get();
        if (count($ads) > 0) {
            $data['show_ad'] = true;
        } else {
            $data['show_ad'] = false;
        }
        $response = APIHelpers::createApiResponse(false, 200, '', '', $data, $request->lang);
        return response()->json($response, 200);
    }

    public function balance_packages(Request $request)
    {
        if ($request->lang == 'en') {
            $data['packages'] = Balance_package::where('status', 'show')->select('id', 'name_en as title', 'price', 'amount', 'desc_en as desc')->orderBy('title', 'desc')->get();
        } else {
            $data['packages'] = Balance_package::where('status', 'show')->select('id', 'name_ar as title', 'price', 'amount', 'desc_ar as desc')->orderBy('title', 'desc')->get();
        }
        $response = APIHelpers::createApiResponse(false, 200, '', '', $data, $request->lang);
        return response()->json($response, 200);
    }
}
