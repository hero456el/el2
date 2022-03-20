@section('header')
<!DOCTYPE html>
<html lang="ja">
<head>
<meta name=”robots” content=”noindex , nofollow”>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta charset="utf-8">
<title>Hero's Eye</title>
    <link rel="shortcut icon" href="{{url('/img/favicon.ico')}}" type="image/vnd.microsoft.icon">
    <link rel="icon" href="{{url('/img/favicon.ico')}}" type="image/vnd.microsoft.icon">
    <link rel="shortcut icon" type="image/x-icon" href="{{url('/img/favicon.ico')}}" />
<meta name="description" content="">
<meta name="author" content="">
<meta name="viewport" content="width=device-width, initial-scale=1">
@yield('addCSS')
<link href="{{ url('/css/main.css') }}" rel="stylesheet">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<!--[if lt IE 9]>
<script src="//cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.2/html5shiv.min.js">
<script src="//cdnjs.cloudflare.com/ajax/libs/respond.js/1.4.2/respond.min.js">
<![endif]-->
</head>

<body>
@stop