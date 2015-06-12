@extends('layout_isda')

@section('content-isda')
    <div class="row">
        <h3>Symptom</h3>
        @foreach($hierarchy as $path)
            <a href="{{ url('symptom/' . $path['id']) }}">{{ $path['name'] }}</a> &raquo;
        @endforeach
        <span>{{ $item['name'] }}</span>

        <hr/>

        <h5>Pilih symptom berikutnya:</h5>
        <table  class="u-full-width">
            <tbody class="list">
            @foreach($children as $item)
                <tr>
                    <td>
                        <a href="{{ url('symptom/' . $item['id']) }}">
                            {{ $item['name'] }}
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
