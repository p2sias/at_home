<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Http\Resources\UserResource;

class LoginController extends Controller
{

    /**
     * Authentifier un utilisateur
     * @param Request data envoyées par une requete POST
     */
    public function authenticate(Request $request)
    {
        //Validation des inputs
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        //SI validation échoue, on retournes les champs erronés
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        //On place tous les champs dans une variable input
        $input = $request->all();

        //On récupère l'user concené
        $user = User::where('email', $input['email'])->first();

        //Si l'utilisateur n'existe pas
        if (!isset($user)) return response()->json(["res" => [
            "code" => 401,
            "message" => "identifiants incorrects"
        ]]);

        // On check la validité du mot de passe avec celui en base
        if (Hash::check($input['password'], $user->password)) {
            // création d'un nouveau token
            $new_token = Str::random(50);

            //Stockage du token en base et sauvegarde de l'user
            $user->api_token = $new_token;
            $user->save();

            //On retourne l'user connecté
            return response()->json(["res" => [
                "code" => 200,
                "data" => new UserResource($user)
            ]]);
        } else return response()->json(["res" => [
            "code" => 401,
            "message" => "identifiants incorrects"
        ]]);
    }

    public function logout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $inputs = $request->all();

        $user = User::where('id', $inputs['user_id'])->first();

        if (!isset($user)) return response()->json(["res" => [
            "code" => 404,
            "message" => "utilisateur inconnu"
        ]]);

        $user->api_token = null;
        $user->save();

        return response()->json(["res" => [
            'code' => 200
        ]]);
    }
}
