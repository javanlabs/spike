@extends('layout')

@section('content')
    <div class="row">
        <h1>ISDA</h1>

        <input id="search" class="u-full-width" name="search" placeholder="Cari pengkajian awal..." type="text" data-list=".list">

        <table  class="u-full-width">
            <tbody class="list">
            @foreach($items as $item)
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

    <script>
        $(document).ready(function() {
            $('#search').hideseek({
                highlight:      true,
                nodata: 'Tidak ada diagnosis sesuai pencarian Anda'
            });
        });
    </script>
@endsection
