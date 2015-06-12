@extends('layout_isda')

@section('content-isda')
    <div class="row">
        <h3>Symptom</h3>
        @foreach($hierarchy as $path)
            <a href="{{ url('symptom/' . $path['id']) }}">{{ $path['name'] }}</a> &raquo;
        @endforeach
        <span>{{ $item['name'] }}</span>

        <hr/>

        <h3>
            Kemungkinan Diagnosis
            @if(\Illuminate\Support\Facades\Input::has('admin'))<a class="button" href="#" data-remodal-target="modal-create">Tambah</a>@endif
        </h3>
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
            @foreach($diagnoses as $diagnose)
                <tr>
                    <td>
                        <a id="diagnose-{{$diagnose['id']}}" href="{{ url('diagnose/' . $diagnose['id']) }}" data-remodal-target="modal-{{ $diagnose['id'] }}">
                            {{ $diagnose['name'] }}
                        </a>

                        @if($diagnose['page'])
                        <small>(halaman {{ $diagnose['page'] }})</small>
                        @endif

                        @if(in_array($diagnose['id'], $appliedDiagnoses))
                            <span class="diagnose-status diagnose-status-apply">Ditegakkan</span>
                        @endif

                        @if(in_array($diagnose['id'], $rejectedDiagnoses))
                            <span class="diagnose-status diagnose-status-reject">Dianulir</span>
                        @endif

                        <form class="remodal form-assessment" data-remodal-id="modal-{{ $diagnose['id'] }}" action="{{ url('assessment') }}" method="post">
                            {!! csrf_field() !!}
                            {!! Form::hidden('diagnose_id', $diagnose['id']) !!}
                            {!! Form::hidden('symptom_id', $item['id']) !!}
                            <button data-remodal-action="close" class="remodal-close"></button>
                            <h3>{{ $diagnose['name'] }}</h3>
                            <div class="remodal-body">
                                <p>
                                    <strong>Definisi</strong>
                                    {{ $diagnose['definition'] }}
                                </p>
                                <p>
                                    @include('symptom._checklist', ['checklist' => $diagnose['checklist']])
                                </p>
                            </div>
                            <button type="submit" name="action" value="reject" class="button button-negative">Dianulir</button>
                            <button data-remodal-action="confirm" class="button">Perlu Pengkajian Lebih Lanjut</button>
                            <button type="submit" name="action" value="apply" class="button button-primary">Ditegakkan</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <a class="button" href="{{ url('symptom') }}">Lanjut Pengkajian Lainnya &raquo;</a>
    </div>

    <script>
        $(function(){
            var elem = $('#' + window.location.hash.replace('#', ''));
            if(elem) {
                $('html, body').animate({
                    scrollTop: elem.offset().top - 100
                }, 300);
            }
        });
    </script>
@endsection
