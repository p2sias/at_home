@extends('layout.tutorial')

@section('tutorial-content')
    @markdown($tutorial->content)
@endsection
