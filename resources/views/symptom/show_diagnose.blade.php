@extends('layout')

@section('content')
    <div class="row">
        <h3>Symptom</h3>
        @foreach($hierarchy as $path)
            <a href="{{ url('symptom/' . $path['id']) }}">{{ $path['name'] }}</a> &raquo;
        @endforeach
        <span>{{ $item['name'] }}</span>

        <hr/>

        <h3>Kemungkinan Diagnosis <a class="button" href="#" data-remodal-target="modal-create">Tambah</a></h3>
        <form class="remodal" data-remodal-id="modal-create" action="{{ url('symptom/' . $item['id'] . '/diagnose') }}" method="post">
            {!! csrf_field() !!}
            <button data-remodal-action="close" class="remodal-close"></button>
            <h3>Tambah Diagnosis</h3>
            <div class="remodal-body">
                {!! Form::select('diagnose_id', $availableDiagnoses, ['class' => 'u-full-width']) !!}
            </div>
            <button data-remodal-action="confirm" class="button">Batal</button>
            <button type="submit" class="button button-primary">Simpan</button>
        </form>


        <table  class="u-full-width">
            <tbody class="list">
            @foreach($diagnoses as $item)
                <tr>
                    <td>
                        <a href="{{ url('diagnose/' . $item['id']) }}" data-remodal-target="modal-{{ $item['id'] }}">
                            {{ $item['name'] }}
                        </a>
                        <form class="remodal" data-remodal-id="modal-{{ $item['id'] }}">
                            <button data-remodal-action="close" class="remodal-close"></button>
                            <h3>{{ $item['name'] }}</h3>
                            <div class="remodal-body">
                                <p>
                                    <strong>Definisi</strong>
                                    {{ $item['definition'] }}
                                </p>
                                <p>
                                    @include('symptom._checklist', ['checklist' => $item['checklist']])
                                </p>
                            </div>
                            <button data-remodal-action="confirm" class="button button-primary">Ditegakkan</button>
                            <button data-remodal-action="confirm" class="button button-negative">Dianulir</button>
                            <button data-remodal-action="confirm" class="button">Perlu Pengkajian Lebih Lanjut</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <a class="button" href="{{ url('symptom') }}">Lanjut Pengkajian Lainnya &raquo;</a>
    </div>
@endsection
