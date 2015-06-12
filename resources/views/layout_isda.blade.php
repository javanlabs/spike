@extends('layout')

@section('content')
    <nav class="navbar">
        <div class="container">
            <ul class="navbar-list">
                <li class="navbar-item"><a class="navbar-link" href="{{ url('symptom') }}">Pengkajian Awal</a></li>
                <li class="navbar-item"><a class="navbar-link" href="{{ url('symptom/printout') }}">Print Out</a></li>
            </ul>
        </div>
    </nav>

    @yield('content-isda')
@endsection
