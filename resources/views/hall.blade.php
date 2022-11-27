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


<p style="font-weight: bold;font-size:1.5em;">{{$hall->date}}　{{$hiru}}</p>
<a class="mae {{$notLinkBefor}}" href="{{ asset('/'. $hallLink['beforDay']. '/'.$hall->hall) }}"><span>←前日</span></a>
<a class="tugi  {{$notLinkNext}}" href="{{ asset('/'. $hallLink['nextDay']. '/'.$hall->hall) }}"><span>翌日→</span></a>

<p class="florShousai">
ホール回転数：{{$hall->totalSpin}}万回転<br>
ホール収支：{{$hall->syushi}}万円以上<br>
割：{{$hall->wari}}％<br>
Twitter：{{$hall->twitter}}<br>
</p>
<br><br><br>

<table>
@foreach ($floor as $f)
<tr>
<td><a href="{{ asset('/'. $f["urlDate"]. '/'. $f["hall"]. '/'. $f["floor"]) }}">{{$f["floor"]}}F</a></td>
<td>{{$f["kisyuName"]}}</td>
<td>{{$f["rate"].$f["slo"]}}</td>
<td>{{($f["kankin"]/10)."%"}}</td>
<td>{{round($f["totalSpin"]/1000)."千回転"}}</td>
<td>{{round($f["dedama"]/1000)."千".$f["mai"]}}</td>
<td>{{((round($f["syushi"]/1000))/10)."万円"}}</td>
<td>{{$f["wari"]."％"}}</td>
<td>G:{{$f["good"]}} VG:{{$f["veryGood"]}}</td>

</tr>
@endforeach
</table>



<h1>フロア詳細</h1>
@foreach ($floor as $f)
<table>
<tr>
<td><a href="{{ asset('/'. $f["urlDate"]. '/'. $f["hall"]. '/'. $f["floor"]) }}">{{$f["floor"]}}F</a></td>
<td>{{$f["kisyuName"]}}</td>
<td>{{$f["rate"].$f["slo"]}}</td>
<td>{{($f["kankin"]/10)."%"}}</td>
<td>{{round($f["totalSpin"]/1000)."千回転"}}</td>
<td>{{round($f["dedama"]/1000)."千".$f["mai"]}}</td>
<td>{{((round($f["syushi"]/1000))/10)."万円"}}</td>
<td>{{$f["wari"]."％"}}</td>
<td>G:{{$f["good"]}} VG:{{$f["veryGood"]}}</td>
</tr>
</table>

@foreach ($f->daiList as $d)
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




@endforeach





</div> <!-- content -->
@stop
@include('common.footer')

