@extends('layout.app')

@section('content')
<div class="container mt-5">
    <div class="alert alert-info">
        Selamat datang, <strong>{{ auth()->user()->name }}</strong>!
    </div>
</div>
@endsection
