<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Store;
use Illuminate\Http\Request;
use App\Exports\ProductsExport;
use App\Imports\ProductsImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\ProductsFormRequest;

class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // $this->authorize('read-products');

        return view('products.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $owners = \App\Models\User::role('car-owner')->get();
        return view('products.create', compact('owners'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $data['image'] = base64_encode(file_get_contents($imageFile->getRealPath()));
        }
        if (!$request->has('user_id')) {
            $data['user_id'] = auth()->id();
        }
        $car = Car::create($data);
        return redirect()->route('app.products.index')->with('success', 'Vehicle Added');
    }

    public function import(Request $request)
    {
        Excel::import(new ProductsImport, $request->file('csv')->store('files'));
        return redirect()->route('app.products.index')->with('success', 'Products Imported');
    }

    public function export()
    {
        return Excel::download(new ProductsExport, 'K7-Pharmacy-products-export'.date('d-m-Y').'.xlsx');
    }

    public function importView()
    {
        return view('products.import');
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Car  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Car $product)
    {
        //
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Car  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Car $product)
    {
        $owners = \App\Models\User::role('car-owner')->get();
        return view('products.edit', compact('product', 'owners'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Car  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Car $product)
    {
        $data = $request->all();
        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $data['image'] = base64_encode(file_get_contents($imageFile->getRealPath()));
        }
        $product->update($data);
        return redirect()->route('app.products.index')->with('success', 'Vehicle Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Car  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Car $product)
    {
        $product->delete();

        return back()->with('success', 'Vehicle Deleted');
    }
}
