<?php

namespace App\Http\Controllers;

use App\Models\Session;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\UserResource;
use App\Models\Challenge;
use App\Models\ChallengeFile;
use App\Models\ChallengeUser;
use App\Models\UserScores;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Retourne l'ensemble des utilisateurs
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return response()->json(User::all());
    }

    /**
     * Inscription d'un utilisateur
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'pseudo' => 'required|string|unique:users|max:255|min:8',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|max:255|min:8',
            'birthday' => 'required|date',
            'phone' => 'required|string|min:10',
            'address' => 'required|string|max:255',
            'postal_code' => 'required|string|max:5|min:5',
            'city' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $inputs = $request->all();

        $user = new User();
        $user->name = $inputs['name'];
        $user->surname = $inputs['surname'];
        $user->pseudo = $inputs['pseudo'];
        $user->email = $inputs['email'];
        $user->password = Hash::make($inputs['password']);
        $user->birthday = $inputs['birthday'];
        $user->phone = $inputs['phone'];
        $user->auth_level = 1;
        $user->address = $inputs['address'];
        $user->postal_code = $inputs['postal_code'];
        $user->city = $inputs['city'];
        $user->save();

        return response()->json(
            [
                'success' => 'user with email ' . $user->email . ' successfully created !'
            ]
        );
    }

    /**
     * Affiche un utilisateur selon son ID
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        $user = User::where('id', $id)->first();

        if (isset($user)) {
            return response()->json(['res' => [
                'code' => 200,
                'data' => new UserResource($user)
            ]]);
        }
    }

    /**
     * Modification d'un utilisateur
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id ID de l'utilisateur
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'pseudo' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'birthday' => 'required|date',
            'phone' => 'required|string',
            'address' => 'required|string|max:255',
            'postal_code' => 'required|string|max:5|min:5',
            'city' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        } else {
            $user = User::where('id', $id)->first();
            if (isset($user)) {
                //Remplis tous les champs en base avec ceux reçus
                $user->fill($request->all());
                $user->save();

                return response()->json(["success" => "user edited succefully"]);
            } else {
                return response()->json(["error" => "user with id " . $id . " not found"]);
            }
        }
    }

    /**
     * Vérifie l'unicité de la valeur donnée (email, pseudo ou numéro de téléphone)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkUnique(Request $request)
    {
        $inputs = $request->all();

        $search_user = User::where($inputs['field'], $inputs['valueToCheck'])->first();

        if (isset($search_user) && $search_user->id != $inputs['currentUserId']) return response()->json(['exist' => true]);
        else return response()->json(['exist' => false]);
    }

    /**
     * Supression d'un utilisateur
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        $user = User::find($id);
        if (isset($user)) {
            $user->delete();
            return response()->json(["success" => "user with id " . $id . " deleted"]);
        } else {
            return response()->json(["error" => "user with id " . $id . " not found"]);
        }
    }

    /**
     * Joindre un user à une session
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function joinSession($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $inputs = $request->all();

        $session = Session::where('id', $id)->first();
        $user = User::where('id', $inputs['user_id'])->first();

        if (isset($session) && isset($user)) {
            $user->session_id = $session->id;
            $user->save();

            //Création du score de l'utilisateur en base
            $score = new UserScores();
            $score->session_id = $session->id;
            $score->user_id = $user->id;
            $score->save();

            return response()->json(["res" => [
                "code" => 200
            ]]);
        } else {
            return response()->json(["res" => [
                "code" => 404
            ]]);
        }
    }

    /**
     * Joindre un User à un challenge
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @param  int  $id ID de l'utilisateur
     */
    public function joinChallenge($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $inputs = $request->all();

        //On vérifie que l'user ne fait pas partie du challenge
        $existing = ChallengeUser::where('user_id', $inputs['user_id'])->where('challenge_id', $id)->first();
        if (isset($existing)) return response()->json(['res' => ['message' => 'challenge already joined']]);

        $user = User::where('id', $inputs['user_id'])->first();
        $challenge = Challenge::where('id', $id)->first();


        if (isset($challenge) && isset($user) && $user->session_id == $challenge->session->id) {
            $link = new ChallengeUser();
            $link->user_id = $user->id;
            $link->challenge_id = $challenge->id;
            $link->save();

            return response()->json(["res" => [
                "code" => 200
            ]]);
        } else {
            return response()->json(["res" => [
                "code" => 500
            ]]);
        }
    }


    /**
     * Quitter un challenge
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @param  int  $id ID du challenge
     */
    public function leaveChallenge($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $inputs = $request->all();

        $link = ChallengeUser::where('user_id', $inputs['user_id'])->where('challenge_id', $id)->first();
        if (isset($link)) {
            //On supprime le lien entre le challenge et l'user
            $link->delete();

            $files = ChallengeFile::where('challenge_id', $id)->where('user_id', $inputs['user_id'])->get();

            //On supprime toute ses preuves
            foreach ($files as $file) {
                Storage::delete($file->link);
                $file->delete();
            }

            return response()->json(["res" => [
                "code" => 200
            ]]);
        }

        return response()->json(["res" => [
            "code" => 404,
            'message' => 'link not found'
        ]]);
    }


    /**
     * Changer le mot de passe de l'utilisateur
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @param  int  $id ID de l'user
     */
    public function changePassword(Request $request, $id)
    {
        $inputs = $request->all();
        $user = User::find($id);
        if (isset($user)) {
            //On vérifie la validité du mot de passe
            if (Hash::check($inputs['current'], $user->password)) {
                //Hashage du nouveau mot de passe et sauvegarde en base
                $user->password = Hash::make($inputs['new']);
                $user->save();
                return response()->json(['changed' => true]);
            } else {
                return response()->json(['changed' => false]);
            }
        }
    }
}
