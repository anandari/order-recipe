<?php

namespace App;
use App\Recipe;
use App\Box;
use Illuminate\Database\Eloquent\Model;

class BoxRecipe extends Model
{
    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }

    public function box()
    {
        return $this->belongsTo(Box::class,'boxes_id');
    }
}
?>