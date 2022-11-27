@extends('common.layout')
@include('common.header')
@include('common.topmenu')

@section('content')

<div id="content">



<h1>収支</h1>

<?php
$mainasu = "";
$Pmainasu = "";
if($syushiList['diffEP']<0){$mainasu = "emp";}
if($syushiList['diffPEP']<0){$Pmainasu = "emp";}
?>
<p>ぽんちゃん<br>
{{$syushiList['totalPEP']}}EP +{{$syushiList['totalPKakutoku']}}EP<br>
 合計 <span class="syoji">{{number_format($syushiList['totalPEP'] + $syushiList['totalPKakutoku'])}}EP</span><br>
 差異 <span class="{{$Pmainasu}}">{{$syushiList['diffPEP']}}EP（{{number_format($syushiList['diffPEP']*120)}}円）</span></p>

<p>俺所持EP<br>
{{$syushiList['totalEP']}}EP +{{$syushiList['totalKakutoku']}}EP<br>
 合計 <span class="syoji">{{number_format($syushiList['totalEP'] + $syushiList['totalKakutoku'])}}EP</span><br>
 差異 <span class="{{$mainasu}}">{{$syushiList['diffEP']}}EP（{{number_format($syushiList['diffEP']*120)}}円）</span></p>
<br>
<table class="epTable">
<tr>
  <th>EL</th>
  <th>所持EP</th>
  <th>獲得EP</th>
  <th>フロア</th>
  <th>台番</th>
</tr>
@foreach($syushiList['syushiList'] as $s)
<?php
$emp = "";
if(($s["balance"]+$s["kakutoku"])<50){$emp = "emp";}
if(($s["balance"]+$s["kakutoku"])>1000){$emp = "b";}
?>
@if($s["el"]=='EL50')
<tr><td>　</td><td></td><td></td><td></td><td></td></tr>
<tr><td>----</td><td>----</td><td>----</td><td>----</td><td>----</td></tr>
<tr><td>　</td><td></td><td></td><td></td><td></td></tr>
@endif
<tr>
  <td>{{$s["el"]}}</td>
  <td class="{{$emp}}"> @if($s["sid"]){{$s["balance"]}}EP @endif</td>
  <td> @if($s["floor"])+{{$s["kakutoku"]}}EP @endif</td>
  <td> @if($s["floor"]){{$s["floor"]}}F @endif</td>
  <td> @if($s["daiban"]){{$s["daiban"]}}番 @endif</td>
</tr>
@endforeach
</table>





</div> <!-- content -->
@stop
@include('common.footer')

