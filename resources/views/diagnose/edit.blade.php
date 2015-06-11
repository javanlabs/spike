@extends('layout')

@section('content')
<a href="{{ url('diagnose') }}">Kembali ke daftar diagnosis</a>
<form method="post" action="{{ url('diagnose/' . $item['id']) }}">
    {!! csrf_field() !!}
    <div class="row">
        <div class="columns seven">
            <div class="row">
                <label >Diagnosis</label>
                <input class="u-full-width" type="text" name="name" value="{{ $item['name'] }}">
            </div>
            <div class="row">
                <label >Definisi</label>
                <input class="u-full-width" type="text" name="definition" value="{{ $item['definition'] }}">
            </div>
            <div class="row">
                <label >Nomor halaman dalam buku NANDA</label>
                <input class="" type="text" name="page" value="{{ $item['page'] }}">
            </div>
            <div class="row">
                <label >Content</label>
                <textarea name="content" id="content" cols="30" rows="50" class="u-full-width">{{ $item['content'] }}</textarea>
            </div>
        </div>
        <div class="columns five">
            <div class="row">
                <label >Checklist</label>
                <textarea name="checklist"   style="height: 600px" class="u-full-width">{{ $item['checklist'] }}</textarea>
            </div>
        </div>
    </div>
    <div class="row">
        <input class="button-primary" type="submit" value="Simpan">
    </div>
</form>

<script type="text/javascript">
    $(function()
    {
        $('#content').redactor({
            minHeight: 300
        });
    });
</script>
@endsection
