@extends('common.layout')
@include('common.header')
@include('common.topmenu')

@section('content')

<div id="content" class="sitContent">

@if(isset($pinch) && $pinch) <img src="{{url('/img/top2.jpg')}}" style="width:100%;">
@else <img src="{{url('/img/top3.jpg')}}" style="width:100%;">
@endif


<br>
<h1>シナモンパトロール部隊</h1>
<table style="height: 270px; margin-left:20px;"><tr>
<td class="shinaImg"><img class="mainShina" src="{{url('/img/shina_neru.png')}}" style="width:300px;"></td>
</tr></table>
{{--<p class="shinaImg"><img class="mainShina" src="{{url('/img/shina_neru.png')}}" style="width:300px;"></p><br>--}}
<button class="serch">シナモン、台を探してくるんだ！！</button>
<button class="serchNow displayNone">もういい、シナモン戻れ！！</button>
<br><br>

<h2>■シナモンに指示を出す</h2>
    {{ Form::open(['url' => url('/apiPatFloor'), 'class' => 'f1']) }}
    {{ Form::hidden('folder','1')}}
    <table>
    <tr>
    <td>　機種名</td>
    <td>レート</td>
    <td>　ボーダー</td>
    <td>　ターゲット台番</td>
    <td>　NG台番追加</td>
    </tr>
    @for ($i=1; $i<=15; $i++)
    <tr>
    <td><label class="check-box">{{Form::checkbox('patFloor', $i, false, ['class'=>''])}} <span>{{$i}}F kisyuName</span></label></td>
    <td>20S75%</td>
    <td>　 1/{{Form::number('border'.$i, null, ["class"=>"w50"])}}</td>
    <td>　{{Form::text('target'.$i, null, ["class"=>"w120"])}}</td>
    <td>　{{Form::text('ng'.$i, null, ["class"=>"w120"])}}</td>
    </tr>
    @endfor
    </table>
    {{Form::submit('保存',['class'=>'not_view btn'])}}
    {{ Form::close() }}
    <br><br>


<h2>■着席boyを選ぶ</h2>
    {{ Form::open(['url' => url('/apiPatBoy'), 'class' => 'f2']) }}
    {{ Form::hidden('folder','1')}}
    @for ($i=1; $i<=15; $i++)
    <label class="check-box">
    {{Form::checkbox('patBoy', $i, false, ['class'=>'custom-control-input'])}}
    <span>EL{{$i}}</span>　
    </label>
    @endfor
    <br>
    {{Form::submit('保存',['class'=>'not_view btn'])}}
    {{ Form::close() }}
    <br><br>


<h2>■着席boy作成</h2>
    {{ Form::open(['url' => url('/apiPatBoyId'), 'class' => 'f3']) }}
    {{ Form::hidden('folder','1')}}
    <p>　　SID（ブラウザのクッキーを入力）</p>
    @for ($i=1; $i<=15; $i++)
    <label><span>・EL{{$i}}</span>{{Form::text('patSid'.$i, null, ["class"=>""])}}</label><br>
    @endfor
    {{Form::submit('保存',['class'=>'not_view btn'])}}
    {{ Form::close() }}
    <br><br>


<?php var_dump($test);?>


<br>
<br>
<br>

</div> <!-- content -->
@stop
@include('common.footer')

