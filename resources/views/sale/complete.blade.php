@extends('app')
@section('content')
    {!! Html::script('js/angular.min.js', array('type' => 'text/javascript')) !!}
    {!! Html::script('js/jspdf.min.js', array('type' => 'text/javascript')) !!}
    {!! Html::script('js/numbers.js', array('type' => 'text/javascript')) !!}
    {!! Html::script('js/app.js', array('type' => 'text/javascript')) !!}
    <style>
        table td {
            border-top: none !important;
        }
    </style>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12" style="text-align:center">
                TutaPOS - Tuta Point of Sale
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                {{trans('sale.customer')}}: {{ $sales->customer->name}}<br/>
                {{trans('sale.sale_id')}}: SALE{{$saleItemsData->sale_id}}<br/>
                {{trans('sale.employee')}}: {{$sales->user->name}}<br/>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table">
                        <tr>
                            <td>{{trans('sale.item')}}</td>
                            <td>{{trans('sale.price')}}</td>
                            <td>{{trans('sale.qty')}}</td>
                            <td>{{trans('sale.total')}}</td>
                        </tr>
                        @foreach($saleItems as $value)
                            <tr>
                                <td>{{$value->item->item_name}}</td>
                                <td>{{$value->selling_price-$value->discount}}</td>
                                <td>{{$value->quantity}}</td>
                                <td>{{$value->total_selling}}</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                {{trans('sale.payment_type')}}: {{$sales->payment_type}}
            </div>
        </div>
        <hr class="hidden-print"/>
        <div class="row">
            <div class="col-md-8">
                &nbsp;
            </div>
            <div class="col-md-2">
                <button type="button" onclick="printInvoice()"
                        class="btn btn-info pull-right hidden-print">{{trans('sale.print')}}</button>
            </div>
            <div class="col-md-2">
                <a href="{{ url('/sales') }}" type="button"
                   class="btn btn-info pull-right hidden-print">{{trans('sale.new_sale')}}</a>
            </div>
        </div>
    </div>
    <script>
        var invoice ={!! $invoice_json !!};

        function printInvoice() {
            var pdf = new jsPDF('p', 'pt', 'letter');
            pdf.setFontSize(10);
            pdf.setFillColor(0);
            pdf.text(invoice.created_at, 60, 50);
            pdf.text(invoice.customer.name, 60, 65);
            pdf.text(invoice.customer.address, 60, 80);
            pdf.text(invoice.customer.code, 390, 80);

            var x = 110;
            invoice.items.forEach((i) => {
                pdf.text(`${i.quantity}`, 40, x);
                pdf.setFontSize(8);
                pdf.text(i.description, 100, x);
                pdf.setFontSize(10);
                pdf.text(`${i.unit_price}`, 480, x);
                pdf.text(`${i.subtotal}`, 540, x);
                x = x + 15;
            });

            var lettersTotal=numeroALetras(invoice.total, {
                plural: 'QUETZALES',
                singular: 'QUETZAL',
                centPlural: 'CENTAVOS',
                centSingular: 'CENTAVO'
            });

            pdf.setFontSize(8);
            pdf.text(lettersTotal, 100, 355);
            pdf.setFontSize(10);
            pdf.text(`${invoice.total}`, 540, 355);
            pdf.autoPrint();
            pdf.output("dataurlnewwindow");
        }
    </script>
@endsection