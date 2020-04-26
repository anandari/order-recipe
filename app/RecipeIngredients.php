<?php

namespace App;
use App\Recipe;
use App\Ingredients;
use Illuminate\Database\Eloquent\Model;

class RecipeIngredients extends Model
{
    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }

    public function ingredients()
    {
        return $this->belongsTo(Ingredients::class,'ingredient_id');
    }
}
?>