<?php

namespace App\Http\Controllers;

use App\Http\Resources\ChallengeResource;
use App\Http\Resources\ValidationResource;
use Illuminate\Http\Request;
use App\Models\Session;
use App\Models\Challenge;
use App\Models\ChallengeFile;
use App\Models\UserScores;
use Illuminate\Support\Facades\Validator;
use App\Models\Validation;

class ValidationController extends Controller
{
    /**
     * Retourne toutes les validations
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(ValidationResource::collection(Validation::all()));
    }



    /**
     * Traiter une validation (refus ou validation)
     *
     * @param \Illuminate\Http\Request
     * @param int $id ID de la validation
     * @return \Illuminate\Http\Response
     */
    public function validateValidation($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'choice' => 'required|string',
            'comment' => 'string',
        ]);

        $inputs = $request->all();

        $validation = Validation::find($id);

        if (isset($validation)) {
            if ($validation->status != 'prog') {
                return response()->json([
                    'res' => [
                        'code' => 500,
                        'message' => 'Cette validation à déjà été traitée !'
                    ]
                ]);
            }

            //Attribution des points si acceptation
            if ($inputs['choice'] == 'valid') {
                $points = $validation->challenge->points;
                $session_id = $validation->challenge->session->id;
                $user_id = $validation->user->id;
                $userScore = UserScores::where('session_id', $session_id)->where('user_id', $user_id)->first();

                if (isset($userScore)) {
                    $userScore->score += $points;
                    $userScore->save();
                }
            }

            //Mise à jour de la validation
            if ($inputs['choice'] == 'valid' || $inputs['choice'] == 'reject') {
                //Un commentaire est nécéssaire en cas de refus
                if ($inputs['choice'] == 'reject' && !isset($inputs['comment'])) return response()->json(['res' => ['code' => 500, 'message' => 'reject need comment']]);

                //
                $validation->status = $inputs['choice'];
                if (isset($inputs['comment'])) $validation->comment = $inputs['comment'];
                $validation->save();

                return response()->json([
                    'res' => [
                        'code' => 200,
                        'message' => 'validation updated'
                    ]
                ]);
            }
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $inputs = $request->all();

        $validation = new Validation();
        $validation->challenge_id = $id;
        $validation->user_id = $inputs['user_id'];
        $validation->save();

        $files = ChallengeFile::where('challenge_id', $id)->where('user_id', $inputs['user_id'])->get();

        for ($i = 0; $i < count($files); $i++) {
            $files[$i]->validation_id = $validation->id;
            $files[$i]->save();
        }


        return response()->json(
            [
                'res' => [
                    'code' => 200,
                    'message' => 'Validation created for ' . $validation->user->pseudo
                ]
            ]
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        $validation = Validation::where('id', $id)->first();

        if (isset($validation)) {
            if ($request->wantsJson()) return response()->json($validation);
            else return view('showValidation', ['validation' => $validation]);
        } else {
            if ($request->wantsJson()) return response()->json(['error' => 'validation with id ' . $id . ' not found']);
            else return back();
        }
    }

    public function getValidationByUser(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id',
        ]);

        $inputs = $request->all();

        $validations = Validation::where('user_id', $inputs['user_id'])->get();

        if (isset($validations)) {
            return response()->json([
                'res' => [
                    'code' => 200,
                    'data' => $validations
                ]
            ]);
        }
    }

    public function getValidatingChallengesByUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $inputs = $request->all();

        $validating = [];
        $validations = Validation::where('user_id', $inputs['user_id'])->get();
        for ($i = 0; $i < count($validations); $i++) {
            array_push($validating, $validations[$i]->challenge);
        }

        return response()->json(['res' => [
            'code' => 200,
            'data' => ChallengeResource::collection($validating)
        ]]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'comment' => 'required|string|max:500',
            'status' => 'required|string|in:prog,valid,reject',
            'user_id' => 'required|integer|exists:users,id',
            'challenge_id' => 'required|integer|exists:challenges,id',
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson()) {
                return response()->json($validator->errors());
            } else {
                return back()
                    ->withErrors($validator)
                    ->withInput();
            }
        } else {
            $validation = Validation::where('id', $id)->first();
            if (isset($validation)) {
                $validation->fill($request->all());
                $validation->save();

                if ($request->wantsJson()) return response()->json(["success" => "validation edited succefully"]);
                else return back();
            } else {
                if ($request->wantsJson()) return response()->json(["error" => "validation with id " . $id . " not found"]);
                else return back();
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        $validation = Validation::find($id);
        if (isset($validation)) {
            $validation->delete();
            if ($request->wantsJson()) return response()->json(["success" => "validation with id " . $id . " deleted"]);
            else return back();
        } else {
            if ($request->wantsJson()) return response()->json(["error" => "validation with id " . $id . " not found"]);
            else return back();
        }
    }
}
