@extends('app')

@section('content')
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">{{trans('item.list_items')}}</div>

            <div class="panel-body">
                {!! Form::model($search, array('route' => array('items.index'), 'method' => 'GET','class'=>'form-inline')) !!}
                {!! Form::label('code', trans('item.search_code') ) !!}
                {!! Form::text('code', Input::old('code'), array('class' => 'form-control')) !!}
                {!! Form::label('name', trans('item.search_name') ) !!}
                {!! Form::text('name', Input::old('name'), array('class' => 'form-control')) !!}
                {!! Form::label('status', trans('item.stock_status') ) !!}
                {!!Form::select('status', array(''=>'Todos', '1' => trans('item.stock_status1'),
                            '2' => trans('item.stock_status2')),Input::old('status'), array('class' => 'form-control')) !!}
                <button class="btn btn-success" type="submit">{{trans('item.search_btn')}}</button>
                <a class="btn btn-small btn-success"
                   href="{{ URL::to('items') }}">{{trans('item.clear_btn')}}</a>
                <hr/>
                <a class="btn btn-small btn-success"
                   href="{{ URL::to('items/create') }}">{{trans('item.new_item')}}</a>
                <hr/>
                @if (Session::has('message'))
                    <div class="alert alert-info">{{ Session::get('message') }}</div>
                @endif

                @if (Session::has('error'))
                    <div class="alert alert-danger">{{ Session::get('error') }}</div>
                @endif

                <table class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <td>{{trans('item.upc_ean_isbn')}}</td>
                        <td>{{trans('item.item_name')}}</td>
                        <td>{{trans('item.cost_price')}}</td>
                        <td>{{trans('item.selling_price')}}</td>
                        <td>{{trans('item.quantity')}}</td>
                        <td>{{trans('item.stock_worth')}} </td>
                        <td>&nbsp;</td>
                        <td>{{trans('item.avatar')}}</td>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($item as $value)
                        <tr>
                            <td>{{ $value->upc_ean_isbn }}</td>
                            <td>{{ $value->item_name }}</td>
                            <td>{{ number_format($value->cost_price, 2, '.', ',') }}</td>
                            <td>{{ number_format($value->selling_price, 2, '.', ',') }}</td>
                            <td><strong>{{ $value->quantity }}</strong></td>
                            <td>{{ number_format($value->quantity*$value->cost_price, 2, '.', ',')  }}</td>
                            <td>

                                <a class="btn btn-small btn-success"
                                   href="{{ URL::to('inventory/' . $value->id . '/edit') }}">{{trans('item.inventory')}}</a>
                                <a class="btn btn-small btn-info"
                                   href="{{ URL::to('items/' . $value->id . '/edit') }}">{{trans('item.edit')}}</a>
                                {!! Form::open(array('url' => 'items/' . $value->id, 'class' => 'pull-right')) !!}
                                {!! Form::hidden('_method', 'DELETE') !!}
                                {!! Form::submit(trans('item.delete'), array('class' => 'btn btn-warning')) !!}
                                {!! Form::close() !!}
                            </td>
                            <td>{!! Html::image(url() . '/images/items/' . $value->avatar, 'a picture', array('class' => 'thumb')) !!}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                           <td colspan="5"></td>
                            <td colspan="5">{{ $totalItemsWorth }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection
