@extends('admin.app')

@section('title' , __('messages.add_plan'))

@section('content')
    <div class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>{{ __('messages.add_plan') }}</h4>
                 </div>
            </div>
        </div>
        <form action="{{route('designs.store')}}" method="post" enctype="multipart/form-data" file="true" >
            @csrf
            <div class="custom-file-container" data-upload-id="myFirstImage">
                <label>{{ __('messages.upload') }} ({{ __('messages.single_image') }}) <a href="javascript:void(0)" class="custom-file-container__image-clear" title="Clear Image">x</a></label>
                <label class="custom-file-container__custom-file" >
                    <input type="file" required name="image" class="custom-file-container__custom-file__custom-file-input" accept="image/*">
                    <input type="hidden" name="MAX_FILE_SIZE" value="10485760" />
                    <span class="custom-file-container__custom-file__custom-file-control"></span>
                </label>
                <div class="custom-file-container__image-preview"></div>
            </div>
            <div class="form-group mb-4">
                <label for="plan_price">{{ __('messages.title_ar') }}</label>
                <input required type="text" name="title_ar" class="form-control" >
            </div>
            <div class="form-group mb-4">
                <label for="plan_price">{{ __('messages.title_en') }}</label>
                <input required type="text" name="title_en" class="form-control" >
            </div>
            <div class="form-group">
                <label for="sel1">{{ __('messages.type') }}</label>
                <select class="form-control" name="type" id="sel1">
                    <option value="free" selected>{{ __('messages.free') }}</option>
                    <option value="payed">{{ __('messages.payed') }}</option>
                </select>
            </div>
            <input type="submit" value="{{ __('messages.add') }}" class="btn btn-primary">
        </form>
    </div>
@endsection
