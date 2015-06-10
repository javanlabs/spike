@extends('layout')

@section('content')
<a href="{{ url('diagnose') }}">Kembali ke daftar diagnosis</a>
<form method="post" action="{{ url('diagnose') }}">
    {!! csrf_field() !!}
    <div class="row">
        <label >Name</label>
        <input class="u-full-width" type="text" name="name" value="{{ Input::old('name') }}">
    </div>
    <div class="row">
        <label >Content</label>
        <textarea name="content" id="content" cols="30" rows="50" class="u-full-width">{{ Input::old('content') }}</textarea>
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
