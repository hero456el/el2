@extends('common.layout')
@include('common.header')
@include('common.topmenu')

@section('content')
<div id="content">
<p>ホールリスト</p>


<table>
@foreach ($hallList as $h)
<tr>
@if($h[2]) <td>{{$h[2]["date"]}}</td>
@else <td>{{$h[3]["date"]}}</td>
@endif

@if($h[2])
<td><a href="{{ asset('/'. $h[2]["urlDate"]. '/2') }}">-昼-</a></td>
<td>{{$h["2"]["totalSpin"]}}万回転</td>
<td>{{$h["2"]["syushi"]}}万円</td>
<td>{{$h["2"]["wari"]}}%</td>
@else <td></td><td></td><td></td><td></td>
@endif
<td>　　</td>
@if($h[3])
<td><a href="{{ asset('/'. $h["3"]["urlDate"]. '/3') }}">-夜-</a></td>
<td>{{$h["3"]["totalSpin"]}}万回転</td>
<td>{{$h["3"]["syushi"]}}万円</td>
<td>{{$h["3"]["wari"]}}%</td>
@else <td></td><td></td><td></td><td></td>
@endif
</tr>
@endforeach
</table>



</div> <!-- content -->
@stop
@include('common.footer')

