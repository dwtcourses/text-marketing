<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Text extends Model
{
	protected $guarded = [];

    public function receivers()
    {
    	return $this->hasMany('App\Receiver', 'text_id');
    }

    public function message()
    {
        return $this->belongsTo('App\Message');
    }
}
