@extends('app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">{{trans('report-sale.reports')}}
                        - {{trans('report-sale.sales_report')}}</div>

                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-10">
                                <div class="well well-sm"><strong>{{trans('report-sale.title_info')}}:</strong>
                                    {{trans('report-sale.'.$period)." ( $from - $to )"}}</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="well well-sm"><strong>{{trans('report-sale.grand_total')}}:
                                        Q.{{$grandTotal}}</strong></div>
                            </div>
                            <div class="col-md-4">
                                <div class="well well-sm"><strong>{{trans('report-sale.grand_profit')}}:
                                        Q.{{$grandProfit}}</strong>
                                </div>
                            </div>
                        </div>
                        <form class="form-inline" method="get" action="/reports/sales-filter">
                            {!! Form::label('from', trans('report-sale.date_from') .' *') !!}
                            <div class="input-group date" data-provide="datepicker" data-date-format="dd/m/yyyy">

                                {!! Form::text('from', Input::old('from'), array('class' => 'form-control','value'=>$from)) !!}
                                <span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
                            </div>
                            {!! Form::label('to', trans('report-sale.date_to') .' *') !!}
                            <div class="input-group date" data-provide="datepicker" data-date-format="dd/mm/yyyy">
                                {!! Form::text('to', Input::old('to'), array('class' => 'form-control','value'=>$to)) !!}
                                <span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
                            </div>
                            <button class="btn btn-success" type="submit">Filtrar</button>
                        </form>
                        <br>
                        <div class="span6">
                        <table class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <td>{{trans('report-sale.sale_id')}}</td>
                                <td>{{trans('report-sale.date')}}</td>
                                <td>{{trans('report-sale.items_purchased')}}</td>
                                <td>{{trans('report-sale.sold_by')}}</td>
                                <td>{{trans('report-sale.sold_to')}}</td>
                                <td>{{trans('report-sale.total')}}</td>
                                <td>{{trans('report-sale.profit')}}</td>
                                <td>{{trans('report-sale.payment_type')}}</td>
                                <td>{{trans('report-sale.comments')}}</td>
                                <td>&nbsp;</td>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($saleReport as $value)
                                <tr>
                                    <td>{{ $value->id }}</td>
                                    <td>{{ Carbon\Carbon::parse($value->created_at)->format('d-m-Y') }}</td>
                                    <td>{{DB::table('sale_items')->where('sale_id', $value->id)->sum('quantity')}}</td>
                                    <td>{{ $value->user->name }}</td>
                                    <td>{{ $value->customer->name }}</td>
                                    <td>
                                        ${{DB::table('sale_items')->where('sale_id', $value->id)->sum('total_selling')}}</td>
                                    <td>{{DB::table('sale_items')->where('sale_id', $value->id)->sum('total_selling') - DB::table('sale_items')->where('sale_id', $value->id)->sum('total_cost')}}</td>
                                    <td>{{ $value->payment_type }}</td>
                                    <td>{{ $value->comments }}</td>
                                    <td>
                                        <a class="btn btn-small btn-info" data-toggle="collapse"
                                           href="#detailedSales{{ $value->id }}" aria-expanded="false"
                                           aria-controls="detailedReceivings">
                                            {{trans('report-sale.detail')}}</a>
                                    </td>
                                </tr>

                                <tr class="collapse" id="detailedSales{{ $value->id }}">
                                    <td colspan="10">
                                        <table class="table">
                                            <tr>
                                                <td>{{trans('report-sale.item_id')}}</td>
                                                <td>{{trans('report-sale.item_name')}}</td>
                                                <td>{{trans('report-sale.quantity_purchase')}}</td>
                                                <td>{{trans('report-sale.total')}}</td>
                                                <td>{{trans('report-sale.profit')}}</td>
                                            </tr>
                                            @foreach(ReportSalesDetailed::sale_detailed($value->id) as $SaleDetailed)
                                                <tr>
                                                    <td>{{ $SaleDetailed->item_id }}</td>
                                                    <td>{{ $SaleDetailed->item->item_name }}</td>
                                                    <td>{{ $SaleDetailed->quantity }}</td>
                                                    <td>{{ $SaleDetailed->selling_price * $SaleDetailed->quantity}}</td>
                                                    <td>{{ ($SaleDetailed->quantity * $SaleDetailed->selling_price) - ($SaleDetailed->quantity * $SaleDetailed->cost_price)}}</td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    </td>
                                </tr>

                            @endforeach
                            </tbody>
                        </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection