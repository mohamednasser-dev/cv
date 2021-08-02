<?php

namespace App\Http\Controllers;

use App\Cv_job_experience;
use App\cv_personal_data;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use JD\Cloudder\Facades\Cloudder;
use App\Category_option_value;
use Illuminate\Http\Request;
use App\Helpers\APIHelpers;
use App\SubThreeCategory;
use App\Product_feature;
use App\SubFiveCategory;
use App\SubFourCategory;
use App\Product_comment;
use App\Product_report;
use App\User_specialty;
use App\SubTwoCategory;
use App\Plan_details;
use App\Product_view;
use App\ProductImage;
use App\SubCategory;
use App\Participant;
use App\Nationality;
use Carbon\Carbon;
use App\Category;
use App\Favorite;
use App\Setting;
use App\Product;
use App\User;
use App\Plan;
use App\City;
use App\Area;
use App\cv;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['get_job_experience','save_job_experience','save_design','select_my_ads', 'all_comments', 'make_comment', 'make_report', 'ad_owner_info', 'current_ads', 'ended_ads', 'max_min_price', 'filter', 'offer_ads', 'republish_ad',
            'areas', 'cities', 'third_step_excute_pay', 'save_third_step_with_money', 'update_ad', 'select_ad_data', 'delete_my_ad',
            'save_third_step', 'save_second_step', 'save_first_step', 'getdetails', 'last_seen', 'getoffers', 'getproducts','map_ads', 'getsearch', 'getFeatureOffers']]);
    }


    public function create(Request $request)
    {
        $user = auth()->user();
        if ($user->active == 0) {
            $response = APIHelpers::createApiResponse(true, 406, 'تم حظر حسابك', null);
            return response()->json($response, 406);
        }

        if ($user->free_ads_count == 0 && $user->paid_ads_count == 0) {
            $response = APIHelpers::createApiResponse(true, 406, 'ليس لديك رصيد إعلانات لإضافه إعلان جديد يرجي شراء باقه إعلانات', null);
            return response()->json($response, 406);
        }

        $validator = Validator::make($request->all(), [
            'category_id' => 'required',
            "type" => "required",
            "title" => "required",
            "description" => "required",
            "price" => "required",
            "image" => "required"
        ]);

        if ($validator->fails()) {
            $response = APIHelpers::createApiResponse(true, 406, $validator->errors()->first(), $validator->errors()->first());
            return response()->json($response, 406);
        }

        if ($user->free_ads_count > 0) {
            $count = $user->free_ads_count;
            $user->free_ads_count = $count - 1;
        } else {
            $count = $user->paid_ads_count;
            $user->paid_ads_count = $count - 1;
        }

        $user->save();

        $ad_period = Setting::find(1)['ad_period'];

        $product = new Product();
        $product->title = $request->title;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->category_id = $request->category_id;
        $product->type = $request->type;
        $product->user_id = $user->id;
        $product->publication_date = date("Y-m-d H:i:s");
        $product->expiry_date = date('Y-m-d H:i:s', strtotime('+' . $ad_period . ' days'));

        $product->save();

        $image = $request->image;
        Cloudder::upload("data:image/jpeg;base64," . $image, null);
        $imagereturned = Cloudder::getResult();
        $image_id = $imagereturned['public_id'];
        $image_format = $imagereturned['format'];
        $image_new_name = $image_id . '.' . $image_format;
        $product_image = new ProductImage();
        $product_image->image = $image_new_name;
        $product_image->product_id = $product->id;
        $product_image->save();

        $product->image = $image_new_name;

        $response = APIHelpers::createApiResponse(false, 200, '', $product);
        return response()->json($response, 200);
    }

    public function uploadimages(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
            'image' => 'required'
        ]);

        if ($validator->fails()) {
            $response = APIHelpers::createApiResponse(true, 406, $validator->errors()->first(), $validator->errors()->first());
            return response()->json($response, 406);
        }

        $image = $request->image;
        Cloudder::upload("data:image/jpeg;base64," . $image, null);
        $imagereturned = Cloudder::getResult();
        $image_id = $imagereturned['public_id'];
        $image_format = $imagereturned['format'];
        $image_new_name = $image_id . '.' . $image_format;
        $product_image = new ProductImage();
        $product_image->image = $image_new_name;
        $product_image->product_id = $request->product_id;
        $product_image->save();
        $response = APIHelpers::createApiResponse(false, 200, '', $product_image);
        return response()->json($response, 200);
    }

    public function all_comments(Request $request, $id)
    {
        $comments = Product_comment::with('User')
            ->select('id', 'user_id', 'comment')
            ->where('product_id', $id)
            ->where('status', 'accepted')
            ->orderBy('created_at', 'desc')
            ->get();
        $response = APIHelpers::createApiResponse(false, 200, '', '', $comments, $request->lang);
        return response()->json($response, 200);
    }

    public function getdetails(Request $request){
        $user = auth()->user();
        $lang = $request->lang;
        Session::put('api_lang', $lang);
        Session::put('lang', $lang);
        $data = Product::with('Product_user')->with('Area_name')
            ->select('id', 'title', 'main_image', 'description', 'price', 'type', 'publication_date as date', 'user_id', 'category_id', 'latitude', 'longitude', 'share_location', 'area_id')
            ->find($request->id);
        $data->price = number_format((float)($data->price), 3);
        $data->comments_count = Product_comment::where('status', 'accepted')
            ->where('product_id', $request->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->count();

        if ($data->share_location == '0') {
            $data->share_location = 0;
        } else {
            $data->share_location = 1;
        }
        $user_ip_address = $request->ip();
        if ($user == null) {
            $prod_view = Product_view::where('ip', $user_ip_address)->where('product_id', $data->id)->first();
            if ($prod_view == null) {
                $data_view['ip'] = $user_ip_address;
                $data_view['product_id'] = $data->id;
                Product_view::create($data_view);
            }
        } else {
            $prod_view = Product_view::where('ip', $user_ip_address)->where('product_id', $data->id)->first();
            if ($prod_view == null) {
                $data_view['user_id'] = $user->id;
                $data_view['ip'] = $user_ip_address;
                $data_view['product_id'] = $data->id;
                Product_view::create($data_view);
            } else {
                $prod_view->user_id = $user->id;
                $prod_view->save();
            }
        }
        $features = Product_feature::where('product_id', $request->id)
            ->select('id', 'type', 'product_id', 'target_id', 'option_id')
            ->get();
        $feature_data = null;
        foreach ($features as $key => $feature) {
            $feature_data[$key]['image'] = $feature->Option->image;
            if ($feature->type == 'manual') {
                $feature_data[$key]['title'] = $feature->Option->title;
                $feature_data[$key]['value'] = $feature->target_id;
            } else if ($feature->type == 'option') {
                $feature_data[$key]['title'] = $feature->Option->title;
                $feature_data[$key]['value'] = $feature->Option_value->value;
            }
        }
        if ($user) {
            $favorite = Favorite::where('user_id', $user->id)->where('product_id', $data->id)->first();
            if ($favorite) {
                $data->favorite = true;
            } else {
                $data->favorite = false;
            }
            $conversation = Participant::where('ad_product_id', $data->id)->where('user_id', $user->id)->first();
            if ($conversation == null) {
                $data->conversation_id = 0;
            } else {
                $data->conversation_id = $conversation->conversation_id;
            }

        } else {
            $data->favorite = false;
            $data->conversation_id = 0;
        }
        $date = date_create($data->date);
        $data->date = date_format($date, 'd M Y');
        $data->time = date_format($date, 'g:i a');


        //to get ad images in array
        $images = ProductImage::where('product_id', $data->id)->pluck('image')->toArray();
        $images[count($images)] = $data->main_image;
        $data->images = $images;

        $comments = Product_comment::with('User')->select('id', 'user_id', 'comment')->where('product_id', $data->id)->where('status', 'accepted')->orderBy('created_at', 'desc')->limit(3)->get();


        $related = Product::where('category_id', $data->category_id)
            ->where('id', '!=', $data->id)
            ->where('status', 1)
            ->where('publish', 'Y')
            ->where('deleted', 0)
            ->with('Publisher')
            ->select('id', 'title', 'main_image as image', 'created_at', 'user_id', 'city_id', 'area_id')
            ->limit(3)
            ->get()->makeHidden(['City', 'Area'])
            ->map(function ($ads) use ($lang, $user) {
                if ($lang == 'ar') {
                    $ads->address = $ads->City->title_ar . ' , ' . $ads->Area->title_ar;
                } else {
                    $ads->address = $ads->City->title_en . ' , ' . $ads->Area->title_en;
                }
                if ($user) {
                    $favorite = Favorite::where('user_id', $user->id)->where('product_id', $ads->id)->first();
                    if ($favorite) {
                        $ads->favorite = true;
                    } else {
                        $ads->favorite = false;
                    }
                    $conversation = Participant::where('ad_product_id', $ads->id)->where('user_id', $user->id)->first();
                    if ($conversation == null) {
                        $ads->conversation_id = 0;
                    } else {
                        $ads->conversation_id = $conversation->conversation_id;
                    }
                } else {
                    $ads->favorite = false;
                    $ads->conversation_id = 0;
                }

                return $ads;
            });

        $response = APIHelpers::createApiResponse(false, 200, '', '', array('product' => $data,
            'features' => $feature_data, 'comments' => $comments, 'related' => $related), $request->lang);
        return response()->json($response, 200);
    }

    public function getoffers(Request $request)
    {
        $products = Product::where('offer', 1)->select('id', 'title', 'price', 'type', 'publication_date as date')->orderBy('publication_date', 'DESC')->where('status', 1)->where('deleted', 0)->where('publish', 'Y')->simplePaginate(12);
        for ($i = 0; $i < count($products); $i++) {
            $date = date_create($products[$i]['date']);
            $products[$i]['date'] = date_format($date, 'd M Y');
            $products[$i]['image'] = ProductImage::where('product_id', $products[$i]['id'])->select('image')->first()['image'];
            $user = auth()->user();
            if ($user) {
                $favorite = Favorite::where('user_id', $user->id)->where('product_id', $products[$i]['id'])->first();
                if ($favorite) {
                    $products[$i]['favorite'] = true;
                } else {
                    $products[$i]['favorite'] = false;
                }
            } else {
                $products[$i]['favorite'] = false;
            }
        }
        $response = APIHelpers::createApiResponse(false, 200, '', '', $products, $request->lang);
        return response()->json($response, 200);
    }

    public function getproducts(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required'
        ]);

        if ($validator->fails()) {
            $response = APIHelpers::createApiResponse(true, 406, $validator->errors()->first(), $validator->errors()->first(), null, $request->lang);
            return response()->json($response, 406);
        }

        if ($request->type) {
            $type = [$request->type];
        } else {
            $type = [1, 2];
        }

        $products = Product::where('status', 1)->where('deleted', 0)->where('publish', 'Y')->whereIn('type', $type)->where('category_id', $request->category_id)->select('id', 'title', 'price', 'type', 'publication_date as date')->orderBy('publication_date', 'DESC')->simplePaginate(12);

        for ($i = 0; $i < count($products); $i++) {
            $date = date_create($products[$i]['date']);
            $products[$i]['date'] = date_format($date, 'd M Y');
            $products[$i]['image'] = ProductImage::where('product_id', $products[$i]['id'])->first()['image'];
            $user = auth()->user();
            if ($user) {
                $favorite = Favorite::where('user_id', $user->id)->where('product_id', $products[$i]['id'])->first();
                if ($favorite) {
                    $products[$i]['favorite'] = true;
                } else {
                    $products[$i]['favorite'] = false;
                }
            } else {
                $products[$i]['favorite'] = false;
            }
        }
        $response = APIHelpers::createApiResponse(false, 200, '', '', $products, $request->lang);
        return response()->json($response, 200);

    }

    public function ad_owner_info(Request $request, $id)
    {
        $user = auth()->user();
        $lang = $request->lang;
        Session::put('api_lang', $lang);
        $basic_info = User::with('Account_type')->select('id', 'name', 'cover', 'email', 'image','account_type', 'phone', 'city_id', 'area_id', 'about_user', 'created_at as join_date')->where('id', $id)->first()->makeHidden(['city_id', 'area_id', 'City', 'Area']);

        if ($basic_info->city_id != null && $basic_info->area_id != null) {
            $basic_info->address = $basic_info->City->title_ar . ' , ' . $basic_info->Area->title_ar;
        } else {
            $basic_info->address = "";
        }
        $spec = "";
        if($basic_info->Account_type->type == 'commercial'){
            $user_specialties = User_specialty::with('Specialty')
                ->select('special_id')
                ->where('user_id', $id)->get();

            foreach ($user_specialties as $row) {
                if ($lang == 'ar') {
                    $spec = $spec . ',' . $row->Specialty->name;
                } else {
                    $spec = $spec . ',' . $row->Specialty->name;
                }
            }
            $basic_info->specialties = $spec;
        }else{
            $basic_info->specialties = "";
        }
        $basic_info->current_ads_count = Product::where('user_id', $id)
            ->where('status', 1)
            ->where('publish', 'Y')
            ->where('deleted', 0)
            ->get()->count();

        $products = Product::select('id', 'title', 'main_image as image', 'created_at', 'user_id','city_id','area_id')
            ->where('user_id', $id)
            ->where('status', 1)
            ->where('publish', 'Y')
            ->where('deleted', 0)
            ->orderBy('created_at', 'desc')
            ->simplePaginate(12);
//        ->makeHidden(['city_id','area_id','City','Area'])
        for ($i = 0; $i < count($products); $i++) {
           if($products[$i]['City'] != null &&$products[$i]['Area'] != null  ){
               if ($lang == 'ar') {
                   $products[$i]['address'] = $products[$i]['City']->title_ar . ' , ' . $products[$i]['Area']->title_ar;
               } else {
                   $products[$i]['address'] = $products[$i]['City']->title_en . ' , ' . $products[$i]['Area']->title_en;
               }
           }else{
               $products[$i]['address'] = "";
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
        }

        $response = APIHelpers::createApiResponse(false, 200, '', '', array('basic_info'=>$basic_info ,'ads'=>$products), $request->lang);
        return response()->json($response, 200);

    }

    public function getsearch(Request $request)
    {
        $lang = $request->lang;
        $validator = Validator::make($request->all(), [
            'search' => 'required'
        ]);
        $search = $request->search;
        $products = Product::where('publish', 'Y')
            ->where('deleted', 0)
            ->where('status', 1)
            ->select('id', 'title', 'price', 'main_image as image', 'created_at', 'pin')
            ->Where(function ($query) use ($search) {
                $query->Where('title', 'like', '%' . $search . '%');
            })
            ->orderBy('pin', 'desc')
            ->orderBy('created_at', 'desc')
            ->simplePaginate(12);
        for ($i = 0; $i < count($products); $i++) {
            $views = Product_view::where('product_id', $products[$i]['id'])->get()->count();
            $products[$i]['views'] = $views;
            $user = auth()->user();
            if ($user) {
                $favorite = Favorite::where('user_id', $user->id)->where('product_id', $products[$i]['id'])->first();
                if ($favorite) {
                    $products[$i]['favorite'] = true;
                } else {
                    $products[$i]['favorite'] = false;
                }
            } else {
                $products[$i]['favorite'] = false;
            }
            $products[$i]['time'] = APIHelpers::get_month_day($products[$i]['created_at'], $lang);
        }
        $response = APIHelpers::createApiResponse(false, 200, '', '', $products, $request->lang);
        return response()->json($response, 200);

    }

    //to get map ads establish
    public function map_ads(Request $request)
    {
        $lang = $request->lang;
        Session::put('api_lang', $lang);
        $products = Product::where('publish', 'Y')->with('Publisher')->with('Sub_category')
            ->where('deleted', 0)
            ->where('status', 1)
            ->where('category_id',7)
            ->select('id', 'title', 'price','sub_category_id', 'main_image as image', 'created_at', 'pin','city_id','area_id', 'user_id','latitude','longitude')
            ->orderBy('pin', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()->makeHidden(['City','Area']);
        for ($i = 0; $i < count($products); $i++) {
            if($lang == 'ar'){
                $products[$i]['address'] = $products[$i]['City']->title_ar .' , '.$products[$i]['Area']->title_ar;
            }else{
                $products[$i]['address'] = $products[$i]['City']->title_en .' , '.$products[$i]['Area']->title_en;
            }
            $views = Product_view::where('product_id', $products[$i]['id'])->get()->count();
            $products[$i]['views'] = $views;
            $user = auth()->user();
            if ($user) {
                $favorite = Favorite::where('user_id', $user->id)->where('product_id', $products[$i]['id'])->first();
                if ($favorite) {
                    $products[$i]['favorite'] = true;
                } else {
                    $products[$i]['favorite'] = false;
                }
            } else {
                $products[$i]['favorite'] = false;
            }
            $products[$i]['time'] = APIHelpers::get_month_day($products[$i]['created_at'], $lang);
        }
        $response = APIHelpers::createApiResponse(false, 200, '', '', $products, $request->lang);
        return response()->json($response, 200);

    }

    public function filter(Request $request)
    {
        $lang = $request->lang;
        $result = Product::query();
        $result = $result->where('publish', 'Y')
            ->where('status', 1)
            ->where('deleted', 0);
        if ($request->from_price != null && $request->to_price != null) {
            $result = $result->whereRaw('price BETWEEN ' . $request->from_price . ' AND ' . $request->to_price . '');
        }
        if ($request->area_id != 0) {
            $result = $result->where('area_id', $request->area_id);
        }
        if ($request->category_id != null) {
            $result = $result->where('category_id', $request->category_id);
        }
        if ($request->sub_category_level1_id != null) {
            $result = $result->where('sub_category_id', $request->sub_category_level1_id);
        }
        if ($request->sub_category_level2_id != null) {
            $result = $result->where('sub_category_two_id', $request->sub_category_level2_id);
        }
        if ($request->sub_category_level3_id != null) {
            $result = $result->where('sub_category_three_id', $request->sub_category_level3_id);
        }
        if ($request->sub_category_level4_id != null) {
            $result = $result->where('sub_category_four_id', $request->sub_category_level4_id);
        }
        if ($request->sub_category_level5_id != null) {
            $result = $result->where('sub_category_five_id', $request->sub_category_level5_id);
        }
        if ($request->options != null) {
            $product_ids[] = null;
            foreach ($request->options as $key => $row) {
                $product_ids = Product_feature::where('option_id', $row['option_id'])->where('target_id', $row['option_value'])->pluck('product_id')->toArray();
            }
            $result = $result->whereIn('id', $product_ids);
        }
        $products = $result->select('id', 'title', 'price', 'main_image as image', 'pin', 'created_at')
            ->orderBy('pin', 'desc')
            ->orderBy('created_at', 'desc')
            ->simplePaginate(12);
        for ($i = 0; $i < count($products); $i++) {
            $products[$i]['price'] = number_format((float)($products[$i]['price']), 3);
            $views = Product_view::where('product_id', $products[$i]['id'])->get()->count();
            $products[$i]['views'] = $views;
            $user = auth()->user();
            if ($user) {
                $favorite = Favorite::where('user_id', $user->id)->where('product_id', $products[$i]['id'])->first();
                if ($favorite) {
                    $products[$i]['favorite'] = true;
                } else {
                    $products[$i]['favorite'] = false;
                }
            } else {
                $products[$i]['favorite'] = false;
            }
            $products[$i]['time'] = APIHelpers::get_month_day($products[$i]['created_at'], $request->lang);
        }
        $data['products'] = $products;
        $response = APIHelpers::createApiResponse(false, 200, '', '', $products, $request->lang);
        return response()->json($response, 200);
    }

    public function max_min_price(Request $request)
    {
        $result = Product::query();
        $result = $result->where('publish', 'Y')
            ->where('status', 1)
            ->where('deleted', 0)
            ->where('price', '!=', null)
            ->where('category_id', $request->category_id)
            ->orderBy('price', 'asc');

        if ($request->category_id != null) {
            $result = $result->where('category_id', $request->category_id);
        }


        if ($request->sub_category_level1_id != null) {
            $result = $result->where('sub_category_id', $request->sub_category_level1_id);
        }
        if ($request->sub_category_level2_id != null) {
            $result = $result->where('sub_category_two_id', $request->sub_category_level2_id);
        }
        if ($request->sub_category_level3_id != null) {
            $result = $result->where('sub_category_three_id', $request->sub_category_level3_id);
        }
        if ($request->sub_category_level4_id != null) {
            $result = $result->where('sub_category_four_id', $request->sub_category_level4_id);
        }
        if ($request->sub_category_level5_id != null) {
            $result = $result->where('sub_category_five_id', $request->sub_category_level5_id);
        }
        $result = $result->get();


        $data['max'] = $result->last()->price;
        $data['min'] = $result->first()->price;

//        $null_price = Product::where('publish', 'Y')->where('status', 1)->where('deleted', 0)->where('price', null)->get();
//        if (count($null_price) > 0) {
//            $data['min'] = "0";
//        }
        $response = APIHelpers::createApiResponse(false, 200, '', '', $data, $request->lang);
        return response()->json($response, 200);
    }

    public function getFeatureOffers(Request $request)
    {
        $products = Product::where('feature', 1)
            ->select('id', 'title', 'price')
            ->orderBy('publication_date', 'DESC')
            ->where('status', 1)
            ->where('deleted', 0)
            ->where('publish', 'Y')
            ->simplePaginate(12);
        for ($i = 0; $i < count($products); $i++) {
            $products[$i]['image'] = ProductImage::where('product_id', $products[$i]['id'])->select('image')->first()['image'];
        }
        $response = APIHelpers::createApiResponse(false, 200, '', '', $products, $request->lang);
        return response()->json($response, 200);
    }
    //nasser code
    //to create ad you need 3 steps
    public function save_first_step(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'design_number' => 'required'
        ]);
        if ($validator->fails()) {
            $response = APIHelpers::createApiResponse(true, 406, $validator->messages()->first(), $validator->messages()->first(), null, $request->lang);
            return response()->json($response, 406);
        } else {
            $user = auth()->user();
            if ($user != null) {
                $product = new cv();
                $product->design_number = $request->design_number;
                $product->user_id = $user->id;
                $product->save();
                $response = APIHelpers::createApiResponse(false, 200, 'The design has been selected successfully', 'تم اختيار التصميم بنجاح', null, $request->lang);
                return response()->json($response, 200);
            } else {
                $response = APIHelpers::createApiResponse(true, 406, '', 'يجب تسجيل الدخول اولا', null, $request->lang);
                return response()->json($response, 406);
            }
        }
    }

    public function save_personal_data(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'image' => '',
            'full_name' => '',
            'nationality_id' => '',
            'date_of_birth' => '',
            'social_status' => '',
            'email' => '',
            'phone' => '',
            'web_site' => '',
            'address' => '',
            'mail' => '',
            'city_id' => '',
            'cv_id' => '',
        ]);
        if ($validator->fails()) {
            $response = APIHelpers::createApiResponse(true, 406, $validator->messages()->first(), $validator->messages()->first(), null, $request->lang);
            return response()->json($response, 406);
        } else {
            $user = auth()->user();
            if ($user != null) {
                $input['user_id'] = $user->id;
                if ($request->image != null) {
                    $image = $request->image;
                    Cloudder::upload("data:image/jpeg;base64," . $image, null);
                    $imagereturned = Cloudder::getResult();
                    $image_id = $imagereturned['public_id'];
                    $image_format = $imagereturned['format'];
                    $image_new_name = $image_id . '.' . $image_format;
                    $input['image'] = $image_new_name ;
                }
                cv_personal_data::create($input);
                $response = APIHelpers::createApiResponse(false, 200, 'The design has been selected successfully', 'تم حفظ البيانات الشخصية بنجاح', null, $request->lang);
                return response()->json($response, 200);
            } else {
                $response = APIHelpers::createApiResponse(true, 406, '', 'يجب تسجيل الدخول اولا', null, $request->lang);
                return response()->json($response, 406);
            }
        }
    }

    public function save_job_experience(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'job_name' => 'required',
            'job_distination' => 'required',
            'start_date' => 'required',
            'end_date' => 'required'
        ]);
        if ($validator->fails()) {
            $response = APIHelpers::createApiResponse(true, 406, $validator->messages()->first(), $validator->messages()->first(), null, $request->lang);
            return response()->json($response, 406);
        } else {
            $user = auth()->user();
            if ($user != null) {
                $input['user_id'] = $user->id;
                Cv_job_experience::create($input);
                $response = APIHelpers::createApiResponse(false, 200, 'Job experience saved successfully', 'تم حفظ الخبرة الوظيفية بنجاح', null, $request->lang);
                return response()->json($response, 200);
            } else {
                $response = APIHelpers::createApiResponse(true, 406, '', 'يجب تسجيل الدخول اولا', null, $request->lang);
                return response()->json($response, 406);
            }
        }
    }


    public function save_second_step(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'ad_id' => 'required|exists:products,id',
            'main_image' => 'required',
            'images' => 'required',
        ]);
        if ($validator->fails()) {
            $response = APIHelpers::createApiResponse(true, 406, $validator->messages()->first(), $validator->messages()->first(), $validator->messages()->first(), $request->lang);
            return response()->json($response, 406);
        } else {
            if (auth()->user() != null) {
                $image = $request->main_image;
                Cloudder::upload("data:image/jpeg;base64," . $image, null);
                $imagereturned = Cloudder::getResult();
                $image_id = $imagereturned['public_id'];
                $image_format = $imagereturned['format'];
                $image_new_name = $image_id . '.' . $image_format;
                $product = Product::where('id', $request->ad_id)->first();
                $product->main_image = $image_new_name;
                $product->save();

                foreach ($request->images as $image) {
                    Cloudder::upload("data:image/jpeg;base64," . $image, null);
                    $imagereturned = Cloudder::getResult();
                    $image_id = $imagereturned['public_id'];
                    $image_format = $imagereturned['format'];
                    $image_name = $image_id . '.' . $image_format;

                    $data['product_id'] = $request->ad_id;
                    $data['image'] = $image_name;
                    ProductImage::create($data);
                }
                $response = APIHelpers::createApiResponse(false, 200, 'image saved successfully', 'تم حفظ الصور بنجاح', null, $request->lang);
                return response()->json($response, 200);
            } else {
                $response = APIHelpers::createApiResponse(true, 406, '', 'يجب تسجيل الدخول اولا', null, $request->lang);
                return response()->json($response, 406);
            }
        }
    }

    public function save_third_step(Request $request, $ad_id, $plan_id)
    {
        //when user have enghe money in wallet
        if (auth()->user() != null) {
            $user = User::where('id', auth()->user()->id)->first();
            $selected_plan = Plan::where('id', $plan_id)->first();
            $plan_ads_number = $selected_plan->ads_count;
            $plan_price = $selected_plan->price;
            if ($plan_price <= $user->my_wallet) {
                //to select expire days of selected plane
                $plan_detail = Plan_details::where('plan_id', $plan_id)->where('type', 'expier_num')->first();
                $expire_days = $plan_detail->expire_days;

                $user->my_wallet = $user->my_wallet - $plan_price;
                if ($user->free_balance >= $plan_price) {
                    $user->free_balance = $user->free_balance - $plan_price;
                } else if ($user->payed_balance >= $plan_price) {
                    $user->payed_balance = $user->payed_balance - $plan_price;
                } else {
                    $free_balance = $user->free_balance;  //70
                    $payed_balance = $user->payed_balance;  //30
                    $price = $plan_price; //100
                    $after_min_free = $price - $free_balance;  // 100 - 70 = 30
                    if ($after_min_free <= $payed_balance && $after_min_free > 0) {
                        // 30 <= 30 && 30 < 0
                        $user->free_balance = 0;
                        $user->payed_balance = $user->payed_balance - $after_min_free;
                    } else if ($after_min_free > $payed_balance && $after_min_free > 0) {
                        $after_min_payed = $price - $payed_balance;
                        $user->free_balance = $user->free_balance - $after_min_payed;
                        $user->payed_balance = 0;
                    }
                }
                $user->save();

                //to get the expire_date of ad
                $mytime = Carbon::now();
                $today = Carbon::parse($mytime->toDateTimeString())->format('Y-m-d H:i');
                $ad_data = null;
                $pin = Plan_details::where('plan_id', $plan_id)->where('type', 'pin')->first();
                if ($pin != null) {
                    $expire_pin_date = $pin->expire_days;
                    $ad_data['pin'] = 1;
                    //to create expire pin date
                    $final_pin_date = Carbon::createFromFormat('Y-m-d H:i', $today);
                    $final_expire_pin_date = $final_pin_date->addDays($expire_pin_date);
                    $ad_data['expire_pin_date'] = $final_expire_pin_date;
                }
                $re_post = Plan_details::where('plan_id', $plan_id)->where('type', 're_post')->first();
                if ($re_post != null) {
                    $expire_re_post_date = $re_post->expire_days;
                    $ad_data['re_post'] = '1';
                    //to create expire pin date
                    $final_pin_date = Carbon::createFromFormat('Y-m-d H:i', $today);
                    $final_expire_re_post_date = $final_pin_date->addDays($expire_re_post_date);
                    $ad_data['re_post_date'] = $final_expire_re_post_date;
                }
                $special = Plan_details::where('plan_id', $plan_id)->where('type', 'special')->first();
                if ($special != null) {
                    $expire_special_date = $special->expire_days;
                    $ad_data['is_special'] = '1';
                    //to create expire pin date
                    $final_pin_date = Carbon::createFromFormat('Y-m-d H:i', $today);
                    $final_expire_special_date = $final_pin_date->addDays($expire_special_date);
                    $ad_data['expire_special_date'] = $final_expire_special_date;
                }

                $final_today = Carbon::createFromFormat('Y-m-d H:i', $today);
                $expire_date = $final_today->addDays($expire_days);

                $ad_data['publish'] = 'Y';
                $ad_data['plan_id'] = $plan_id;
                $ad_data['publication_date'] = $today;
                $ad_data['expiry_date'] = $expire_date;
                Product::where('id', $ad_id)->update($ad_data);

                $response = APIHelpers::createApiResponse(false, 200, 'your ad added successfully', 'تم أنشاء الاعلان بنجاح', null, $request->lang);
                return response()->json($response, 200);
            } else {
                $response = APIHelpers::createApiResponse(true, 406, 'Your wallet does not contain enough amount to create an ad',
                    'محفظتك لا تحتوى على المبلغ الكافى لانشاء الاعلانا', null, $request->lang);
                return response()->json($response, 406);
            }
        } else {
            $response = APIHelpers::createApiResponse(true, 406, '', 'يجب تسجيل الدخول اولا', null, $request->lang);
            return response()->json($response, 406);
        }
    }

    // add balance to wallet
    public function save_third_step_with_money(Request $request)
    {
        $plan = Plan::where('id', $request->plan_id)->first();
        if ($plan == null) {
            $response = APIHelpers::createApiResponse(true, 406, 'يجب اختيار خطة صحيحة', 'plan not found ', null, $request->lang);
            return response()->json($response, 406);
        }

        $products = Product::where('id', $request->ad_id)->first();

        if ($products == null) {
            $response = APIHelpers::createApiResponse(true, 406, 'يجب اختيار اعلان صحيحة', 'Ad not found ', null, $request->lang);
            return response()->json($response, 406);
        }
        $user = auth()->user();
        $root_url = $request->root();
        $path = 'https://apitest.myfatoorah.com/v2/SendPayment';
        $token = "bearer rLtt6JWvbUHDDhsZnfpAhpYk4dxYDQkbcPTyGaKp2TYqQgG7FGZ5Th_WD53Oq8Ebz6A53njUoo1w3pjU1D4vs_ZMqFiz_j0urb_BH9Oq9VZoKFoJEDAbRZepGcQanImyYrry7Kt6MnMdgfG5jn4HngWoRdKduNNyP4kzcp3mRv7x00ahkm9LAK7ZRieg7k1PDAnBIOG3EyVSJ5kK4WLMvYr7sCwHbHcu4A5WwelxYK0GMJy37bNAarSJDFQsJ2ZvJjvMDmfWwDVFEVe_5tOomfVNt6bOg9mexbGjMrnHBnKnZR1vQbBtQieDlQepzTZMuQrSuKn-t5XZM7V6fCW7oP-uXGX-sMOajeX65JOf6XVpk29DP6ro8WTAflCDANC193yof8-f5_EYY-3hXhJj7RBXmizDpneEQDSaSz5sFk0sV5qPcARJ9zGG73vuGFyenjPPmtDtXtpx35A-BVcOSBYVIWe9kndG3nclfefjKEuZ3m4jL9Gg1h2JBvmXSMYiZtp9MR5I6pvbvylU_PP5xJFSjVTIz7IQSjcVGO41npnwIxRXNRxFOdIUHn0tjQ-7LwvEcTXyPsHXcMD8WtgBh-wxR8aKX7WPSsT1O8d8reb2aR7K3rkV3K82K_0OgawImEpwSvp9MNKynEAJQS6ZHe_J_l77652xwPNxMRTMASk1ZsJL";
        $headers = array(
            'Authorization:' . $token,
            'Content-Type:application/json'
        );
        $call_back_url = $root_url . "/api/ad/save_third_step/excute_pay?user_id=" . $user->id . "&plan_id=" . $request->plan_id . "&ad_id=" . $request->ad_id;
        $error_url = $root_url . "/api/pay/error";
        $fields = array(
            "CustomerName" => $user->name,
            "NotificationOption" => "LNK",
            "InvoiceValue" => $plan->price,
            "CallBackUrl" => $call_back_url,
            "ErrorUrl" => $error_url,
            "Language" => "AR",
            "CustomerEmail" => $user->email
        );
        $payload = json_encode($fields);
        $curl_session = curl_init();
        curl_setopt($curl_session, CURLOPT_URL, $path);
        curl_setopt($curl_session, CURLOPT_POST, true);
        curl_setopt($curl_session, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl_session, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl_session, CURLOPT_IPRESOLVE, CURLOPT_IPRESOLVE);
        curl_setopt($curl_session, CURLOPT_POSTFIELDS, $payload);
        $result = curl_exec($curl_session);
        curl_close($curl_session);
        $result = json_decode($result);
        $data['url'] = $result->Data->InvoiceURL;
        $response = APIHelpers::createApiResponse(false, 200, '', '', $data, $request->lang);
        return response()->json($response, 200);
    }

    // excute pay
    public function third_step_excute_pay(Request $request)
    {
        //after customer pay the price of plan ..
        $plan_id = $request->plan_id;
        if (auth()->user() != null) {
            $user = User::where('id', $request->user_id)->first();
            $selected_plan = Plan::where('id', $plan_id)->first();
            $plan_ads_number = $selected_plan->ads_count;
            $plan_price = $selected_plan->price;
            //to select expire days of selected plane
            $plan_detail = Plan_details::where('plan_id', $plan_id)->where('type', 'expier_num')->first();
            $expire_days = $plan_detail->expire_days;
            //to get the expire_date of ad
            $mytime = Carbon::now();
            $today = Carbon::parse($mytime->toDateTimeString())->format('Y-m-d H:i');
            $ad_data = null;
            $pin = Plan_details::where('plan_id', $plan_id)->where('type', 'pin')->first();
            if ($pin != null) {
                $expire_pin_date = $pin->expire_days;
                $ad_data['pin'] = 1;
                //to create expire pin date
                $final_pin_date = Carbon::createFromFormat('Y-m-d H:i', $today);
                $final_expire_pin_date = $final_pin_date->addDays($expire_pin_date);
                $ad_data['expire_pin_date'] = $final_expire_pin_date;
            }
            $re_post = Plan_details::where('plan_id', $plan_id)->where('type', 're_post')->first();
            if ($re_post != null) {
                $expire_re_post_date = $re_post->expire_days;
                $ad_data['re_post'] = '1';
                //to create expire pin date
                $final_pin_date = Carbon::createFromFormat('Y-m-d H:i', $today);
                $final_expire_re_post_date = $final_pin_date->addDays($expire_re_post_date);
                $ad_data['re_post_date'] = $final_expire_re_post_date;
            }
            $special = Plan_details::where('plan_id', $plan_id)->where('type', 'special')->first();
            if ($special != null) {
                $expire_special_date = $special->expire_days;
                $ad_data['is_special'] = '1';
                //to create expire pin date
                $final_pin_date = Carbon::createFromFormat('Y-m-d H:i', $today);
                $final_expire_special_date = $final_pin_date->addDays($expire_special_date);
                $ad_data['expire_special_date'] = $final_expire_special_date;
            }

            $final_today = Carbon::createFromFormat('Y-m-d H:i', $today);
            $expire_date = $final_today->addDays($expire_days);

            $ad_data['publish'] = 'Y';
            $ad_data['plan_id'] = $plan_id;
            $ad_data['publication_date'] = $today;
            $ad_data['expiry_date'] = $expire_date;
            Product::where('id', $request->ad_id)->update($ad_data);

            return redirect('api/pay/success');
        } else {
            return redirect('api/pay/error');
        }
    }

    public function republish_ad(Request $request)
    {
        $user = auth()->user();
        if ($user == null) {
            $response = APIHelpers::createApiResponse(true, 406, 'you should login first', 'يجب تسجيل الدخول اولا', null, $request->lang);
            return response()->json($response, 406);
        }
        if ($user->active == 0) {
            $response = APIHelpers::createApiResponse(true, 406, 'your account un actived', 'تم حظر حسابك', null, $request->lang);
            return response()->json($response, 406);
        }
        $plan = Plan::where('id', $request->plan_id)->first();
        if ($user->my_wallet < $plan->price) {
            $response = APIHelpers::createApiResponse(true, 406, 'you don`t have enough balance to republish ad , please buy ads package', 'ليس لديك رصيد إعلانات لتجديد الإعلان يرجي شراء باقه إعلانات', null, $request->lang);
            return response()->json($response, 406);
        }
        $product = Product::where('id', $request->product_id)->where('user_id', $user->id)->first();
        if ($product->status == 1) {
            $response = APIHelpers::createApiResponse(true, 406, 'this ad not ended yet', 'هذا الاعلان لم ينتهى بعد', null, $request->lang);
            return response()->json($response, 406);
        }
        if ($product->deleted == 1) {
            $response = APIHelpers::createApiResponse(true, 406, 'this ad deleted before', 'هذا الاعلان تم حذفة', null, $request->lang);
            return response()->json($response, 406);
        }
        if ($product) {
            $plan_price = $plan->price;
            //to select expire days of selected plane
            $plan_detail = Plan_details::where('plan_id', $request->plan_id)->where('type', 'expier_num')->first();
            $expire_days = $plan_detail->expire_days;

            $user->my_wallet = $user->my_wallet - $plan_price;
            if ($user->free_balance >= $plan_price) {
                $user->free_balance = $user->free_balance - $plan_price;
            } else if ($user->payed_balance >= $plan_price) {
                $user->payed_balance = $user->payed_balance - $plan_price;
            } else {
                $free_balance = $user->free_balance;  //70
                $payed_balance = $user->payed_balance;  //30
                $price = $plan_price; //100
                $after_min_free = $price - $free_balance;  // 100 - 70 = 30
                if ($after_min_free <= $payed_balance && $after_min_free > 0) {
                    // 30 <= 30 && 30 < 0
                    $user->free_balance = 0;
                    $user->payed_balance = $user->payed_balance - $after_min_free;
                } else if ($after_min_free > $payed_balance && $after_min_free > 0) {
                    $after_min_payed = $price - $payed_balance;
                    $user->free_balance = $user->free_balance - $after_min_payed;
                    $user->payed_balance = 0;
                }
            }
            $user->save();
            //to get the expire_date of ad
            $mytime = Carbon::now();
            $today = Carbon::parse($mytime->toDateTimeString())->format('Y-m-d H:i');
            $pin = Plan_details::where('plan_id', $request->plan_id)->where('type', 'pin')->first();
            if ($pin != null) {
                $expire_pin_date = $pin->expire_days;
                $product->pin = 1;
                //to create expire pin date
                $final_pin_date = Carbon::createFromFormat('Y-m-d H:i', $today);
                $final_expire_pin_date = $final_pin_date->addDays($expire_pin_date);
                $product->expire_pin_date = $final_expire_pin_date;
            }
            $re_post = Plan_details::where('plan_id', $request->plan_id)->where('type', 're_post')->first();
            if ($re_post != null) {
                $expire_re_post_date = $re_post->expire_days;
                $product->re_post = '1';
                //to create expire pin date
                $final_pin_date = Carbon::createFromFormat('Y-m-d H:i', $today);
                $final_expire_re_post_date = $final_pin_date->addDays($expire_re_post_date);
                $product->re_post_date = $final_expire_re_post_date;
            }
            $special = Plan_details::where('plan_id', $request->plan_id)->where('type', 'special')->first();
            if ($special != null) {
                $expire_special_date = $special->expire_days;
                $product->is_special = '1';
                //to create expire pin date
                $final_pin_date = Carbon::createFromFormat('Y-m-d H:i', $today);
                $final_expire_special_date = $final_pin_date->addDays($expire_special_date);
                $product->expire_special_date = $final_expire_special_date;
            }

            $final_today = Carbon::createFromFormat('Y-m-d H:i', $today);
            $expire_date = $final_today->addDays($expire_days);
            $product->plan_id = $request->plan_id;
            $product->expiry_date = $expire_date;
            $product->status = 1;
            $product->publish = 'Y';
            $product->save();
            $response = APIHelpers::createApiResponse(false, 200, 'republish done', 'تم اعادة النشر بنجاح', null, $request->lang);
            return response()->json($response, 200);

        } else {
            $response = APIHelpers::createApiResponse(true, 406, 'ليس لديك الصلاحيه لتجديد هذا الاعلان', '', null, $request->lang);
            return response()->json($response, 406);
        }
    }

    public function select_my_ads(Request $request)
    {
        $ads['ended_ads'] = Product::where('status', 2)
            ->where('deleted', 0)
            ->where('user_id', auth()->user()->id)
            ->select('id', 'title', 'price', 'main_image')
            ->orderBy('created_at', 'desc')
            ->get();
        $ads['current_ads'] = Product::where('status', 1)
            ->where('publish', 'Y')
            ->where('deleted', 0)
            ->where('user_id', auth()->user()->id)
            ->select('id', 'title', 'price', 'main_image')
            ->orderBy('created_at', 'desc')
            ->get();
        if (count($ads) == 0) {
            $response = APIHelpers::createApiResponse(false, 200, 'no ads yet !', ' !لا يوجد اعلانات حتى الان', null, $request->lang);
            return response()->json($response, 200);
        } else {
            $response = APIHelpers::createApiResponse(false, 200, '', '', $ads, $request->lang);
            return response()->json($response, 200);
        }
    }

    public function ended_ads(Request $request)
    {
        $data = Product::where('status', 2)
            ->where('deleted', 0)
            ->where('user_id', auth()->user()->id)
            ->select('id', 'title', 'price', 'main_image')
            ->orderBy('created_at', 'desc')
            ->get();
        if (count($data) == 0) {
            $response = APIHelpers::createApiResponse(false, 200, 'no ads yet !', ' !لا يوجد اعلانات حتى الان', null, $request->lang);
            return response()->json($response, 200);
        } else {
            $response = APIHelpers::createApiResponse(false, 200, '', '', $data, $request->lang);
            return response()->json($response, 200);
        }
    }

    public function current_ads(Request $request)
    {
        $data = Product::where('status', 1)
            ->where('publish', 'Y')
            ->where('deleted', 0)
            ->where('user_id', auth()->user()->id)
            ->select('id', 'title', 'price', 'main_image')
            ->orderBy('created_at', 'desc')
            ->get();
        if (count($data) == 0) {
            $response = APIHelpers::createApiResponse(false, 200, 'no ads yet !', ' !لا يوجد اعلانات حتى الان', null, $request->lang);
            return response()->json($response, 200);
        } else {
            $response = APIHelpers::createApiResponse(false, 200, '', '', $data, $request->lang);
            return response()->json($response, 200);
        }
    }

    public function last_seen(Request $request)
    {
        $user = auth()->user();
        if ($user == null) {
            $response = APIHelpers::createApiResponse(true, 406, 'you should login first', 'يجب تسجيل الدخول اولا', null, $request->lang);
            return response()->json($response, 406);
        }

        $ads = Product_view::where('user_id', auth()->user()->id)
            ->select('product_id', 'user_id')
            ->orderBy('created_at', 'desc')
            ->get();
        $inc = 0;
        foreach ($ads as $key => $row) {
            $product = Product::where('id', $row->product_id)->first();
            if ($product != null) {
                if ($product->status == 1 && $product->deleted == 0 && $product->publish == 'Y') {
                    $data[$inc]['id'] = $product->id;
                    $data[$inc]['title'] = $product->title;
                    $data[$inc]['image'] = $product->main_image;
                    $data[$inc]['price'] = number_format((float)($product->price), 3) ;
                    $data[$inc]['description'] = $product->description;
                    $favorite = Favorite::where('user_id', $user->id)->where('product_id', $product->id)->first();
                    if ($favorite) {
                        $data[$inc]['favorite'] = true;
                    } else {
                        $data[$inc]['favorite'] = false;
                    }
                    $inc = $inc + 1;
                }
            }
        }
        if (count($data) == 0) {
            $response = APIHelpers::createApiResponse(false, 200, 'no ads yet !', ' !لا يوجد اعلانات حتى الان', null, $request->lang);
            return response()->json($response, 200);
        } else {
            $response = APIHelpers::createApiResponse(false, 200, '', '', $data, $request->lang);
            return response()->json($response, 200);
        }
    }

    public function offer_ads(Request $request)
    {
        $user = auth()->user();
        $lang = $request->lang;
        if ($user == null) {
            $response = APIHelpers::createApiResponse(true, 406, 'you should login first', 'يجب تسجيل الدخول اولا', null, $request->lang);
            return response()->json($response, 406);
        }
        if ($lang == 'ar') {
            $offer_image = Setting::where('id', 1)->first()->offer_image;
        } else {
            $offer_image = Setting::where('id', 1)->first()->offer_image_en;
        }
        $ads = Product::where('offer', 1)
            ->where('status', 1)
            ->where('deleted', 0)
            ->where('publish', 'Y')
            ->orderBy('created_at', 'desc')
            ->get();
        $inc = 0;
        foreach ($ads as $key => $row) {
            $data[$inc]['id'] = $row->id;
            $data[$inc]['title'] = $row->title;
            $data[$inc]['image'] = $row->main_image;
            $data[$inc]['price'] = $row->price;
            $data[$inc]['description'] = $row->description;
            $favorite = Favorite::where('user_id', $user->id)->where('product_id', $row->id)->first();
            if ($favorite) {
                $data[$inc]['favorite'] = true;
            } else {
                $data[$inc]['favorite'] = false;
            }
            $inc = $inc + 1;
        }
        if (count($data) == 0) {
            $response = APIHelpers::createApiResponse(false, 200, 'no ads yet !', ' !لا يوجد اعلانات حتى الان', null, $request->lang);
            return response()->json($response, 200);
        } else {
            $response = APIHelpers::createApiResponse(false, 200, '', '', array('offer_image' => $offer_image, 'data' => $data), $request->lang);
            return response()->json($response, 200);
        }
    }

    public function select_current_ads(Request $request)
    {
        $ads = Product::where('status', 1)
            ->where('publish', 'Y')
            ->where('deleted', 0)
            ->where('user_id', auth()->user()->id)
            ->select('id', 'title', 'price', 'main_image', 'views', 'pin', 'publication_date')
            ->orderBy('pin', 'desc')
            ->orderBy('created_at', 'desc')
            ->simplePaginate(12)
            ->map(function ($ads) {
                $ads->views = Product_view::where('product_id', $ads->id)->count();
                return $ads;
            });
        if (count($ads) == 0) {
            $response = APIHelpers::createApiResponse(false, 200, 'no ended ads yet !', ' !لا يوجد اعلانات منتهيه حتى الان', null, $request->lang);
            return response()->json($response, 200);
        } else {
            $response = APIHelpers::createApiResponse(false, 200, '', '', $ads, $request->lang);
            return response()->json($response, 200);
        }
    }

    public function select_all_ads(Request $request)
    {
        $ads = Product::where('status', 1)
            ->where('publish', 'Y')
            ->where('deleted', 0)
            ->where('user_id', auth()->user()->id)
            ->select('id', 'title', 'price', 'main_image', 'pin', 'user_id')
            ->orderBy('pin', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
        if (count($ads) == 0) {
            $response = APIHelpers::createApiResponse(false, 200, 'no  ads until now !', ' !لا يوجد اعلانات حتى الان', null, $request->lang);
            return response()->json($response, 200);
        } else {
            $response = APIHelpers::createApiResponse(false, 200, '', '', $ads, $request->lang);
            return response()->json($response, 200);
        }
    }

    public function delete_my_ad(Request $request, $id)
    {
        $user = auth()->user();
        if ($user != null) {
            $product = Product::where('id', $id)->first();
            if ($product != null) {
                if ($product->user_id == $user->id) {
                    $product->deleted = 1;
                    $product->save();
                    $response = APIHelpers::createApiResponse(false, 200, 'deleted', 'تم الحذف بنجاح', null, $request->lang);
                    return response()->json($response, 200);
                } else {
                    $response = APIHelpers::createApiResponse(true, 406, 'this not your ad', 'لا تمتلك هذا الاعلان !!', null, $request->lang);
                    return response()->json($response, 406);
                }
            } else {
                $response = APIHelpers::createApiResponse(true, 406, 'no ad of this id', 'لا يوجد اعلان بهذا ال id', null, $request->lang);
                return response()->json($response, 406);
            }
        } else {
            $response = APIHelpers::createApiResponse(true, 406, 'you should login first ', 'يجب تسجيل الدخول أولا !!', null, $request->lang);
            return response()->json($response, 406);
        }
    }

    public function select_ad_data(Request $request, $id)
    {
        Session::put('local_api', $request->lang);
        $data['ad'] = Product::where('id', $id)
            ->with('City_api')
            ->with('Area_api')
            ->select('id', 'category_id', 'sub_category_id', 'sub_category_two_id', 'sub_category_three_id', 'sub_category_four_id', 'sub_category_five_id', 'title', 'price', 'description', 'main_image', 'city_id', 'area_id', 'share_location', 'latitude', 'longitude')
            ->first();
        if ($data['ad']['sub_category_id'] == null) {
            $data['ad']['sub_category_id'] = 0;
        }
        if ($data['ad']['sub_category_two_id'] == null) {
            $data['ad']['sub_category_two_id'] = 0;
        }
        if ($data['ad']['sub_category_three_id'] == null) {
            $data['ad']['sub_category_three_id'] = 0;
        }
        if ($data['ad']['sub_category_four_id'] == null) {
            $data['ad']['sub_category_four_id'] = 0;
        }
        if ($data['ad']['sub_category_five_id'] == null) {
            $data['ad']['sub_category_five_id'] = 0;
        }
        if ($data['ad']->share_location == '1') {
            $data['ad']->share_location = true;
        } else {
            $data['ad']->share_location = false;
        }
        $data['ad_images'] = ProductImage::where('product_id', $id)->select('id', 'image', 'product_id')->get();
        if ($request->lang == 'ar') {
            if ($data['ad']->city_id != null) {
                $cat_data_city = City::find($data['ad']->city_id);
                $data['area_names'] = $cat_data_city->title_ar;
            }
            if ($data['ad']->area_id != null) {
                $cat_data_area = Area::find($data['ad']->area_id);
                $data['area_names'] = $data['area_names'] . '/' . $cat_data_area->title_ar;
            }
            if ($data['ad']->category_id != null) {
                $cat_data = Category::find($data['ad']->category_id);
                $data['category_names'] = $cat_data->title_ar;
            }
            if ($data['ad']->sub_category_id != null) {
                $scat_data = SubCategory::find($data['ad']->sub_category_id);
                $data['category_names'] = $data['category_names'] . '/' . $scat_data->title_ar;
            }
            if ($data['ad']->sub_category_two_id != null) {
                $sscat_data = SubTwoCategory::find($data['ad']->sub_category_two_id);
                $data['category_names'] = $data['category_names'] . '/' . $sscat_data->title_ar;
            }
            if ($data['ad']->sub_category_three_id != null) {
                $ssscat_data = SubThreeCategory::find($data['ad']->sub_category_three_id);
                $data['category_names'] = $data['category_names'] . '/' . $ssscat_data->title_ar;
            }
            if ($data['ad']->sub_category_four_id != null) {
                $sssscat_data = SubFourCategory::find($data['ad']->sub_category_four_id);
                $data['category_names'] = $data['category_names'] . '/' . $sssscat_data->title_ar;
            }
            if ($data['ad']->sub_category_five_id != null) {
                $ssssscat_data = SubFiveCategory::find($data['ad']->sub_category_five_id);
                $data['category_names'] = $data['category_names'] . '/' . $ssssscat_data->title_ar;
            }
        } else {
            if ($data['ad']->city_id != null) {
                $cat_data_city = City::find($data['ad']->city_id);
                $data['area_names'] = $cat_data_city->title_en;
            }
            if ($data['ad']->area_id != null) {
                $cat_data_area = Area::find($data['ad']->area_id);
                $data['area_names'] = $data['area_names'] . '/' . $cat_data_area->title_en;
            }
            if ($data['ad']->category_id != null) {
                $cat_data = Category::find($data['ad']->category_id);
                $data['category_names'] = $cat_data->title_en;
            }
            if ($data['ad']->sub_category_id != null) {
                $scat_data = SubCategory::find($data['ad']->sub_category_id);
                $data['category_names'] = $data['category_names'] . '/' . $scat_data->title_en;
            }
            if ($data['ad']->sub_category_two_id != null) {
                $sscat_data = SubTwoCategory::find($data['ad']->sub_category_two_id);
                $data['category_names'] = $data['category_names'] . '/' . $sscat_data->title_en;
            }
            if ($data['ad']->sub_category_three_id != null) {
                $ssscat_data = SubThreeCategory::find($data['ad']->sub_category_three_id);
                $data['category_names'] = $data['category_names'] . '/' . $ssscat_data->title_en;
            }
            if ($data['ad']->sub_category_four_id != null) {
                $sssscat_data = SubFourCategory::find($data['ad']->sub_category_four_id);
                $data['category_names'] = $data['category_names'] . '/' . $sssscat_data->title_en;
            }
            if ($data['ad']->sub_category_five_id != null) {
                $ssssscat_data = SubFiveCategory::find($data['ad']->sub_category_five_id);
                $data['category_names'] = $data['category_names'] . '/' . $ssssscat_data->title_en;
            }
        }

        $features = Product_feature::where('product_id', $id)
            ->select('id', 'type', 'product_id', 'target_id', 'option_id')
            ->orderBy('option_id', 'asc')
            ->get();

        foreach ($features as $key => $feature) {
            if ($feature->type == 'manual') {
                $features[$key]['type'] = 'input';
                $features[$key]['value'] = $feature->target_id;
            } else if ($feature->type == 'option') {
                $features[$key]['type'] = 'select';
                $target_data = Category_option_value::where('id', $feature->target_id)->first();
                if ($request->lang == 'ar')
                    $features[$key]['value'] = $target_data->value_ar;
                else {
                    $features[$key]['value'] = $target_data->value_en;
                }
            }
        }

        $data['features'] = $features;
        $response = APIHelpers::createApiResponse(false, 200, 'data shown', 'تم أظهار البيانات', $data, $request->lang);
        return response()->json($response, 200);
    }

    public function remove_main_image(Request $request, $id)
    {
        $data['main_image'] = null;
        $final_data = Product::where('id', $id)->update($data);

        if ($final_data == 1) {
            $data_f['status'] = true;
            $response = APIHelpers::createApiResponse(false, 200, 'data updated', 'تم تعديل البيانات', $data_f, $request->lang);
            return response()->json($response, 200);
        } else {
            $data_f['status'] = false;
            $response = APIHelpers::createApiResponse(true, 406, 'not updated', 'لم يتم التعديل', $data_f, $request->lang);
            return response()->json($response, 406);
        }
    }

    public function remove_product_image(Request $request, $image_id)
    {

        $final_data = ProductImage::where('id', $image_id)->delete();
        if ($final_data == 1) {
            $data_f['status'] = true;
            $response = APIHelpers::createApiResponse(false, 200, 'data deleted', 'تم الحذف البيانات', $data_f, $request->lang);
            return response()->json($response, 200);
        } else {
            $data_f['status'] = false;
            $response = APIHelpers::createApiResponse(true, 406, 'not deleted', 'لم يتم الحذف', $data_f, $request->lang);
            return response()->json($response, 406);
        }
    }

    public function update_ad(Request $request, $id)
    {
        $input = $request->all();
        $product = Product::where('id', $id)->first();
        if ($product == null) {
            $response = APIHelpers::createApiResponse(true, 406, 'ad not exists', 'لا يوجد اعلان بهذا ال id', null, $request->lang);
            return response()->json($response, 406);
        }
        $validator = Validator::make($input, [
            'category_id' => 'required',
            'sub_category_id' => '',
            'sub_category_two_id' => '',
            'sub_category_three_id' => '',
            'sub_category_four_id' => '',
            'sub_category_five_id' => '',
            'title' => 'required',
            'city_id' => 'required',
            'area_id' => 'required',
            'share_location' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'options' => '',
            'price' => 'required|numeric',
            'description' => '',
            'main_image' => '',
            'images' => ''
        ]);
        if ($validator->fails()) {
            $response = APIHelpers::createApiResponse(true, 406, $validator->messages()->first(), $validator->messages()->first(), null, $request->lang);
            return response()->json($response, 406);
        } else {
            $user = auth()->user();
            if ($user != null) {
                $input['user_id'] = $user->id;
            } else {
                $response = APIHelpers::createApiResponse(true, 406, 'you should login first', 'يجب تسجيل الدخول اولا', null, $request->lang);
                return response()->json($response, 406);
            }
            if ($request->main_image != null) {
                $image = $request->main_image;
                Cloudder::upload("data:image/jpeg;base64," . $image, null);
                $imagereturned = Cloudder::getResult();
                $image_id = $imagereturned['public_id'];
                $image_format = $imagereturned['format'];
                $image_new_name = $image_id . '.' . $image_format;
                $input['main_image'] = $image_new_name;
            }
            if ($request->images != null) {
                foreach ($request->images as $image) {
                    Cloudder::upload("data:image/jpeg;base64," . $image, null);
                    $imagereturned = Cloudder::getResult();
                    $image_id = $imagereturned['public_id'];
                    $image_format = $imagereturned['format'];
                    $image_name = $image_id . '.' . $image_format;
                    $data['product_id'] = $id;
                    $data['image'] = $image_name;
                    ProductImage::create($data);
                }
            }
            unset($input['images']);
            unset($input['options']);
            $updated = Product::where('id', $id)->update($input);
            if ($request->options != null) {
                Product_feature::where('product_id', $id)->delete();
                foreach ($request->options as $key => $option) {
                    if ($option['option_value'] != null) {
                        if (is_numeric($option['option_value'])) {
                            $option_values = Category_option_value::where('id', $option['option_value'])->first();
                            if ($option_values != null) {
                                $feature_data['type'] = 'option';
                            } else {
                                $feature_data['type'] = 'manual';
                            }
                        } else {
                            $feature_data['type'] = 'manual';
                        }
                        $feature_data['product_id'] = $id;
                        $feature_data['target_id'] = $option['option_value'];
                        $feature_data['option_id'] = $option['option_id'];
                        Product_feature::create($feature_data);
                    }
                }
            }
            if ($updated == 1) {
                $final_data['status'] = true;
                $response = APIHelpers::createApiResponse(false, 200, 'updated successfuly', 'تم التعديل بنجاح', $final_data, $request->lang);
                return response()->json($response, 200);
            } else {
                $data_f['status'] = false;
                $response = APIHelpers::createApiResponse(true, 406, 'not updated', 'لم يتم التعديل', $data_f, $request->lang);
                return response()->json($response, 406);
            }
        }
    }

    public function cities(Request $request)
    {
        Session::put('api_lang', $request->lang);
        if ($request->lang == 'en') {
            $cities = City::where('deleted', '0')
                ->select('id', 'title_en as title')
                ->get();
        } else {
            $cities = City::where('deleted', '0')
                ->select('id', 'title_ar as title')
                ->get();
        }
        $response = APIHelpers::createApiResponse(false, 200, '', '',  $cities, $request->lang);
        return response()->json($response, 200);
    }

    public function get_job_experience(Request $request)
    {
        $user = auth()->user();
        $data = Cv_job_experience::select('id','job_name','job_distination','start_date','end_date')->where('user_id',$user->id)->where('cv_id',null)->get();
        $response = APIHelpers::createApiResponse(false, 200, '', '',  $data, $request->lang);
        return response()->json($response, 200);
    }
    public function delete_job_experience(Request $request,$id)
    {
        $data = Cv_job_experience::where('id',$id)->delete();
        $response = APIHelpers::createApiResponse(false, 200, 'Job experience deleted successfully', 'تم حذف الخبرة الوظيفية بنجاح', null , $request->lang);
        return response()->json($response, 200);
    }
    public function nationalities(Request $request)
    {
        Session::put('api_lang', $request->lang);
        if ($request->lang == 'en') {
            $nationalities = Nationality::where('deleted', '0')
                ->select('id', 'title_en as title')
                ->get();
        } else {
            $nationalities = Nationality::where('deleted', '0')
                ->select('id', 'title_ar as title')
                ->get();
        }
        $response = APIHelpers::createApiResponse(false, 200, '', '',  $nationalities, $request->lang);
        return response()->json($response, 200);
    }

    public function areas( Request $request , $city_id )
    {
        Session::put('api_lang', $request->lang);

        $areas = [];
        if ($request->lang == 'en') {
            $areas = Area::where('city_id',$city_id)->where('deleted', '0')
                ->select('id', 'title_en as title')
                ->get();
        } else {
            $areas = Area::where('city_id',$city_id)->where('deleted', '0')
                ->select('id', 'title_ar as title')
                ->get();
        }
        $response = APIHelpers::createApiResponse(false, 200, '', '', $areas, $request->lang);
        return response()->json($response, 200);
    }


    public function make_report(Request $request)
    {
        $user = auth()->user();
        if ($user->active == 0) {
            $response = APIHelpers::createApiResponse(true, 406, 'تم حظر حسابك', 'تم حظر حسابك', null, $request->lang);
            return response()->json($response, 406);
        }
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
        ]);
        if ($validator->fails()) {
            $response = APIHelpers::createApiResponse(true, 406, $validator->errors()->first(), $validator->errors()->first(), null, $request->lang);
            return response()->json($response, 406);
        }
        $reported = Product_report::where('product_id', $request->product_id)->where('user_id', $user->id)->first();
        if ($reported) {
            $response = APIHelpers::createApiResponse(true, 406, 'this ad reported before', 'تم الابلاغ عن هذا الاعلان من قبل', null, $request->lang);
            return response()->json($response, 406);
        } else {
            $favorite = new Product_report();
            $favorite->user_id = $user->id;
            $favorite->product_id = $request->product_id;
            $favorite->save();
            $response = APIHelpers::createApiResponse(false, 200, '', '', $favorite, $request->lang);
            return response()->json($response, 200);
        }
    }

    public function make_comment(Request $request)
    {
        $user = auth()->user();
        if ($user->active == 0) {
            $response = APIHelpers::createApiResponse(true, 406, 'your account blocked', 'تم حظر حسابك', null, $request->lang);
            return response()->json($response, 406);
        }
        $validator = Validator::make($request->all(), [
            'comment' => 'required',
            'product_id' => 'required|exists:products,id',
        ]);
        if ($validator->fails()) {
            $response = APIHelpers::createApiResponse(true, 406, $validator->errors()->first(), $validator->errors()->first(), null, $request->lang);
            return response()->json($response, 406);
        }
        $comment = new Product_comment();
        $comment->comment = $request->comment;
        $comment->user_id = $user->id;
        $comment->product_id = $request->product_id;
        $comment->save();

        $response = APIHelpers::createApiResponse(false, 200, 'commented successfully', 'تم التعليق بنجاح', $comment, $request->lang);
        return response()->json($response, 200);

    }


}
