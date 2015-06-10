@extends('layout')

@section('content')
    <div class="row">
        @foreach($hierarchy as $path)
            <a href="{{ url('symptom/' . $path['id']) }}">{{ $path['name'] }}</a> &raquo;
        @endforeach
        <h1>{{ $item['name'] }}</h1>
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
