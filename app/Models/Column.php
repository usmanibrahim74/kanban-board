<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Column extends Model
{
    use HasFactory,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title'
    ];


    public function cards(){
        return $this->hasMany(Card::class)->orderBy('order');
    }
    public static function boot() {

	    parent::boot();
	    static::deleting(function($item) {
	        $cards = Card::where('column_id',$item->id)->whereNull('deleted_at')->get();
            foreach($cards as $card){
                $card->delete();
            }
	    });
	    
	}
}
