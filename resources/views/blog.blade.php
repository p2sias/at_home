@extends('layout.app')

@section('custom-css')
    <link rel="stylesheet" href="{{URL::asset('css/blog.css')}}" />
@endsection

@section('content')
    <div id="blog-box">
        <div id="blog-head">
            <h1>Actualités At Home A Game</h1>
            <a id="blog-register" href="register">Rejoindre l'aventure</a>
        </div>
        <div id="blog-posts">
            @foreach ($posts as $post)
                <div class="post">
                    <div class="post-image"></div>
                    <div class="post-text">
                        <a class="post-title" href="post/{{$post->id}}">{{$post->title}}</a>
                        <span class="post-shortDesc">{{$post->short_desc}}</span>
                        <span class="post-date">Posté le {{date('d/m/Y', strtotime($post->created_at))}}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
