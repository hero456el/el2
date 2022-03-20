@extends('common.layout')
@include('common.header')
@include('common.topmenu')

@section('content')
<div id="content">

<p>ようこそ【Hero's Eye】へ!!!</p>



<a href="{{ asset('/dataget?f='.$floor->floor) }}">This Floor Get</a><br><br>
<a href="">Floor Up</a><br><br>
<a href="">Floor Down</a><br><br>


<h1>フロアデータ</h1>
更新時間 {{$floor->lastUpdate}}<br>
{{$floor->updateDiff}}分前更新<br>
機種 {{$floor->kisyuName}}<br>
レート {{$floor->rate}}<br>
換金率 {{$floor->kankin/10}}%<br>
階数 {{$floor->floor}}F<br>
営業日 {{$floor->date}}<br>
出玉 {{number_format($floor->dedama)}}{{$floor->mai}}<br>
回転数 {{number_format($floor->totalSpin)}}回転<br>
割 {{$floor->wari}}％<br>
総収支 {{round($floor->syushi/10000)}}万円<br>

<br><br><br><br>

@foreach ($daiList as $d)
<?php
$l = $loop->index;
?>

@if(($l+8)%8 == 0) <div class="col"> @endif
<div class="dai {{$d->kakurituHyouka}} {{$d->dedamaHyouka}}">
台番：{{$d->daiban}}<br>
初当：1/<span class="kakuritu">{{$d->kakuritu}}</span><br>
　({{$d->hatu}}/{{$d->tujyo}})<br>
出玉：<span class="dedama">{{$d->dedama}}</span><br>
{{--
BB：{{$d->data["bb_count"]}}<br>
RB：{{$d->data["rb_count"]}}<br>
着席：{{$d->data["usr_id"]}}<br>
--}}
収支：{{round($d->syushi/1000)}}K<br>
</div>
@if(($l+1)%8 == 0) </div> @endif
@endforeach







</div> <!-- content -->
@stop
@include('common.footer')



{{--
■Todo
シークレットローズ平均スルー-平均連










--}}