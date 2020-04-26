<?php

namespace App\Http\Controllers;

use App\ingredients;
use Illuminate\Http\Request;
use App\Http\Resources\Ingredients as IngredientsResource;
use App\Http\Controllers\ApiBaseController as ApiBaseController;
use Validator;

class IngredientsController extends ApiBaseController
{
    public function __construct()
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return IngredientsResource::collection(Ingredients::paginate(10));
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
        
        /* Allow alphabets and spaces for ingredent name and supplier */
        $validator = Validator::make($Input, [
            'name'      => 'required|regex:/^[\pL\s\-]+$/u',
            'measure'   => 'required|in:g,kg,pieces',
            'supplier'  => 'required|regex:/^[\pL\s\-]+$/u'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        /* Validation for supplier against ingredient can be added if required */
        $Ingredients = Ingredients::create($Input);
        return $this->sendResponse($Ingredients->toArray(), 'Ingredient created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ingredients  $ingredients
     * @return \Illuminate\Http\Response
     */
    public function show(ingredients $ingredients)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ingredients  $ingredients
     * @return \Illuminate\Http\Response
     */
    public function edit(ingredients $ingredients)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ingredients  $ingredients
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ingredients $ingredients)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ingredients  $ingredients
     * @return \Illuminate\Http\Response
     */
    public function destroy(ingredients $ingredients)
    {
        //
    }
}
?>