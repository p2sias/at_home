<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * Retourne tous les posts
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return response()->json(Post::all());
    }


    /**
     * Création d'un post
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|unique:challenges|max:255',
            'content' => 'required|string|max:2000',
            'short_desc' => 'required|string|max:255',
            'user_id' => 'required|integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $inputs = $request->all();

        $post = new Post();
        $post->title = $inputs['title'];
        $post->content = $inputs['content'];
        $post->short_desc = $inputs['short_desc'];
        $post->user_id = $inputs['user_id'];
        $post->save();

        return response()->json(
            [
                'success' => 'post ' . $post->title . ' successfully created !'
            ]
        );
    }

    /**
     * Affiche un post selon l'ID
     *
     * @param  int  $id du post
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post = Post::find($id);

        if (isset($post)) {
            return response()->json($post);
        }
    }

    /**
     * Modifier un post
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|unique:challenges|max:255',
            'content' => 'required|string|max:2000',
            'short_desc' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        } else {
            $post = Post::find($id);
            if (isset($post)) {
                $post->fill($request->all());
                $post->save();

                return response()->json(["success" => "post edited succefully"]);
            } else {
                return response()->json(["error" => "post with id " . $id . " not found"]);
            }
        }
    }

    /**
     * Récupération des 4 derniers posts
     * @return \Illuminate\Http\Response
     */
    public function getLatestPosts()
    {
        return response()->json(['posts' => Post::latest()->take(4)->get()]);
    }

    /**
     * Suppression d'un post
     *
     * @param  int  $id ID du post
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        $post = Post::find($id);
        if (isset($post)) {
            $post->delete();
            return response()->json(["success" => "post with id " . $id . " deleted"]);
        } else {
            return response()->json(["error" => "post with id " . $id . " not found"]);
        }
    }
}
