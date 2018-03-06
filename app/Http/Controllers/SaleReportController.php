<?php namespace App\Http\Controllers;

use App\Sale;
use App\SaleItem;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
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
    public function index(Request $request)
    {
        return $this->filter($request);
    }

    public function filter(Request $request)
    {
        $empty = "none";
        $from = $request->get("from", $empty);
        $to = $request->get("to", $empty);

        if ($request->has('from') && $request->has('from')) {
            $from = Carbon::createFromFormat('d/m/Y', $from);
            $to = Carbon::createFromFormat('d/m/Y', $to);
            $period = 'period_custom';
        } else {
            $to = Carbon::now();
            $from = $to->copy()->startOfMonth();
            $period = 'period_monthly';

        }

        $saleReport = Sale::whereBetween('created_at', array($from, $to))->get();
        $grandTotal = SaleItem::whereBetween('created_at', array($from, $to))
            ->whereIn('sale_id', function ($query) use ($from, $to) {
                $query->select('id')
                    ->from(with(new Sale)->getTable())
                    ->whereBetween('created_at', array($from, $to))
                    ->where('status', 'active');
            })->sum('total_selling');
        $grandProfit = SaleItem::whereBetween('created_at', array($from, $to))
                ->whereIn('sale_id', function ($query) use ($from, $to) {
                    $query->select('id')
                        ->from(with(new Sale)->getTable())
                        ->whereBetween('created_at', array($from, $to))
                        ->where('status', 'active');
                })->sum('total_selling') - SaleItem::whereBetween('created_at', array($from, $to))
                ->whereIn('sale_id', function ($query) use ($from, $to) {
                    $query->select('id')
                        ->from(with(new Sale)->getTable())
                        ->whereBetween('created_at', array($from, $to))
                        ->where('status', 'active');
                })->sum('total_cost');

        $from = $from->format('d/m/Y');
        $to = $to->format('d/m/Y');

        $criteria = array('from' => $from,
            'to' => $to, 'period' => $period);
        return view('report.sale', compact('grandTotal',
            'grandProfit', 'criteria', 'saleReport'));

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
