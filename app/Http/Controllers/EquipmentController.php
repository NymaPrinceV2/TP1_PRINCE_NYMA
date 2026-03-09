<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\EquipmentResource;
use App\Models\Equipment;
use App\Models\Rental;
use App\Models\Review;

class EquipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return EquipmentResource::collection(Equipment::paginate(20))->response()->setStatusCode(200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return (new EquipmentResource(Equipment::find($id)))->response()->setStatusCode(200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function getPopularityIndex($id){
        define('POPULARITY_PONDERATION', 0.6);
        define('RATING_PONDERATION', 0.4);
        $equipment = Equipment::findOrFail($id);
        $nbOfLocations = Rental::where('equipment_id', $id)->count();
        $avgRating = 0;
        $nbOfRating = 0;
        foreach(Review::where('equipment_id', $equipment->id)->get() as $review){
            $avgRating += $review->rating;
            $nbOfRating ++;
        }
        if($nbOfRating != 0){
            $avgRating /= $nbOfRating;
        }
        return (response()->json(['popularityIndex' => ($nbOfLocations * POPULARITY_PONDERATION + $avgRating * RATING_PONDERATION)]))->setStatusCode(200);

    }
}
