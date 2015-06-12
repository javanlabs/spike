@extends('layout_isda')

@section('content-isda')

    <div class="row">
        <strong>Print Out Assessment</strong>
        <br/>
        <br/>

        @foreach($assessment as $item)
            <?php $diagnose = \App\Models\Diagnose::find($item['diagnose_id']) ?>
            <?php unset($item['diagnose_id']) ?>
            <?php unset($item['symptom_id']) ?>
            <h4 style="margin-bottom: 5px">
                {{ $diagnose['name'] }}
                @if($item['action'] == 'apply')
                    <span class="diagnose-status diagnose-status-apply">Ditegakkan</span>
                @endif

                @if($item['action'] == 'reject')
                    <span class="diagnose-status diagnose-status-reject">Dianulir</span>
                @endif

            </h4>

            <strong>Definisi</strong><br/>
            <p>
                {{ $diagnose['definition'] }}
            </p>

            <?php unset($item['action']) ?>

            <div class="row">
                @foreach($item as $key => $val)
                    <strong>{{ ucwords(str_replace('_', ' ', $key)) }}</strong><br/>
                    @foreach($val as $check => $val)
                        {{ $check }}<br/>
                    @endforeach
                    <br/>
                @endforeach
            </div>
            <hr/>
        @endforeach
    </div>
@endsection
