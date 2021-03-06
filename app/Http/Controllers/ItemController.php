<?php namespace App\Http\Controllers;

use App\Http\Requests\ItemRequest;
use App\Inventory;
use App\Item;
use Auth;
use Excel;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Image;
use Input;
use Redirect;
use Response;
use Session;
use Validator;

class ItemController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $query = (new Item)->newQuery();

        if ($request->has("code")) {
            $query->where('upc_ean_isbn', $request->get('code'));
        }
        if ($request->has("name")) {
            $query->where('item_name', 'like', '%' . $request->get('name') . '%');
        }
        if ($request->has("status")) {
            $status = $request->get("status");
            $query->where('quantity', $status == 1 ? '=' : '>', 0);
        }
        $item = $query->where('enabled', 1)
            ->get()
            ->map(function ($it) {
                $it->quantity = $it->quantity > 0 ? $it->quantity : 0;
                return $it;
            })
            ->sortBy('item_name');

        if ($request->has("export")) {
            $items = $item->map(function ($item) {
                return ['Codigo' => $item->upc_ean_isbn,
                    'Nombre' => $item->item_name,
                    'P. Costo' => $item->cost_price,
                    'P. Venta' => $item->selling_price,
                    'Inventario' => $item->quantity > 0 ? $item->quantity : 0];
            });
            return $this->downloadExcel($items);
        }

        $totalItemsWorth = $item->map(function ($value) {
            return $value->quantity <= 0 ? 0 : $value->quantity * $value->cost_price;
        })->sum();
        $search = array("code" => $request->get('code', ''),
            "name" => $request->get('name', ''),
            "status" => $request->get('status', ''));

        return view('item.index', compact('item', 'search', 'totalItemsWorth'));
    }

    /**
     *
     * @return json items list
     */
    public function listAll()
    {
        $items = Item::where('enabled', 1)->get();
        return Response::json($items);
    }

    public function downloadExcel($data)
    {
        return Excel::create('items', function ($excel) use ($data) {
            $excel->sheet('Inventario', function ($sheet) use ($data) {
                $sheet->fromArray($data);
            });

        })->download("xlsx");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('item.create');
    }

    private function itemExists($code)
    {
        try {
            Item::where('upc_ean_isbn', $code)->firstOrFail();
            return true;
        } catch (ModelNotFoundException  $e) {
            return false;
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(ItemRequest $request)
    {

        if ($this->itemExists($request->get("upc_ean_isbn"))) {
            Session::flash("error_msg", Lang::get("item.duplicated_error"));
            return Redirect::to('items/create');
        }

        $items = new Item;
        $items->upc_ean_isbn = $request->get('upc_ean_isbn');
        $items->item_name = $request->get('item_name');
        $items->size = "none";
        $items->description = $request->get('description');
        $items->cost_price = $request->get('cost_price');
        $items->selling_price = $request->get('selling_price');
        $items->quantity = $request->get('quantity');
        $items->enabled = true;
        $items->save();
        // process inventory
        if (!$request->has('quantity')) {
            $inventories = new Inventory;
            $inventories->item_id = $items->id;
            $inventories->user_id = Auth::user()->id;
            $inventories->in_out_qty = $request->get('quantity');
            $inventories->remarks = 'Manual Edit of Quantity';
            $inventories->save();
        }
        // process avatar
        $image = $request->file('avatar');
        if (!empty($image)) {
            $avatarName = 'item' . $items->id . '.' .
                $request->file('avatar')->getClientOriginalExtension();

            $request->file('avatar')->move(
                base_path() . '/public/images/items/', $avatarName
            );
            $img = Image::make(base_path() . '/public/images/items/' . $avatarName);
            $img->resize(100, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $img->save();
            $itemAvatar = Item::find($items->id);
            $itemAvatar->avatar = $avatarName;
            $itemAvatar->save();
        }
        Session::flash('message', 'You have successfully added item');
        return Redirect::to('items/create');
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
        $items = Item::find($id);
        return view('item.edit')
            ->with('item', $items);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update(ItemRequest $request, $id)
    {
        $items = Item::find($id);
        // process inventory
        $inventories = new Inventory;
        $inventories->item_id = $id;
        $inventories->user_id = Auth::user()->id;
        $inventories->in_out_qty = Input::get('quantity') - $items->quantity;
        $inventories->remarks = 'Manual Edit of Quantity';
        $inventories->save();
        // save update
        $items->upc_ean_isbn = Input::get('upc_ean_isbn');
        $items->item_name = Input::get('item_name');
        $items->size = "none"; //Input::get('size');
        $items->description = Input::get('description');
        $items->cost_price = Input::get('cost_price');
        $items->selling_price = Input::get('selling_price');
        $items->quantity = Input::get('quantity');
        $items->enabled = true;
        $items->save();
        // process avatar
        $image = $request->file('avatar');
        if (!empty($image)) {
            $avatarName = 'item' . $id . '.' .
                $request->file('avatar')->getClientOriginalExtension();

            $request->file('avatar')->move(
                base_path() . '/public/images/items/', $avatarName
            );
            $img = Image::make(base_path() . '/public/images/items/' . $avatarName);
            $img->resize(100, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $img->save();
            $itemAvatar = Item::find($id);
            $itemAvatar->avatar = $avatarName;
            $itemAvatar->save();
        }
        Session::flash('message', 'You have successfully updated item');
        return Redirect::to('items');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($id)
    {
        $item = Item::find($id);
        $item->enabled = 0;
        $item->save();

        Session::flash('message', 'You have successfully deleted item');
        return Redirect::to('items');
    }

}
