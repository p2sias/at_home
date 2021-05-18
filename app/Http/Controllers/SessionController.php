<?php

namespace App\Http\Controllers;

use App\Http\Resources\SessionResource;
use App\Http\Resources\ValidationResource;
use App\Models\Log;
use Illuminate\Http\Request;
use App\Models\Session;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class SessionController extends Controller
{
    /**
     * Retourne toutes les sessions
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return response()->json(SessionResource::collection(Session::all()));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255|unique:sessions',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'max_participant' => 'required|integer',
            'status' => 'required|in:ofj,full,ended,started',
            //'main_picture' => 'string',
            'user_id' => 'required|integer|exists:users,id',
        ]);

        if ($validator->fails()) {

            return response()->json($validator->errors());
        }

        $inputs = $request->all();

        $session = new Session();
        $session->title = $inputs['title'];
        $session->start_date = $inputs['start_date'];
        $session->end_date = $inputs['end_date'];
        $session->max_participant = $inputs['max_participant']; //$inputs['max_participant'];
        $session->status = $inputs['status'];
        $session->main_picture = 'pic'; // $inputs['main_picture'];
        $session->user_id = $inputs['user_id'];
        $session->save();

        //logs
        $admin = User::find($inputs['user_id']);

        if (isset($admin)) {
            $log = new Log();
            $log->message = "Création de la session : " . $session->title;
            $log->user_id = $admin->id;
            $log->save();
        }


        return response()->json(
            [
                'success' => 'session with title ' . $session->title . ' successfully created !'
            ]
        );
    }

    public function getValidations($id)
    {
        $session = Session::find($id);

        if (isset($session)) {
            return response()->json([
                'res' => [
                    'code' => 200,
                    'data' => ValidationResource::collection($session->validations)
                ]
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        $session = Session::where('id', $id)->first();

        if (isset($session)) {
            if ($request->wantsJson()) return response()->json($session);
            else return view('showSession', ['session' => $session]);
        } else {
            if ($request->wantsJson()) return response()->json(['error' => 'session with id ' . $id . ' not found']);
            else return back();
        }
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
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'max_participant' => 'required|integer',
            'status' => 'required|in:ofj,full,ended,started',
            'main_picture' => 'required|string',
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
            $session = Session::where('id', $id)->first();
            if (isset($session)) {

                //logs
                $admin = User::find($request->input('user_id'));

                if (isset($admin)) {
                    $log = new Log();
                    $log->message = "Modification du post : " . $request->input('title');
                    $log->user_id = $admin->id;
                    $log->save();
                }

                $inputs = $request->all();
                unset($inputs['user_id']);

                $session->fill($inputs);
                $session->save();

                if ($request->wantsJson()) return response()->json(["success" => "session edited succefully"]);
                else return back();
            } else {
                if ($request->wantsJson()) return response()->json(["error" => "session with id " . $id . " not found"]);
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
        $session = Session::find($id);
        if (isset($session)) {

            $admin = User::find($request->input('user_id'));

            if (isset($admin)) {
                $log = new Log();
                $log->message = "Suppression de la session : " . $session->title;
                $log->user_id = $admin->id;
                $log->save();
            }

            $session->delete();
            return response()->json(["success" => "session with id " . $id . " deleted"]);
        } else {
            if ($request->wantsJson()) return response()->json(["error" => "session with id " . $id . " not found"]);
        }
    }
}
