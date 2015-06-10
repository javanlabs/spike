@extends('layout')

@section('content')
    <div class="row">
        <h1>Diagnosis <a class="button" href="{{ url('diagnose/create') }}">Tambah</a></h1>

        <input id="search" class="u-full-width" name="search" placeholder="Cari diagnosis..." type="text" data-list=".list">

        <table  class="u-full-width">
            <tbody class="list">
                @foreach($items as $item)
                    <tr>
                        <td style="width: 100px">
                            <a class="button" href="{{ url('diagnose/' . $item['id']) }}">Edit</a>
                        </td>
                        <td>
                            {{ $item['name'] }}
                            @if($item['page'])
                                (halaman {{ $item['page'] }})
                            @endif
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
