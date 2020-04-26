<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Box extends Model
{
    protected $fillable = ['delivery_date'];
    public function boxrecipe()
    {
        return $this->hasMany(BoxRecipe::class,'boxes_id');
    }
}
?>