<?php

namespace App;
use App\RecipeIngredients;
use Illuminate\Database\Eloquent\Model;

class ingredients extends Model
{
    protected $fillable = ['name','measure','supplier'];
    public function recipeingredients()
    {
        return $this->hasMany(RecipeIngredients::class);
    }
}
?>