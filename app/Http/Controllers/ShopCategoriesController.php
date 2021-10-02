<?php

namespace App\Http\Controllers;

use App\Models\Shop_categories;
use Illuminate\Http\Request;
use App\Util\ResponseJson;

class ShopCategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param  \App\Models\Shop_categories  $shop_categories
     * @return \Illuminate\Http\Response
     */
    public function show(Shop_categories $shop_categories)
    {
        $shop_categories = Shop_categories::orderBy('name', 'asc')->get();
        $data = array(
            'indonesia' => 'Semua Kategori Toko',
            'english' => 'All shop categories',
            'data' => array(
                'shops' => $shop_categories,
            )
        );
        return response()->json(ResponseJson::response($data), 200);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Shop_categories  $shop_categories
     * @return \Illuminate\Http\Response
     */
    public function edit(Shop_categories $shop_categories)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Shop_categories  $shop_categories
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Shop_categories $shop_categories)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Shop_categories  $shop_categories
     * @return \Illuminate\Http\Response
     */
    public function destroy(Shop_categories $shop_categories)
    {
        //
    }
}
