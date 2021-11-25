<?php

namespace App\Http\Controllers;

use App\Models\Price_grade_product;
use Illuminate\Http\Request;

use App\Util\ResponseJson;
use App\Util\Checker;
use App\Util\Log;
use App\Models\Shop_user;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PriceGradeProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        if(isset($_GET['id'])){
            $id = $_GET['id'];
            $data = array(
                'indonesia' => 'Ditemukan',
                'english' => 'Founded',
                'data' => Price_grade_product::with('product')->with('buyer_type')->first(),
            );
            return response()->json(ResponseJson::response($data), 200);
        }else{
            $data = array(
                'status' => false,
                'indonesia' => 'Ditemukan',
                'english' => 'Founded',
                'data' => Price_grade_product::with('product')->with('buyer_type')->first(),
            );
            return response()->json(ResponseJson::response($data), 200);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Price_grade_product  $price_grade_product
     * @return \Illuminate\Http\Response
     */
    public function show(Price_grade_product $price_grade_product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Price_grade_product  $price_grade_product
     * @return \Illuminate\Http\Response
     */
    public function edit(Price_grade_product $price_grade_product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Price_grade_product  $price_grade_product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Price_grade_product $price_grade_product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Price_grade_product  $price_grade_product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Price_grade_product $price_grade_product)
    {
        //
    }
}
