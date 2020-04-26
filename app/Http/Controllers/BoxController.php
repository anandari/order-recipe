<?php

namespace App\Http\Controllers;

use App\Box;
use App\Recipe;
use App\BoxRecipe;
use App\Ingredients;
use App\RecipeIngredients;
use App\Http\Resources\Box as BoxResource;
use App\Http\Controllers\ApiBaseController as ApiBaseController;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BoxController extends ApiBaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return BoxResource::collection(Box::paginate(10));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            'delivery_date' => 'required|date|date_format:Y-m-d|after:tomorrow',
            'recipe_id'     => 'required|array|between:1,4',
            'recipe_id.*'   => 'integer'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
        
        /* First validate the recipe id */
        foreach( $Input['recipe_id'] as $recipe_id ){
            $ObjRecipe = Recipe::find( $recipe_id );
            if( !$ObjRecipe )
                return $this->sendError('Validation Error.', 'Recipe ID '.$recipe_id.' is incorrect');
        }

        $Box = Box::create($Input);
        /* Then add to DB */
        foreach( $Input['recipe_id'] as $recipe_id ){
            $BoxRecipe = new BoxRecipe();
            $BoxRecipe->box()->associate( $Box );
            $ObjRecipe = Recipe::find( $recipe_id );
            $BoxRecipe->recipe()->associate( $ObjRecipe );
            $BoxRecipe->save();
        }       
        
        return $this->sendResponse($Box->toArray(), 'Box created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Box  $box
     * @return \Illuminate\Http\Response
     */
    public function show(Box $box)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Box  $box
     * @return \Illuminate\Http\Response
     */
    public function edit(Box $box)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Box  $box
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Box $box)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Box  $box
     * @return \Illuminate\Http\Response
     */
    public function destroy(Box $box)
    {
        //
    }

    /**
     * Returns list of ingredients to be purchased.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getCompanyOrder(Request $request){
        $Input = $request->all();

        $validator = Validator::make($Input, [
            'delivery_date' => 'date|date_format:Y-m-d',
            'order_date'    => 'required|date|date_format:Y-m-d',
            'supplier'      => 'regex:/^[\pL\s\-]+$/u'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
        
        $OrderFromDate  = $Input['order_date'];
        $OrderToDate    = date("Y-m-d", strtotime('+7 days' , strtotime($OrderFromDate)));
        $DeliveryDate   = isset( $Input['delivery_date'] ) ? $Input['delivery_date'] : FALSE;

        /* Get boxes from order date to 7 days */
        $ObjBox         = Box::whereBetween('created_at', [$OrderFromDate, $OrderToDate]);

        /* Add delivery date if requested in the filter */
        if( $DeliveryDate ) {
            $ObjBox->whereDate('delivery_date', '=', $DeliveryDate); 
        }

        /* Get the boxes */
        $GetBoxes   = $ObjBox->with('boxrecipe')->get()->ToArray();
        
        if( !$GetBoxes )
            return $this->sendError('Empty Results.', 'There are no results matching your search criteria, please try changing filters');
        
        /* We can get this data in one single custom query 
        but since the task is to test an understanding of how ORM works, i have done it in the native way*/
        $InputSupplier   = isset( $Input['supplier'] ) ? $Input['supplier'] : FALSE;
        $arIngredients = [];
        
        foreach( $GetBoxes as $Box ){
            foreach( $Box['boxrecipe'] as $BoxDetail ){

                /* Get the recipe detail for each box with ingredients */
                $arRecipe = RecipeIngredients::with('ingredients')
                ->where('recipe_id',$BoxDetail['recipe_id'])->get()->ToArray();

                foreach( $arRecipe as $Recipe ){

                    $Supplier       = $Recipe['ingredients']['supplier'];
                    $IngredientID   = $Recipe['ingredient_id'];

                    /* Incase the ingredient is ordered from same supplier, sum up the data */
                    if( isset( $arIngredients[$Supplier][$IngredientID] ) ){
                        $arIngredients[$Supplier][$IngredientID]['amount'] += $Recipe['amount'];
                        continue;
                    }

                    $arIngredients[$Supplier][$IngredientID] = array(
                        'ingredient_id' => $IngredientID,
                        'amount'        => $Recipe['amount'],
                        'name'          => $Recipe['ingredients']['name'],
                        'measure'       => $Recipe['ingredients']['measure'],
                        'supplier'      => $Supplier,
                    );
                }
            }
        }

        if( !isset( $arIngredients ) ){
            return $this->sendError('Empty Results.', 'There are no results matching your search criteria, please try changing filters');
        }
        
        /* Filter by supplier if needed, else clean up result */
        if( $InputSupplier && isset( $arIngredients[$InputSupplier] ) ) {
            $arFinal = $arIngredients[$InputSupplier];
        }
        else{
            /* Flatten grouped array by supplier */
            foreach( $arIngredients as $SupplierName => $SupplierIngredients ){
                foreach( $SupplierIngredients as $SupplierIngredient ){
                    $arFinal[] = $SupplierIngredient;
                }
            }
        }
        
        /* Remove unwanted keys */
        $arFinal = array_values( $arFinal );
        return $this->sendResponse( $arFinal, 'Ingredients required to be ordered by company' );            
    }
}
?>