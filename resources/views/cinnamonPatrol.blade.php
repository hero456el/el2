@extends('common.layout')
@include('common.header')
@include('common.topmenu')

@section('content')

<div id="content" class="sitContent">

@if(isset($pinch) && $pinch) <img src="{{url('/img/top2.jpg')}}" style="width:100%;">
@else <img src="{{url('/img/top3.jpg')}}" style="width:100%;">
@endif


<br><br><br>
<div class="cinnaLeft">
<h1 style="margin:0;">シナモンパトロール部隊</h1>
<table style="height: 270px; margin-left:20px;"><tr>
<td class="shinaImg"><img class="mainShina" src="{{url('/img/shina_neru.png')}}" style="width:300px;"></td>
</tr></table>
{{--<p class="shinaImg"><img class="mainShina" src="{{url('/img/shina_neru.png')}}" style="width:300px;"></p><br>--}}
<button class="serch">シナモン、台を探してくるんだ！！</button>
<button class="serchNow displayNone">もういい、シナモン戻れ！！</button>
<span class="goStop displayNone">{{$goStop}}</span>
</div>
<div class="cinnaRight">
<p class="seikaTime">aaa</p><span class="cpError"></span>
<div class="seika">
@foreach($seika as $s)
・{{$s['target']}}<br>
@endforeach
</div>
<br>
<div class="dull">★★↓↓ドゥルお願い↓↓★★</div>
</div>
<br><br>
<div class="cb"></div>
<br><br>
<h2>■シナモンに指示を出す　<span class="tukaikata">使い方</span></h2>
    {{ Form::open(['url' => url('/cpShiji'), 'class' => 'f1']) }}
    {{ Form::hidden('folder','1')}}
    <table>
    <tr>
    <td>　機種名</td>
    <td>レート</td>
    <td>ボーダー</td>
    <td>初当り</td>
    <td>　天井狙い</td>
    <td> 連狙い</td>
    <td>　ターゲット台番</td>
    <td>　NG台番追加</td>
    </tr>
    @if(!empty($floor))
    @foreach($floor as $f)
    <?php $SP = $f['cp']['type']==1? 'S': 'P'; ?>
    <tr>
    <td><label class="check-box">{{Form::checkbox('go'.$f['floor'], null, $f['cp']['go'], ['class'=>''])}} <span>{{$f['floor']}}F {{$f['cp']['kisyu']}}</span></label></td>
    <td>{{$f['rate']}}{{$SP}} {{$f['kankin']/10}}%</td>
    <td> 1/{{Form::number('border'.$f['floor'], $f['cp']['border'], ["class"=>"w38 tr", "placeholder"=>$f['cp']['border']])}}</td>
    <td> {{Form::number('hatu'.$f['floor'], $f['cp']['hatu'], ["class"=>"w38 tr"])}}回↑</td>
    <td>　 {{Form::number('tenjyo'.$f['floor'], $f['cp']['tenjyo'], ["class"=>"w38 tr", "placeholder"=>$f['cp']['tenjyoPla']])}}G↑</td>
    <td> {{Form::number('ren'.$f['floor'], $f['cp']['ren'], ["class"=>"w38 tr", "placeholder"=>$f['cp']['renPla']])}}G↓</td>
    <td>　 {{Form::text('target'.$f['floor'], $f['cp']['target'], ["class"=>"w100"])}}</td>
    <td>　{{Form::text('ng'.$f['floor'], $f['cp']['ng'], ["class"=>"w100"])}} {{ Form::hidden('ngBackup'.$f['floor'], $f['cp']['ng'])}}</td>
    </tr>
    @endforeach
    @endif
    </table>
    {{Form::submit('保存',['class'=>'not_view btn'])}}
    {{ Form::close() }}
    <br><br>

<h2>■着席boyを選ぶ</h2>
    {{ Form::open(['url' => url('/boyGo'), 'class' => 'f2']) }}
    {{ Form::hidden('folder','1')}}
    @foreach($go as $g)
    <label class="check-box">
    {{Form::checkbox($g['name'], null, $g['go'], ['class'=>'custom-control-input'])}}
    <span>{{$g['name']}}</span>　
    </label>
    @endforeach
    <br>
    {{Form::submit('保存',['class'=>'not_view btn'])}}
    {{ Form::close() }}
    <p>※着席ボーイは勝手に休んでることがあるよ。こまめにサイトを更新してね。</p>
    <br><br>


<h2>■着席boy作成</h2>
    {{ Form::open(['url' => url('/boyInsert'), 'class' => 'f3']) }}
    {{ Form::hidden('folder','1')}}
    <p>　　SID（ブラウザのIR-SESSIONを入力）</p>
    @foreach($sid as $s)
    @if($s['sid']) <span>・{{$s['name']}}</span>{{$s['sid']}}<br>
    @else <label><span>・{{$s['name']}}</span>{{Form::text($s['name'], null, ["class"=>""])}}</label><br>
    @endif
    @endforeach
    {{Form::submit('保存',['class'=>'not_view btn'])}}
    　<a href="{{url('/allSidCheck')}}">チェック</a>

    {{ Form::close() }}
    <p>※他のブラウザでログインしちゃうとSIDが消えちゃうよ。</p>
    <br><br>



<br>
<br>
<br>

</div> <!-- content -->
@stop
@include('common.footer')

