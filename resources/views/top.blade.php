@extends('common.layout')
@include('common.header')
@include('common.topmenu')

@section('content')

<div id="content">

<span id="" class="foxOnly displayNone">
@if($pinch=='pinch')
{{--<audio src="{{ asset('/sound/sound1.mp3') }}" id="sound"  autoplay controls></audio>--}}
<iframe width="560" height="315" src="https://www.youtube.com/embed/qNrRnnG8glY/<video_id>?autoplay=1" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
<span id="pinch" style="display:none;">pinch</span>
@endif
@if($pinch=='notGood')
{{--<audio src="{{ asset('/sound/sound1.mp3') }}" id="sound"  autoplay controls></audio>--}}
<iframe width="560" height="315" src="https://www.youtube.com/embed/Hx9iZZCrQeE/<video_id>?autoplay=1" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
<span id="pinch" style="display:none;">pinch</span>
@endif
@if($dull[0]['mess']!='nothing')
<iframe width="560" height="315" src="https://www.youtube.com/embed/Hx9iZZCrQeE/<video_id>?autoplay=1" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
<span id="pinch" style="display:none;">pinch</span>
@endif
</span>

<h1>ようこそ【Hero's Eye】へ!</h1>



<div>
  <div><div style="
  overflow: auto;
  display: flex;
  flex-direction: column;
  justify-content: flex-end;
  align-items: center;
  width: 259px;
  background: #FFFFFF;
  border: 1px solid rgba(0, 0, 0, 0.1);
  box-shadow: -2px 10px 5px rgba(0, 0, 0, 0);
  border-radius: 10px;
  font-family: SQ Market, Helvetica, Arial, sans-serif;
  ">
  <div style="padding: 20px;">
  <a target="_blank" data-url="https://square.link/u/3Db5ewKi?src=embd" href="https://square.link/u/3Db5ewKi?src=embed" style="
    display: inline-block;
    font-size: 18px;
    line-height: 48px;
    height: 48px;
    color: #ffffff;
    min-width: 212px;
    background-color: #000000;
    text-align: center;
    box-shadow: 0 0 0 1px rgba(0,0,0,.1) inset;

  ">今すぐ支払う</a>
  </div>
</div>
</div>

  <script>
    function showCheckoutWindow(e) {
      e.preventDefault();

      const url = document.getElementById('embedded-checkout-modal-checkout-button').getAttribute('data-url');
      const title = 'Square Online Checkout';

      // Some platforms embed in an iframe, so we want to top window to calculate sizes correctly
      const topWindow = window.top ? window.top : window;

      // Fixes dual-screen position                                Most browsers          Firefox
      const dualScreenLeft = topWindow.screenLeft !==  undefined ? topWindow.screenLeft : topWindow.screenX;
      const dualScreenTop = topWindow.screenTop !==  undefined   ? topWindow.screenTop  : topWindow.screenY;

      const width = topWindow.innerWidth ? topWindow.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
      const height = topWindow.innerHeight ? topWindow.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

      const h = height * .75;
      const w = 500;

      const systemZoom = width / topWindow.screen.availWidth;
      const left = (width - w) / 2 / systemZoom + dualScreenLeft;
      const top = (height - h) / 2 / systemZoom + dualScreenTop;
      const newWindow = window.open(url, title, `scrollbars=yes, width=${w / systemZoom}, height=${h / systemZoom}, top=${top}, left=${left}`);

      if (window.focus) newWindow.focus();
    }

    // This overrides the default checkout button click handler to show the embed modal
    // instead of opening a new tab with the given link url
    document.getElementById('embedded-checkout-modal-checkout-button').addEventListener('click', function (e) {
      showCheckoutWindow(e);
    });
  </script>
</div>



<button id="aj">更新</button><span id="floorCount"></span><br>

<div style="display: inline-block;">
最終更新：{{$lastUpdate}}<br>
ホール回転数：{{$hall->totalSpin}}万回転<br>
ホール収支：{{$hall->syushi}}万円以上<br>
割：{{$hall->wari}}％<br>
Twitter：{{$hall->twitter}}<br>
</div>

<div id="news">
<p>News</p>
<ul>
<li>・VeryGood空き情報が最新になりました。</li>
<li>・台データの最終更新時刻が表示されるようになりました。</li>
<li>・変更点をNewsでお伝えするようになりました。</li>
<li>・確率のVG、badなどの色がきちんと反映されるようになりました。</li>
<li>・過去データが見やすくなりました。</li>
</ul>
</div>

<br><br>

<span class="goStop displayNone">1</span>

<span id="" class="foxOnly displayNone">
<p>★★↓↓<img class="" src="img/checkbox-check3.png" style="width: 40px;vertical-align: bottom;">ドゥルお願い↓↓★★</p>
<div class="dull">
@foreach($dull as $d)
・{{$d['mess']}}<br>
@endforeach
</div>
</span>

