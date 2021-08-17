@extends('admin.app')

@section('title' , __('messages.cvs'))

@section('content')
    <div id="tableSimple" class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-12">
                        <h4>{{ __('messages.cvs') }}</h4>
                    </div>
                    <div class="col-md-6 pl-0 col-sm-6 col-12 text-right">
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area">
                <div class="table-responsive">
                    <table id="html5-extension" class="table table-hover non-hover" style="width:100%">
                        <thead>
                        <tr>
                            <th>Id</th>
                            <th>{{ __('messages.design_number') }}</th>
                            <th class="text-center">{{ __('messages.details') }}</th>
                            @if(Auth::user()->update_data)
                                <th class="text-center">{{ __('messages.edit') }}</th>
                            @endif
                            @if(Auth::user()->delete_data)
                                <th class="text-center">{{ __('messages.delete') }}</th>
                            @endif
                        </tr>
                        </thead>
                        <tbody>
                        <?php $i = 1; ?>
                        @foreach ($data['cvs'] as $user)
                            <tr>
                                <td><?=$i;?></td>
                                <td>{{ $user->design_number }}</td>
                                <td class="text-center blue-color"><a href="{{route('user.cv_details',$user->id)}}"><i
                                            class="far fa-eye"></i></a></td>
                                @if(Auth::user()->update_data)
                                    <td class="text-center blue-color"><a
                                            href="/admin-panel/users/edit/{{ $user->id }}"><i
                                                class="far fa-edit"></i></a></td>
                                @endif
                                @if(Auth::user()->delete_data)
                                    <td class="text-center blue-color">
                                        <a onclick="return confirm('Are you sure you want to delete this item?');"
                                           href="">
                                            <i class="far fa-trash-alt"></i>
                                        </a>
                                    </td>
                                @endif
                                <?php $i++; ?>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        {{--model--}}
        {{--send free balance for single user--}}
        <div id="zoomupModal" class="modal animated zoomInUp custo-zoomInUp" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('messages.send_free_balance') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round" class="feather feather-x">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>
                    <form action="{{route('users.send_balance')}}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <input required type="hidden" min="0" name="user_id" id="txt_user_id">
                            <div class="form-group mb-4">
                                <label for="plan_price">{{ __('messages.balance_value') }}</label>
                                <input required type="number" min="0" name="ammount" class="form-control">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn" data-dismiss="modal"><i
                                    class="flaticon-cancel-12"></i> {{ __('messages.cancel') }}</button>
                            <button type="submit" class="btn btn-primary">{{ __('messages.send') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        {{-- send free balance for group of users--}}
        <div id="zoomup_group_Modal" class="modal animated zoomInUp custo-zoomInUp" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('messages.send_free_balance') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round" class="feather feather-x">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>
                    <form action="{{route('users_group.send_group_balance')}}" method="post"
                          enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group mb-4">
                                <label for="plan_price">{{ __('messages.balance_value') }}</label>
                                <input required type="number" min="0" name="ammount" class="form-control">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn" data-dismiss="modal">
                                <i class="flaticon-cancel-12"></i> {{ __('messages.cancel') }}
                            </button>
                            <button type="submit" class="btn btn-primary">{{ __('messages.send') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function () {
            $(document).on('click', '#btn_send', function () {
                user_id = $(this).data('user');
                $("#txt_user_id").val(user_id);
            });
        });
    </script>
@endsection

