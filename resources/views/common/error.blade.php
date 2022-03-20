@extends('common.layout')
@section('addCSS')
<link href="{{{asset('/assets/css/〇〇.css')}}}" rel="stylesheet">
@stop
@include('common.header')
@include('common.topmenu')
@section('content')

<br><br><br>

<div id="content">

{{-- エラーメッセージ --}}
@if ($mess)
{{ $mess }}

@else
<p>お探しのページはありませんでした。</p>

@endif

</div>


@stop
@section('addJS')
<script type="text/javascript" src="{{{asset('/assets/js/〇〇.js')}}}"></script>
@stop
@include('common.footer')