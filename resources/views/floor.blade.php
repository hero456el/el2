@extends('common.layout')
@include('common.header')
@include('common.topmenu')

@section('content')
<div id="content">


<?php
//var_dump($hallLink);
$hiru = '昼';
if($hall->hall==3) $hiru = '夜';
$notLinkBefor = $hallLink['beforDay']? "": "notLink";
$notLinkNext  = $hallLink['nextDay']?  "": "notLink";
?>
<p style="font-weight: bold;font-size:1.5em;"><a href="{{ asset('/'. $hall->date. '/'.$hall->hall) }}">{{$hall->date}}　{{$hiru}}</a>　{{$floor->floor}}F <a href="{{ asset('/dataget?f='.$floor->floor) }}">本日分更新</a></p>

<a class="mae {{$notLinkBefor}}" href="{{ asset('/'. $hallLink['beforDay']. '/'.$hall->hall. '/'.$floor->floor) }}"><span>←前日</span></a>
<a class="tugi  {{$notLinkNext}}" href="{{ asset('/'. $hallLink['nextDay']. '/'.$hall->hall. '/'.$floor->floor) }}"><span>翌日→</span></a>

<p class="florShousai">
{{$floor->rate}}円　{{$floor->kankin/10}}%　{{$floor->kisyuName}}　<br>
{{number_format($floor->dedama)}}{{$floor->mai}} - {{number_format($floor->totalSpin)}}回転　
{{$floor->wari}}％ 【{{round($floor->syushi/10000)}}万円】<br>
</p>
<br><br><br>


@foreach ($daiList as $d)
<?php $l = $loop->index; $tyaku=$d->tyakuseki?'着':''; ?>
@if(($l+8)%8 == 0) <div class="col"> @endif
<div class="dai {{$d->kakurituHyouka}} {{$d->dedamaHyouka}}">
台番：{{$d->daiban}} <span class="aki">{{$tyaku}}</span><br>
初当：1/<span class="kakuritu">{{$d->kakuritu}}</span><br>
　({{$d->hatu}}/{{$d->tujyo}})<br>
出玉：<span class="dedama">{{$d->dedama}}</span><br>
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