<?php

namespace App\Http\Controllers;

use App\cv;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Helpers\APIHelpers;

class CVController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => [ 'save_design']]);
    }

    public function save_design(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'design_number' => 'required'
        ]);
        if ($validator->fails()) {
            $response = APIHelpers::createApiResponse(true, 406, $validator->errors()->first(), $validator->errors()->first());
            return response()->json($response, 406);
        }
        $product = new cv();
        $product->design_number = $request->design_number;
        $product->user_id = $user->id;
        $product->save();
        $response = APIHelpers::createApiResponse(false, 200, '', $product);
        return response()->json($response, 200);
    }

}
