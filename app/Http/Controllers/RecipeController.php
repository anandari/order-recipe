<?php

namespace App\Http\Controllers;

use App\Recipe;
use App\Ingredients;
use App\RecipeIngredients;
use Illuminate\Http\Request;
use App\Http\Resources\Recipe as RecipeResource;
use App\Http\Controllers\ApiBaseController as ApiBaseController;
use Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RecipeController extends ApiBaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return RecipeResource::collection(Recipe::paginate(10));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $Input = $request->all();
        
        $validator = Validator::make($Input, [
            'name'          => 'required|regex:/^[\pL\s\-]+$/u',
            'description'   => 'required',
            'ingredients'   => 'required|distinct|array'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }       
        
        /* First validate the ingredients */
        foreach( $Input['ingredients'] as $Ingredient => $amount ){
            try {
                $ObjIngredients = Ingredients::where('name', '=', $Ingredient)->firstOrFail();
            } catch (ModelNotFoundException $ex) {
                return $this->sendError('Validation Error.', 'Ingredient '.$Ingredient.' doesn\'t exist');
            }
            
            if ( filter_var($amount, FILTER_VALIDATE_INT) === false ){
                return $this->sendError('Validation Error.', 'Ingredient '.$Ingredient.' has invalid amount');
            }
        }

        $Recipe = Recipe::create($Input);
        
        /* Then add to DB */
        foreach( $Input['ingredients'] as $Ingredient => $amount ){
            $ObjIngredients = Ingredients::where('name', '=', $Ingredient)->firstOrFail();
            $NewIngredientForReceipt = new RecipeIngredients();
            $NewIngredientForReceipt->recipe()->associate( $Recipe );
            $NewIngredientForReceipt->ingredients()->associate( $ObjIngredients );
            $NewIngredientForReceipt->amount = $amount;
            $NewIngredientForReceipt->save();
        }
        
        return $this->sendResponse($Recipe->toArray(), 'Recipe created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Recipe  $recipe
     * @return \Illuminate\Http\Response
     */
    public function show(Recipe $recipe)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Recipe  $recipe
     * @return \Illuminate\Http\Response
     */
    public function edit(Recipe $recipe)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Recipe  $recipe
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Recipe $recipe)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Recipe  $recipe
     * @return \Illuminate\Http\Response
     */
    public function destroy(Recipe $recipe)
    {
        //
    }
}
