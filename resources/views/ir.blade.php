@extends('common.layout')
@include('common.header')
@include('common.topmenu')

@section('content')
<div id="content">

<h1>IR</h1>


<table>
	<tr>
		<td>IR</td>
		<td>active</td>
	</tr>
@foreach($irList as $i)
	<tr>
		<td>{{$i->ir}}</td>
		<td>{{$i->active}}</td>
		<td><a href="{{url('/ir?del='.$i->id)}}">del</a></td>
	</tr>
@endforeach
</table>
<br><br><a href="{{url('/ir?check=check')}}">check</a><br><br>

{{ Form::open(['url' => url('/ir')]) }}
	{{ Form::text('ir', null) }}
{{ Form::submit('登録') }}
{{ Form::close() }}



</div> <!-- content -->
@stop
@include('common.footer')

