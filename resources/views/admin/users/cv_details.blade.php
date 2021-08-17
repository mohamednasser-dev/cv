@extends('admin.app')

@section('title' , __('messages.user_details'))

@section('content')

        <div id="tableSimple" class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">

            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <h4>{{ __('messages.user_details') }}</h4>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">
            <div class="table-responsive">
                <table class="table table-bordered mb-4">
                    <h4>{{ __('messages.personal_data') }}</h4>

                    <tbody>
                            <tr>
                                <td class="label-table" > {{ __('messages.full_name') }}</td>
                                <td>{{ $data->Personal_data_all->full_name }}</td>
                            </tr>
                        <tr>
                            <td class="label-table" > {{ __('messages.date_of_birth') }} </td>
                            <td>{{ $data->Personal_data_all->date_of_birth }}</td>
                        </tr>
                            <tr>
                                <td class="label-table" > {{ __('messages.nationality') }} </td>
                                @if($data->Personal_data_all->nationality_id != null)
                                    @if(app()->getLocale() == 'ar')
                                        <td>{{ $data->Personal_data_all->Nationality_web->title_ar }}</td>
                                    @else
                                        <td>{{ $data->Personal_data_all->Nationality_web->title_en }}</td>
                                    @endif

                                @else
                                    <td></td>
                                @endif
                            </tr>

                            <tr>
                                <td class="label-table" > {{ __('messages.social_status') }} </td>
                                <td>{{ $data->Personal_data_all->date_of_birth }}</td>
                            </tr>
                            <tr>
                                <td class="label-table" > {{ __('messages.social_status') }} </td>
                                <td>{{ $data->Personal_data_all->social_status }}</td>
                            </tr>
                    </tbody>
                </table>
            </div>
                    <div class="table-responsive">
                        <table class="table table-bordered mb-4">
                        <h4>{{ __('messages.call_details') }}</h4>
                    <tbody>
                            <tr>
                                <td class="label-table" > {{ __('messages.email') }} </td>
                                <td>{{ $data->Personal_data_all->email }}</td>
                            </tr>
                            <tr>
                                <td class="label-table" > {{ __('messages.phone') }} </td>
                                <td>{{ $data->Personal_data_all->phone }}</td>
                            </tr>
                            <tr>
                                <td class="label-table" > {{ __('messages.web_site') }} </td>
                                <td>{{ $data->Personal_data_all->web_site }}</td>
                            </tr>
                    </tbody>
                        </table>
                    </div>
            <div class="table-responsive">
                <table class="table table-bordered mb-4">
                    <h4>{{ __('messages.location') }}</h4>

                    <tbody>
                            <tr>
                                <td class="label-table" > {{ __('messages.address') }} </td>
                                <td>{{ $data->Personal_data_all->address }}</td>
                            </tr>
                            <tr>
                                <td class="label-table" > {{ __('messages.mail') }} </td>
                                <td>{{ $data->Personal_data_all->mail }}</td>
                            </tr>
                            <tr>
                                <td class="label-table" > {{ __('messages.city') }} </td>
                                @if($data->Personal_data_all->city_id != null)
                                    @if(app()->getLocale() == 'ar')
                                        <td>{{ $data->Personal_data_all->City_web->title_ar }}</td>
                                    @else
                                        <td>{{ $data->Personal_data_all->City_web->title_en }}</td>
                                    @endif

                                @else
                                    <td></td>
                                @endif
                            </tr>
                    </tbody>
                </table>
                <h4>{{ __('messages.job_experience') }}</h4>
                <table class="table table-bordered mb-4">
                    <thead>
                    <tr>
                        <th>{{ __('messages.job_name') }}</th>
                        <th class="text-center">{{ __('messages.job_distination') }}</th>
                        <th class="text-center">{{ __('messages.start_date') }}</th>
                        <th class="text-center">{{ __('messages.end_date_d') }}</th>
                    </tr>
                    </thead>
                    <tbody>
              @foreach($data->Job_experiences as $row)
                    <tr>
                        <td>{{ $row->job_name }}</td>
                        <td>{{ $row->job_distination }}</td>
                        <td>{{ $row->start_date }}</td>
                        <td>{{ $row->end_date }}</td>
                    </tr>
              @endforeach
                    </tbody>
                </table>
                <h4>{{ __('messages.certificats') }}</h4>
                <table class="table table-bordered mb-4">
                    <thead>
                    <tr>
                        <th>{{ __('messages.certificate_name') }}</th>
                        <th class="text-center">{{ __('messages.cert_degree') }}</th>
                        <th class="text-center">{{ __('messages.college_name') }}</th>
                        <th class="text-center">{{ __('messages.start_date') }}</th>
                        <th class="text-center">{{ __('messages.end_date_d') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data->Certificats as $row)
                        <tr>
                            <td>{{ $row->certificate_name }}</td>
                            <td>{{ $row->degree_specialization }}</td>
                            <td>{{ $row->collage_name }}</td>
                            <td>{{ $row->start_date }}</td>
                            <td>{{ $row->end_date }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <h4>{{ __('messages.personal_experience') }}</h4>
                <table class="table table-bordered mb-4">
                    <thead>
                    <tr>
                        <th>{{ __('messages.certificate_name') }}</th>
                        <th class="text-center">{{ __('messages.cert_degree') }}</th>
                        <th class="text-center">{{ __('messages.college_name') }}</th>
                        <th class="text-center">{{ __('messages.start_date') }}</th>
                        <th class="text-center">{{ __('messages.end_date_d') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data->Personal_experiences as $row)
                        <tr>
                            <td>{{ $row->job_name }}</td>
                            <td>{{ $row->job_distination }}</td>
                            <td>{{ $row->start_date }}</td>
                            <td>{{ $row->end_date }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>

@endsection



