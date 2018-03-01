<?php namespace App\Http\Controllers;

use App\Sale;
use App\SaleItem;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Redirect;

class SaleReportController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return $this->filter();
    }

    public function filter()
    {
        $empty = "none";
        $from = Input::get("from", $empty);
        $to = Input::get("to", $empty);

        if ($to != $empty && $from != $empty) {
            $from = Carbon::createFromFormat('d/m/Y', $from);
            $to = Carbon::createFromFormat('d/m/Y', $to);
            $period = 'period_custom';
        } else {
            $to = Carbon::now();
            $from = $to->copy()->addDay(-1)->startOfWeek();
            $period = 'period_weekly';

        }

        $sales = Sale::whereBetween('created_at', array($from, $to))->get();
        $salesGrandTotal = SaleItem::whereBetween('created_at', array($from, $to))->sum('total_selling');
        $salesGrandProfit = SaleItem::whereBetween('created_at', array($from, $to))->sum('total_selling') - SaleItem::whereBetween('created_at', array($from, $to))->sum('total_cost');

        return view('report.sale')
            ->with('saleReport', $sales)
            ->with('period', $period)
            ->with('from', $from->format('d-m-Y'))
            ->with('to', $to->format('d-m-Y'))
            ->with('grandTotal', $salesGrandTotal)
            ->with('grandProfit', $salesGrandProfit);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update($id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }

}
