<?php

namespace App\Http\Resources;
use App\RecipeIngredients;
use Illuminate\Http\Resources\Json\JsonResource;

class Recipe extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $arIngredientDetail = RecipeIngredients::with('ingredients')->where('recipe_id',$this->id)->get();
        $arDetail = $arDetails = [];
        foreach( $arIngredientDetail as $arIngredient ){
            $arDetail['ingredient_id']          = $arIngredient['ingredients']['id'];
            $arDetail['ingredient_name']        = $arIngredient['ingredients']['name'];
            $arDetail['ingredient_measure']     = $arIngredient['ingredients']['measure'];
            $arDetail['ingredient_supplier']    = $arIngredient['ingredients']['supplier'];
            $arDetail['amount']                 = $arIngredient['amount'];
            $arDetails[]                        = $arDetail;
        }
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'description'   => $this->description,
            'detail'        => $arDetails,
            'created_at'    => (string) $this->created_at,
            'updated_at'    => (string) $this->updated_at
        ];
    }
}
