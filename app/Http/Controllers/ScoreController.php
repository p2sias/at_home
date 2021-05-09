<?php

namespace App\Http\Controllers;

use App\Models\UserScores;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ScoreController extends Controller
{
    /**
     * Créer une entrée de score
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id|integer',
            'session_id' => 'required|exists:sessions,id|required',
            'score' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $inputs = $request->all();

        $score = new UserScores();
        $score->user_id = $inputs['user_id'];
        $score->session_id = $inputs['session_id'];
        $score->score = $inputs['score'];
        $score->save();

        //Si la requete vient de l'API
        if ($request->wantsJson()) {
            return response()->json(
                [
                    'success' => 'score with id ' . $score->id . ' successfully created !'
                ]
            );
            //Si elle vient du web
        } else {
            return back();
        }
    }
}
