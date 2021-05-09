<?php

namespace App\Http\Controllers;

use App\Models\TutorialStep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TutorialStepController extends Controller
{

    /**
     * Retourne toutes les étapes de tutoriels
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(TutorialStep::all());
    }


    /**
     * Retourne une étape selon son ID
     *
     * @param int $id ID de l'étape
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     *
     */
    public function show($id, Request $request)
    {
        $tutorial = TutorialStep::find($id);

        if (isset($tutorial)) {
            return response()->json($tutorial);
        }
        return response()->json(['error' => 'tutorial with id ' . $id . ' not found']);
    }

    /**
     * Créer un tutoriel
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'content' => 'required|string',
            'order' => 'required|integer',
            'user_id' => 'required|exists:users,id'
        ]);

        if ($validator->fails()) {

            return response()->json($validator->errors());
        }

        $inputs = $request->all();

        $tutorial = new TutorialStep();
        $tutorial->name = $inputs['name'];
        $tutorial->content = $inputs['content'];
        $tutorial->user_id = $inputs['user_id'];
        $tutorial->order = $inputs['order'];
        $tutorial->save();


        return response()->json(
            [
                'success' => 'tutorial ' . $tutorial->name . ' successfully created !'
            ]
        );
    }


    /**
     * Modifier une étape de tutoriel
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        } else {
            $tutorial = TutorialStep::where('id', $id)->first();
            if (isset($tutorial)) {
                $tutorial->fill($request->all());
                $tutorial->save();

                return response()->json(["success" => "tutorial edited succefully"]);
            } else {
                return response()->json(["error" => "tutorial with id " . $id . " not found"]);
            }
        }
    }

    /**
     * Suppression d'une étape de tutoriel
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        $tutorial = TutorialStep::find($id);
        if (isset($tutorial)) {
            $tutorial->delete();
            return response()->json(["success" => "tutorial with id " . $id . " deleted"]);
        }
        return response()->json(["error" => "tutorial with id " . $id . " not found"]);
    }
}
