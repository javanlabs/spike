@extends('layout')

@section('content')
    <div class="row">
        <h3>Symptom</h3>
        @foreach($hierarchy as $path)
            <a href="{{ url('symptom/' . $path['id']) }}">{{ $path['name'] }}</a> &raquo;
        @endforeach
        <span>{{ $item['name'] }}</span>

        <hr/>

        <h3>Kemungkinan Diagnosis</h3>
        <table  class="u-full-width">
            <tbody class="list">
            @foreach($diagnoses as $item)
                <tr>
                    <td>
                        <a href="{{ url('diagnose/' . $item['id']) }}" data-remodal-target="modal-{{ $item['id'] }}">
                            {{ $item['name'] }}
                        </a>
                        <div class="remodal" data-remodal-id="modal-{{ $item['id'] }}">
                            <button data-remodal-action="close" class="remodal-close"></button>
                            <h3>{{ $item['name'] }}</h3>
                            <div class="remodal-body">
                                {!! $item['content'] !!}
                            </div>
                            <button data-remodal-action="cancel" class="remodal-cancel">Skip</button>
                            <button data-remodal-action="confirm" class="remodal-confirm">Tegakkan</button>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <a class="button" href="{{ url('symptom') }}">Lanjut Pengkajian Lainnya &raquo;</a>
    </div>
@endsection
