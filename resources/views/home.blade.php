@extends('layout')

@section('content')
    <br/>
    <br/>
    <br/>
    <div class="row">
        <div style="text-align: center">
            <h3>Silakan pilih menu</h3>
            <a class="button button-primary" href="{{ url('symptom') }}">ISDA Online</a>
            <a class="button" href="{{ url('diagnose') }}">NANDA Editor</a>
            <a class="button" href="{{ url('download/isda.apk') }}">Download Aplikasi Android</a>
        </div>
    </div>
@endsection
