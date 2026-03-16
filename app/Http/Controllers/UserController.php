<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use App\Http\Resources\UserResource;

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
