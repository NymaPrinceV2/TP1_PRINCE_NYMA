<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\EquipmentResource;
use App\Models\Equipment;
use App\Models\Rental;
use App\Models\Review;
use DateTime;
use OpenApi\Attributes as OA;

class EquipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    #[OA\Get(
        path: "/api/equipments",
        summary: "Liste de tous les équipements",
        tags: ["Équipements"],
        responses: [
            new OA\Response(
                response: "200", description: "OK"
            )
        ]
    )]
    public function index()
    {
        try{
            return EquipmentResource::collection(Equipment::paginate(20))->response()->setStatusCode(OK);
        }
        catch(Exception $ex){
            abort(SERVER_ERROR, "server_error");
        }
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
    #[OA\Get(
        path: "/api/equipments/{id}",
        summary: "Afficher un équipement",
        tags: ["Équipements"],
        parameters: [
            new OA\Parameter(
                name: "id",
                description: "ID de l'équipment",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: "200", description: "OK"
            ),
            new OA\Response(
                response: "404", description: "Équipement non trouvé"
            )
        ]
    )]
    public function show(string $id)
    {
        try{
            return (new EquipmentResource(Equipment::findOrFail($id)))->response()->setStatusCode(OK);
        }
        catch(QueryException $ex){
            abort(NOT_FOUND, "invalid Id");
        }
        catch(Exception $ex){
            abort(SERVER_ERROR, "server_error");
        }
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

    #[OA\Get(
        path: '/api/equipments/{id}/popularity_indexes',
        summary: 'Obtenir l\'index de popularité d\'un équipement',
        tags: ['Équipements'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID de l\'équipement',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Succès'
            ),
            new OA\Response(
                response: 404,
                description: 'Équipement non trouvé'
            ),
            new OA\Response(
                response: 500,
                description: 'Erreur serveur'
            )
        ]
    )]
    public function getPopularityIndex($id){

        try{
            $equipment = Equipment::findOrFail($id);
        }
        catch(QueryException $ex){
            abort(NOT_FOUND, "invalid Id");
        }
        catch(Exception $ex){
            abort(SERVER_ERROR, "server_error");
        }
        $nbOfLocations = Rental::where('equipment_id', $id)->count();
        $avgRating = 0;
        $nbOfRating = 0;
        $rentalIds = Rental::where('equipment_id', $id)->pluck('id');
        foreach(Review::whereIn('rental_id', $rentalIds)->get() as $review){
            $avgRating += $review->rating;
            $nbOfRating ++;
        }
        if($nbOfRating != 0){
            $avgRating /= $nbOfRating;
        }

        try{
            if($nbOfRating != 0){
                return (response()->json(['popularityIndex' => ($nbOfLocations * POPULARITY_PONDERATION + $avgRating * RATING_PONDERATION)]))->setStatusCode(OK);
            }
            else{
                return (response()->json(['popularityIndex' => 0]))->setStatusCode(OK);
            }
        }
        catch(Exception $ex){
            abort(SERVER_ERROR, "server_error");
        }

    }

    #[OA\Get(
        path: '/api/equipments/{id}/average_rental_price/{minDate?}/{maxDate?}',
        summary: 'Obtenir le prix moyen d\'un équipement sur une période de temps prédéfinie',
        tags: ['Équipements'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID de l\'équipement',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
            new OA\Parameter(
                name: 'minDate',
                description: 'date minimal de début des locations que nous prendrons en compte dans le calcul du prix moyen de location de l\'équipement',
                in: 'path',
                required: false,
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(
                name: 'maxDate',
                description: 'date maximal de fin des locations que nous prendrons en compte dans le calcul du prix moyen de location de l\'équipement',
                in: 'path',
                required: false,
                schema: new OA\Schema(type: 'string')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Succès'
            ),
            new OA\Response(
                response: 404,
                description: 'Équipement non trouvé'
            ),
            new OA\Response(
                response: 500,
                description: 'Erreur serveur'
            )
        ]
    )]
    public function getAverageRentalPrice(Request $request, $id){
        $minDate = $request->query('minDate');
        $maxDate = $request->query('maxDate');

        if($minDate !== null && DateTime::createFromFormat('Y-m-d', $minDate) === false){
            abort(INVALID_DATA, 'invalid data');
        }

        if($maxDate !== null && DateTime::createFromFormat('Y-m-d', $maxDate) === false){
            abort(INVALID_DATA, 'invalid data');
        }

        if($minDate == null){
            $minDate = DEFAULT_MIN_DATE;
        }

        if($maxDate == null){
            $maxDate = DEFAULT_MAX_DATE;
        }

        if($minDate > $maxDate){
            abort(INVALID_DATA, 'invalid data');
        }

        try{
            Equipment::findOrFail($id);
        }
        catch(QueryException $ex){
            abort(NOT_FOUND, "invalid Id");
        }
        catch(Exception $ex){
            abort(SERVER_ERROR, "server_error");
        }

        $averageRentalPrice = Rental::where('equipment_id', '=', $id)->where('start_date', '>=', $minDate)->where('end_date', '<=', $maxDate)->avg('total_price');
        if($averageRentalPrice == null){
            $averageRentalPrice = 0;
        }

        return (response()->json(['averageRentalPrice' => $averageRentalPrice]))->setStatusCode(OK);
    }
}
