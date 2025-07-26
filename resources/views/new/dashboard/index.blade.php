@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
    <div>
        <h1>Selamat Datang {{ Auth::user()->name }}</h1>
    </div>
@endsection