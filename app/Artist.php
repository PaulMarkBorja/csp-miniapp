<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Artist extends Model
{
    protected $guarded = ['id'];

    public function songs()
    {
        return $this->hasMany('App\Song');
    }
}
