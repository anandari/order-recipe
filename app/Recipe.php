<?php

namespace App;
use App\RecipeIngredients;
use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    protected $fillable = ['name','description'];
    public function recipeingredients()
    {
        return $this->hasMany(RecipeIngredients::class);
    }
}
?>