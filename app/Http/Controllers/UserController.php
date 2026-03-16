<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use App\Http\Resources\UserResource;
use OpenApi\Attributes as OA;

class UserController extends Controller
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
    #[OA\Post(
        path: "/api/users",
        summary: "Créer un utilisateur",
        tags: ["Utilisateurs"],
        requestBody: new OA\RequestBody(
            required: true,
            content: [
                new OA\JsonContent(
                    required: ["first_name", "last_name", "email", "phone"],
                    properties: [
                        new OA\Property(property: "first_name", type: "string", example: "Joe"),
                        new OA\Property(property: "last_name", type: "string", example: "Biden"),
                        new OA\Property(property: "email", type: "string", example: "email@gmail.com"),
                        new OA\Property(property: "phone", type: "string", example: "581-100-1000"),
                    ]
                )
            ]
        ),
        responses: [
            new OA\Response(
                response: "201", description: "Album créé"
            ),
            new OA\Response(
                response: "422", description: "Données invalides"
            )
        ]
    )]
    public function store(StoreUserRequest $request)
    {

        try{
            $user = User::create($request->validated());
            return (new UserResource($user))->response()->setStatusCode(CREATED);
        }
        catch(QueryException $ex){
            abort(NOT_FOUND, "invalid Id");
        }
        catch(Exception $ex){
            abort(SERVER_ERROR, "server_error");
        }
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
    #[OA\Put(
        path: "/api/users/{id}",
        summary: "Modifier un utilisateur",
        tags: ["Utilisateurs"],
        parameters: [
            new OA\Parameter(
                name: "id",
                description: "ID de l'utilisateur à supprimer",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: [
                new OA\JsonContent(
                    required: ["first_name", "last_name", "email", "phone"],
                    properties: [
                        new OA\Property(property: "first_name", type: "string", example: "Joe"),
                        new OA\Property(property: "last_name", type: "string", example: "Biden"),
                        new OA\Property(property: "email", type: "string", example: "email@gmail.com"),
                        new OA\Property(property: "phone", type: "string", example: "581-100-1000"),
                    ]
                )
            ]
        ),
        responses: [
            new OA\Response(
                response: "201", description: "Album créé"
            ),
            new OA\Response(
                response: "422", description: "Données invalides"
            )
        ]
    )]
    public function update(Request $request, string $id)
    {
        try{
            $request->validate([
                'first_name' => 'required|max:50',
                'last_name' => 'required|max:50',
                'email' => 'required|max:50',
                'phone' => 'required|max:12',
            ]);

            $user = User::findOrFail($id);

            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->email = $request->email;
            $user->phone = $request->phone;

            $user->save();
            return (new UserResource($user))->response()->setStatusCode(OK);
        }
        catch(QueryException $ex){
            abort(NOT_FOUND, "invalid Id");
        }
        catch(Exception $ex){
            abort(SERVER_ERROR, "server_error");
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
