<?php

namespace App\Http\Controllers;

use App\Http\Requests\CardRequest;
use App\Models\Card;
use Illuminate\Http\Request;

class CardController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function filter(Request $request)
    {
        if(!$request->access_token || $request->access_token != env('ACCESS_TOKEN')){
            abort(403, 'Invalid Access Token');
        }
        $query = Card::query();
        if(isset($request->date)){
            $query->whereDate('created_at', '=', $request->date);
        }
        if(isset($request->status)){
            if((int) $request->status == 1){
                $query->whereNull('deleted_at');
            }else if((int) $request->status == 0){
                $query->onlyTrashed();
            }
        }else{
            $query->withTrashed();
        }
        return $query->get()->map(function($item){
            return [
                "id" => $item->id,
                "title" => $item->title,
                "description" => $item->description,
                "created_at" => $item->created_at,
                "updated_at" => $item->updated_at,
                "deleted_at" => $item->deleted_at
            ];
        });

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\CardRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CardRequest $request)
    {
        
        if(!$request->id){
            $count = Card::count();
            $card = new Card();
            $card->fill($request->validated());
            $card->order = $count+1;
            return $card->save();
        }

        return Card::where('id',$request->id)->update($request->validated());



    }

    /**
     * reorder a resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function reorder(Request $request)
    {
        $orderNum = 1;
        foreach($request->order as $order){
            foreach($order['cards'] as $card){
                $card = Card::find($card);
                $card->order = $orderNum;
                $card->column_id = $order['column_id'];
                $card->save();
                $orderNum++;
            }
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Card  $card
     * @return \Illuminate\Http\Response
     */
    public function destroy(Card $card)
    {
        return $card->update(['status' => 0]);
    }
}
