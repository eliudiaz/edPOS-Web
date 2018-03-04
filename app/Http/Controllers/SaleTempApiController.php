<?php namespace App\Http\Controllers;

use App\SaleTemp;
use Auth;
use Illuminate\Http\Request;
use Input;
use Redirect;
use Response;
use Session;
use Validator;

class SaleTempApiController extends Controller
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

        return Response::json(SaleTemp::with('item')->get());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('sale.create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return SaleTemp
     */
    public function store(Request $request)
    {
        $SaleTemps = new SaleTemp;
        $SaleTemps->item_id = $request->get('item_id');
        $SaleTemps->cost_price = $request->get('cost_price');
        $SaleTemps->selling_price = $request->get('selling_price');
        $SaleTemps->discount = $request->get('discount');
        $SaleTemps->quantity = 1;
        $SaleTemps->total_cost = $request->get('cost_price');
        $SaleTemps->total_selling = $request->get('selling_price');
        $SaleTemps->save();
        return $SaleTemps;
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
    public function update(Request $request, $id)
    {
        $SaleTemps = SaleTemp::find($id);
        $SaleTemps->quantity = $request->get('quantity');
        $SaleTemps->selling_price = $request->get('selling_price');
        $SaleTemps->total_cost = $request->get('total_cost');
        $SaleTemps->total_selling = $request->get('total_selling');
        $SaleTemps->discount = $request->get('discount');
        $SaleTemps->save();
        return $SaleTemps;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($id)
    {
        SaleTemp::destroy($id);
    }

}
