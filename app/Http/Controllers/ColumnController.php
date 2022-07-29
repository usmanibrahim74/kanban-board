<?php

namespace App\Http\Controllers;

use App\Http\Requests\ColumnRequest;
use App\Models\Column;

class ColumnController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        return Column::with(['cards'])->get();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Card  $card
     * @return \Illuminate\Http\Response
     */
    public function show($id){
        return Column::find($id);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\ColumnRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ColumnRequest $request){
        
        if($request->id){
            return Column::where('id',$request->id)->update($request->validated());
        }else{
            return Column::create($request->validated());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Card  $card
     * @return \Illuminate\Http\Response
     */
    public function destory($id){
        return Column::find($id)->delete();
    }
}
