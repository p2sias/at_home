@extends('layout.app')

@section('custom-css')
    <link rel="stylesheet" href="{{URL::asset('css/tutorial.css')}}">
@endsection


@section('content')
    <div id="tutorial-box">
        <div id="tutorial-menu">
            <div id="tutorial-menu-box">
                <h1 id="tutorial-menu-title">Démonstration</h1>
                <div id="tutorial-menu-nav-box">
                    <ul id="tutorial-menu-nav">
                        @foreach ($tutorials as $tutorial_step)
                            <li class="@if(explode('/', Request::path())[1] == $tutorial_step->id) selected @endif"><a href="/tutorial/{{$tutorial_step->id}}">{{$tutorial_step->name}}</a><img class="step-play" src="{{URL::asset('img/play-step.png')}}" alt="play logo"></li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <div id="tutorial-content-box">
            @yield('tutorial-content')
        </div>
    </div>
@endsection
