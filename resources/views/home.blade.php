@extends('layout')

@section('content')
    <div class="row">
        <div style="text-align: center">
            <h3>Silakan pilih menu</h3>
            <a class="button button-primary" href="{{ url('symptom') }}">ISDA Online</a>
            <a class="button button-primary" href="{{ url('diagnose') }}">NANDA Editor</a>
        </div>
    </div>
@endsection
