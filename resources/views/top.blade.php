@extends('common.layout')
@include('common.header')
@include('common.topmenu')

@section('content')
<div id="content">
<h1>ようこそ【Hero's Eye】へ!</h1>

日付：{{$hall->date}}（本日データ）<br>
ホール回転数：{{$hall->totalSpin}}万回転<br>
ホール収支：{{$hall->syushi}}万円以上<br>
割：{{$hall->wari}}％<br>
Twitter：{{$hall->twitter}}<br>

<br><br><br><br>

<h1>プレイ中</h1>
<table>
@foreach ($floor as $f)
@if($f->myplay) @foreach ($f->myplay as $d)
<tr class="{{$d->kakurituHyouka}} {{$d->dedamaHyouka}}">
<td>【{{$d->EL}}】</td>
<td>{{$f->floor}}F</td>
<td>{{$f["kisyuName"]}}</td>
<td>{{$d->daiban}}番台</td>
<td>({{$f["rate"].$f["slo"]}}{{($f["kankin"]/10)."%"}})</td>
<td>1/<span class="kakuritu">{{$d->kakuritu}}</span></td>
<td>持ちメダル<span class="dedama">{{$d->dollar_box}}{{$f["mai"]}}</span></td>
<td>{{$d->time_out}}</td>
</tr>
@endforeach @endif
@endforeach
</table><br><br><br><br>

<h1>フロア一覧</h1>
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
<td>{{$f["updateDiff"]}}分前更新</td>
</tr>
@endforeach
</table><br><br><br><br>



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
<td>{{$f["updateDiff"]}}分前更新</td>
</tr>
</table>

@foreach ($f->daiList as $d)
<?php $l = $loop->index; ?>
@if(($l+8)%8 == 0) <div class="col"> @endif
<div class="dai {{$d->kakurituHyouka}} {{$d->dedamaHyouka}}">
台番：{{$d->daiban}}<br>
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

