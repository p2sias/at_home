<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use App\Models\ChallengeFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\URL;
use App\Models\User;
use Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FilesController extends Controller
{

    /**
     * Enregistrement d'une preuve en base
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @param  int  $id ID du challenge
     */
    public function postFile($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|string',
            'user_id' => 'required|integer|exists:users,id',
            'user_fileName' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['res' => [
                'code' => 500,
                'message' => $validator->errors()
            ]]);
        }

        $inputs = $request->all();

        $user = User::where('id', $inputs['user_id'])->first();
        $challenge = Challenge::find($id);

        if (isset($user) && isset($challenge)) {

            //On récupère l'image en base64 et la transforme en fichier image
            $img = Image::make($inputs['file']);
            // On récupère le type de l'image
            $type = $img->mime();
            //On recupere l'extension de l'image
            $ext = explode('/', $type)[1];

            //On génère un nom aléatoire et ajoute l'extension
            $file_name = Str::random(20) . '.' . $ext;

            //On compose le chemin de l'image
            $image_link = 'validations_files/' . $file_name;

            $challenge_file = new ChallengeFile();
            $challenge_file->link = $image_link;
            $challenge_file->user_id = $inputs['user_id'];
            $challenge_file->challenge_id = $id;
            $challenge_file->type = $type;
            $challenge_file->user_fileName = $inputs['user_fileName'];
            $challenge_file->width = $img->width();
            $challenge_file->save();

            //Sauvegarde de l'image
            $img->save(public_path('validations_files/' . $file_name));

            return response()->json(["res" => [
                'code' => 500,
                'message' => 'file save succeed ! ' . URL::asset($image_link)
            ]]);
        } else {
            return response()->json(["res" => [
                'code' => 500,
                'message' => 'file save error !'
            ]]);
        }
    }

    /**
     * Retourne les preuves d'un utilisateur
     *
     * @return \Illuminate\Http\Response
     * @param  int  $user_id ID de l'user
     */
    public function getFilesByUser($user_id)
    {
        //Récupération des preuves liées à cet user
        $files = ChallengeFile::where('user_id', $user_id)->get();
        if (isset($files)) {
            return response()->json($files);
        }
    }

    /**
     * Suppression d'un fichier
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @param  int  $file_id ID du fichier
     */
    public function deleteFile($file_id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['res' => [
                'code' => 500,
                'message' => $validator->errors()
            ]]);
        }

        $inputs = $request->all();

        $file = ChallengeFile::where('id', $file_id)->first();
        $user = User::find($inputs['user_id']);
        if (isset($user) && $file->user_id == $inputs['user_id']) {
            Storage::delete($file->link);
            $file->delete();

            return response()->json(['res' => [
                'code' => 200,
                'message' => 'file deleted'
            ]]);
        }

        return response()->json(['res' => [
            'code' => 500,
            'message' => 'error when try to delete file'
        ]]);
    }
}
