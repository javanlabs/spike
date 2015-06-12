@extends('layout')

@section('content')
    <nav class="navbar">
        <div class="container" style="padding: 0 10px">
            <ul class="navbar-list">
                <li class="navbar-item"><a class="navbar-link" href="{{ url('symptom') }}">Home</a></li>
                <li class="navbar-item"><a class="navbar-link" href="{{ url('symptom/printout') }}">Print Out ({{ count($appliedDiagnoses) + count($rejectedDiagnoses) }})</a></li>
                <li class="navbar-item"><a class="navbar-link" href="#" data-remodal-target="modal-new" style="color: #0A0; font-weight: bold">Mulai Baru !</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        @yield('content-isda')
    </div>

    <div class="remodal" data-remodal-id="modal-new">
        <button data-remodal-action="close" class="remodal-close"></button>
        <h3>Mulai Sesi Baru</h3>
        <div class="remodal-body">
            <p style="text-align: center">Memulai assessment dari awal, data checlist yang telah dilakukan akan dibersihkan, Anda yakin?</p>
        </div>
        <button data-remodal-action="confirm" class="button">Batal</button>
        <a href="{{ url('symptom/refresh') }}" class="button button-primary">Ya, Mulai Baru</a>
    </div>
@endsection
