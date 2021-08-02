@extends('admin.app')

@if(Route::current()->getName() == 'products.choose_to_you')
    @section('title' , __('messages.choose_to_you'))
@else
    @section('title' , __('messages.show_products'))
@endif



@section('content')
    <div id="tableSimple" class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        @if(Route::current()->getName() == 'products.choose_to_you')
                            <h4>{{ __('messages.choose_to_you') }}</h4>
                        @else
                            <h4>{{ __('messages.show_products') }}</h4>
                        @endif
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area">
                <div class="table-responsive">
                    <table id="html5-extension" class="table table-hover non-hover" style="width:100%">
                        <thead>
                        <tr>
                            <th class="text-center">Id</th>
                            <th class="text-center">{{ __('messages.publication_date') }}</th>
                            <th class="text-center">{{ __('messages.product_name') }}</th>
                            <th class="text-center">{{ __('messages.user') }}</th>
                            <th class="text-center">{{ __('messages.details') }}</th>
                            <th class="text-center">{{ __('messages.comments') }}</th>
                            @if(Auth::user()->delete_data)
                                <th class="text-center">{{ __('messages.delete') }}</th>
                            @endif
                        </tr>
                        </thead>
                        <tbody>
                        <?php $i = 1; ?>
                        @foreach ($data['products'] as $product)
                            <tr>
                                <td class="text-center"><?=$i;?></td>
                                <td class="text-center">
                                    @if( $product->publication_date != null)
                                        {{date('Y-m-d', strtotime($product->publication_date))}}
                                    @else
                                        {{ __('messages.not_publish_yet') }}
                                    @endif</td>
                                <td class="text-center">{{ $product->title }}</td>
                                <td class="text-center">
                                    <a href="{{ route('users.details', $product->user->id) }}" target="_blank">
                                        {{ $product->user->name }}
                                    </a>
                                </td>
                                <td class="text-center blue-color">
                                    <a href="{{ route('products.details', $product->id) }}"><i class="far fa-eye"></i></a>
                                </td>
                                <td class="text-center blue-color">
                                    <a href="{{ route('product.comments', $product->id) }}"><i class="far fa-eye"></i></a>
                                </td>
                                @if(Auth::user()->delete_data)
                                    <td class="text-center blue-color">
                                        <a onclick="return confirm('{{ __('messages.are_you_sure') }}');"
                                            href="{{ route('delete.product', $product->id) }}"><i
                                                class="far fa-trash-alt"></i></a></td>
                                @endif
                                <?php $i++; ?>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

