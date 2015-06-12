<!DOCTYPE html>
<html lang="en">
<head>

    <!-- Basic Page Needs
    –––––––––––––––––––––––––––––––––––––––––––––––––– -->
    <meta charset="utf-8">
    <title>ISDA NANDA Online</title>
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Mobile Specific Metas
    –––––––––––––––––––––––––––––––––––––––––––––––––– -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- FONT
    –––––––––––––––––––––––––––––––––––––––––––––––––– -->
    <link href="//fonts.googleapis.com/css?family=Raleway:400,300,600" rel="stylesheet" type="text/css">

    <!-- CSS
    –––––––––––––––––––––––––––––––––––––––––––––––––– -->
    <link rel="stylesheet" href="{{ asset('css/normalize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/skeleton.css') }}">
    <link rel="stylesheet" href="{{ asset('redactor/redactor.css') }}" />
    <link rel="stylesheet" href="{{ asset('remodal/remodal.css') }}" />
    <link rel="stylesheet" href="{{ asset('remodal/remodal-default-theme.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
    <script src="{{ asset('redactor/redactor.min.js') }}"></script>
    <script src="{{ asset('hideseek/jquery.hideseek.min.js') }}"></script>
    <script src="{{ asset('remodal/remodal.min.js') }}"></script>

    <!-- Favicon
    –––––––––––––––––––––––––––––––––––––––––––––––––– -->
    <link rel="icon" type="image/png" href="images/favicon.png">

</head>
<body>

<!-- Primary Page Layout
–––––––––––––––––––––––––––––––––––––––––––––––––– -->
@yield('content')


<!-- End Document
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
</body>
</html>
