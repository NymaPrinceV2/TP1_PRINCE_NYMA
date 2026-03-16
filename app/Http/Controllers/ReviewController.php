<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use OpenApi\Attributes as OA;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    #[OA\Get(
        path: "/reviews/{id}",
        summary: "suppression des critiques",
        tags: ["Critiques"],
        parameters: [
            new OA\Parameter(
                name: "id",
                description: "ID de l\' utilisateur à supprimer",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: "204", description: "NO_CONTENT"
            ),
            new OA\Response(
                response: "404", description: "NOT_FOUND"
            )
        ]
    )]
    public function destroy(string $id)
    {
        try{
            $review = Review::findOrFail($id);

            $review->delete();

            return response(status: NO_CONTENT);
        }
        catch(QueryException $ex){
            abort(NOT_FOUND, "invalid Id");
        }
        catch(Exception $ex){
            abort(SERVER_ERROR, "server_error");
        }
    }
}
