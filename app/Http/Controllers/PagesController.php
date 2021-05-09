<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{UserScores, Post, Session, TutorialStep};


class PagesController extends Controller
{

    public function homePage()
    {
        $posts = Post::latest()->take(4)->get();
        return view('home', ['some_posts' => $posts]);
    }

    public function blogPage()
    {
        $posts = Post::latest()->get();
        return view('blog', ['posts' => $posts]);
    }

    public function registerPage()
    {
        return view('auth.register');
    }

    public function rankingPage()
    {
        $scores = UserScores::orderBy('score', 'desc')->get();
        $sessions = Session::where('status', 'started')->get();

        return view('ranking', ['sessions' => $sessions, 'scores' => $scores]);
    }

    public function tutorialPage($id)
    {
        $tutorials = TutorialStep::all();
        $tutorial = TutorialStep::where('id', $id)->first();

        if (isset($tutorial)) return view('tutorial.tutorial', ['tutorial' => $tutorial, 'tutorials' => $tutorials]);
        else return redirect()->route('page.tutorial');
    }
}