<br><br><br><br>
<h1>ぽんちゃんプレイ中</h1>
<?php $totalKati=0;?>
<table>
@foreach ($floor as $f)
@if($f->myplay) @foreach ($f->myplay as $d)
@if($d->EL=="EL50"||$d->EL=="EL51"||$d->EL=="EL52"||$d->EL=="EL53"||$d->EL=="EL54")
<tr class="{{$d->kakurituHyouka}} {{$d->dedamaHyouka}}">
<td>【{{$d->EL}}】</td>
<td>{{$f->floor}}F</td>
<td>{{$f["kisyuName"]}}</td>
<td>{{$d->daiban}}番台</td>
<td>({{$f["rate"].$f["slo"]}}{{($f["kankin"]/10)."%"}})</td>
<td>1/<span class="kakuritu">{{$d->kakuritu}}</span> ({{$d->hatu}})</td>
<td class="r">　<span class="dedama">{{$d->dollar_box}}{{$f["mai"]}}</span></td>
<?php $kati=$d->dollar_box*$f["rate"]*($f["kankin"]/1000); $totalKati+=$kati; ?>
<td class="r"> {{number_format($kati)}}円</td>
<?php $to=""; if($d->time_out!="30分"){$to="not30";} ?>
<td class="{{$to}}">{{$d->time_out}}</td>
</tr>
@endif
@endforeach @endif
@endforeach
</table>
<p class="kankin">　<span>{{number_format($totalKati)}}</span> 円換金！</p>
<br><br>

<h1>俺プレイ中</h1>
<?php $totalKati=0;?>
<table>
@foreach ($floor as $f)
@if($f->myplay) @foreach ($f->myplay as $d)
@if($d->EL=="EL50"||$d->EL=="EL51"||$d->EL=="EL52"||$d->EL=="EL53"||$d->EL=="EL54")
@else
<tr class="{{$d->kakurituHyouka}} {{$d->dedamaHyouka}}">
<td>【{{$d->EL}}】</td>
<td>{{$f->floor}}F</td>
<td>{{$f["kisyuName"]}}</td>
<td>{{$d->daiban}}番台</td>
<td>({{$f["rate"].$f["slo"]}}{{($f["kankin"]/10)."%"}})</td>
<td>1/<span class="kakuritu">{{$d->kakuritu}}</span> ({{$d->hatu}})</td>
<td class="r">　<span class="dedama">{{$d->dollar_box}}{{$f["mai"]}}</span></td>
<?php $kati=$d->dollar_box*$f["rate"]*($f["kankin"]/1000); $totalKati+=$kati; ?>
<td class="r"> {{number_format($kati)}}円</td>
<?php $to=""; if($d->time_out!="30分"){$to="not30";} ?>
<td class="{{$to}}">{{$d->time_out}}</td>
</tr>
@endif
@endforeach @endif
@endforeach
</table>
<p class="kankin">　<span>{{number_format($totalKati)}}</span> 円換金！</p>
<br><br>






<h1>VeryGood空き情報<span style="font-size: 0.5em;font-weight: normal;">（最新情報になりました）</span></h1>
<table>
@foreach ($floor as $f)
@if($f->akiVG) @foreach ($f->akiVG as $d)
<tr class="{{$d->kakurituHyouka}} {{$d->dedamaHyouka}}">
<td>{{$f->floor}}F</td>
<td>{{$f["kisyuName"]}}</td>
<td>{{$d->daiban}}番台</td>
<td>({{$f["rate"].$f["slo"]}}{{($f["kankin"]/10)."%"}})</td>
<td>1/<span class="kakuritu">{{$d->kakuritu}}</span></td>
<td>({{$d->hatu}}/{{$d->tujyo}})</td>
<td>出玉 <span class="dedama">{{$d->dedama}}{{$f["mai"]}}</span></td>
</tr>
@endforeach @endif
@endforeach
</table><br><br><br><br>

<h1>ドゥル</h1>
<table>
@foreach ($floor as $f)
@if($f->dull) @foreach ($f->dull as $d)
<tr class="{{$d->kakurituHyouka}} {{$d->dedamaHyouka}}">
<td>{{$f->floor}}F</td>
<td>{{$f["kisyuName"]}}</td>
<td>{{$d->daiban}}番台</td>
<td>({{$f["rate"].$f["slo"]}}{{($f["kankin"]/10)."%"}})</td>
<td>1/<span class="kakuritu">{{$d->kakuritu}}</span></td>
<td>({{$d->hatu}}/{{$d->tujyo}})</td>
<td>持メ<span class="dedama">{{$d->dollar_box}}{{$f["mai"]}}</span></td>
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
<td>G:{{$f["good"]}} VG:{{$f["veryGood"]}}</td>
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

