@extends('common.layout')
@include('common.header')
@include('common.topmenu')

@section('content')
<div id="content">
<p>ようこそ【Hero's Eye】へ!</p>


{{--
<iframe class="ffmove displayNone" id="audio" width="560" height="315" src="https://www.youtube.com/embed/Hx9iZZCrQeE/<video_id>?autoplay=1" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
https://www.youtube.com/embed/Hx9iZZCrQeE/<video_id>?autoplay=1
--}}


<iframe id="audio2" width="560" height="315" src="https://www.youtube.com/embed/Hx9iZZCrQeE/<video_id>?enablejsapi=1" frameborder="0" allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

<div id="ytplay">再生</div>



<p class="zanTime displayNone">80</p>


<h1>topページ</h1>
<?php
$c = [];
$c[] = ['aa'=>'end'];
$a = json_encode($c);
var_dump($a[0]);

?>
<br><br><br><br><br>
<?php var_dump($test1);?>
<br><br><br><br><br>
<?php var_dump($test2);?>
<br><br><br>
<?php var_dump(asset('/storage/logs/laravel.log'));?>
<a href="{{ asset('/storage/logs/laravel.log') }}">log</a>


<a href="{{ asset('/daidata/today/2') }}">West（昼の部）</a><br><br>
<a href="{{ asset('/daidata/today/3') }}">East（夜の部）</a><br><br>


<a href="{{ asset('/dataget') }}">エルドラから全データ取得（約10分）</a><br><br>





</div> <!-- content -->
@stop
@include('common.footer')

