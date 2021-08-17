<?php

namespace App\Http\Controllers\Admin;

use App\Nationality;
use Illuminate\Http\Request;

class NationalityController extends AdminController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Nationality::where('deleted','0')->OrderBy('id','desc')->get();
        return view('admin.nationality.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.nationality.create');
    }


    public function store(Request $request)
    {
        $data = $this->validate(\request(),
        [
            'title_ar' => 'required',
            'title_en' => 'required'
        ]);
        Nationality::create($data);
        session()->flash('success', trans('messages.added_s'));
        return redirect( route('nationalities.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function edit($id) {
        $data = Nationality::findOrFail($id)->first();
        return view('admin.nationality.edit', compact('data'));
    }
    public function update(Request $request, $id) {
        $data = $this->validate(\request(),
            [
                'title_ar' => 'required',
                'title_en' => 'required'
            ]);
        Nationality::findOrFail($id)->update($data);
        session()->flash('success', trans('messages.updated_s'));
        return redirect( route('nationalities.index'));
    }

    public function destroy($id)
    {
        $data['deleted'] = '1';
        Nationality::findOrFail($id)->update($data);
        session()->flash('success', trans('messages.deleted_s'));
        return back();
    }

}
