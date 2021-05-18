<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use App\Models\ChallengeFile;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\URL;
use App\Models\User;
use Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ChallengeController extends Controller
{
    /**
     * Retourne tous les challenges
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return response()->json(Challenge::all());
    }

    /**
     * Création d'un challenge
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|unique:challenges|max:255',
            'description' => 'required|string|max:2000',
            'points' => 'required|integer|max:255',
            'main_picture' => 'string|max:255',
            'session_id' => 'required|integer|exists:sessions,id',
            'user_id' => 'required|integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $inputs = $request->all();

        $challenge = new Challenge();
        $challenge->title = $inputs['title'];
        $challenge->description = $inputs['description'];
        $challenge->points = $inputs['points'];
        $challenge->main_picture = 'pic/to/pic';
        $challenge->session_id = $inputs['session_id'];
        $challenge->user_id = $inputs['user_id'];
        $challenge->save();

        //logs
        $admin = User::find($inputs['user_id']);

        if (isset($admin)) {
            $log = new Log();
            $log->message = "Création du challenge : " . $challenge->title;
            $log->user_id = $admin->id;
            $log->save();
        }



        return response()->json(
            [
                'success' => 'challenge ' . $challenge->title . ' successfully created !'
            ]
        );
    }


    /**
     * Display the specified challenge
     *
     * @param  int  Challenge ID
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        $challenge = Challenge::find($id);

        if (isset($challenge)) {
            return response()->json($challenge);
        }

        return response()->json(['error' => 'challenge with id ' . $id . ' not found']);
    }

    /**
     * Modification d'un challenge
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id ID du challenge
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'points' => 'required|integer',
            'user_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        } else {
            $challenge = Challenge::where('id', $id)->first();
            if (isset($challenge)) {

                //logs
                $admin = User::find($request->input('user_id'));

                if (isset($admin)) {
                    $log = new Log();
                    $log->message = "Modification du challenge : " . $request->input('title');
                    $log->user_id = $admin->id;
                    $log->save();
                }

                $inputs = $request->all();
                unset($inputs['user_id']);

                $challenge->fill($inputs);
                $challenge->save();



                return response()->json(["success" => "challenge edited succefully"]);
            }
            return response()->json(["error" => "challenge with id " . $id . " not found"]);
        }
    }


    /**
     * Suppression d'un challenge
     *
     * @param  int  $id ID du challenge
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        $challenge = Challenge::find($id);
        if (isset($challenge)) {

            //logs
            $admin = User::find($request->input('user_id'));

            if (isset($admin)) {
                $log = new Log();
                $log->message = "Suppression du challenge : " . $challenge->title;
                $log->user_id = $admin->id;
                $log->save();
            }

            $challenge->delete();
            return response()->json(["success" => "challenge with id " . $id . " deleted"]);
        }
        return response()->json(["error" => "challenge with id " . $id . " not found"]);
    }
}
