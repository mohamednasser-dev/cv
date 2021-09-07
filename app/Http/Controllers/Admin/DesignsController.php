<?php
namespace App\Http\Controllers\Admin;

use App\Design;
use Illuminate\Http\Request;
use Cloudinary;

class DesignsController extends AdminController
{
    public function index() {
        $data = Design::all();
        return view('admin.designs.index',compact('data'));
    }
    public function create() {
        return view('admin.designs.create');
    }
    public function store(Request $request) {
        $data = $this->validate(\request(),
            [
                'image' => 'required',
                'title_ar' => 'required',
                'title_en' => 'required',
                'type' => 'required',
            ]);
        if ($request->image != null) {
            $logoreturned = Cloudinary::upload($request->file('image')->getRealPath()) ;
            $logo_id = $logoreturned->getPublicId();;
            $logo_format = $logoreturned->getExtension();;
            $logo_new_name = $logo_id.'.'.$logo_format;
            $data['image'] = $logo_new_name;
        }else{
            unset($data['image']);
        }
        Design::create($data);
        session()->flash('success', trans('messages.added_s'));
        return redirect( route('designs.index'));
    }
    public function edit($id) {
        $data = Design::findOrFail($id)->first();
        return view('admin.designs.edit', compact('data'));
    }
    public function update(Request $request, $id) {
        $data = $this->validate(\request(),
            [
                'image' => 'required',
                'title_ar' => 'required',
                'title_en' => 'required',
                'type' => 'required',
            ]);

        $image_name = $request->file('image')->getRealPath();
        Cloudder::upload($image_name, null);
        $imagereturned = Cloudder::getResult();
        $image_id = $imagereturned['public_id'];
        $image_format = $imagereturned['format'];
        $data['image'] = $image_id.'.'.$image_format;
        Design::findOrFail($id)->update($data);
        session()->flash('success', trans('messages.updated_s'));
        return redirect( route('designs.index'));
    }
    public function show(){
    }
    public function destroy($id) {
        $data['deleted'] = "1";
        Design::where('id',$id)->update($data);
        session()->flash('success', trans('messages.deleted_s'));
        return back();
    }
}
