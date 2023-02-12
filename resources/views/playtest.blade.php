@extends('common.layout')
@include('common.header')
@include('common.topmenu')

@section('content')
<div id="content">
<h1>動画自動再生テスト</h1>
<p>これはパソコン用の設定です。</p>


<iframe id="audio" width="560" height="315" src="https://www.youtube.com/embed/Hx9iZZCrQeE/<video_id>?autoplay=1" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

<p>
動画が自動再生される事を確認してください。<br>
<br>
※グーグルクロームはポリシーのせいで自動再生されません。<br>
　Firefoxをインストールして下さい。<br>
　<a href="https://www.mozilla.org/ja/firefox/windows/">【Windows 用 Mozilla Firefox をダウンロード】</a><br>
<br>
<br>
<br>
<br>
<br>



</p>

<h2>Firefoxの初期設定</h2>
<p>
Firefoxは下記からインストールして下さい。<br>
<a href="https://www.mozilla.org/ja/firefox/windows/">【Windows 用 Mozilla Firefox をダウンロード】</a><br>
インストール後「スキップ」を何度かクリックしてブラウジングをスタートさせてください。<br>
<br>
Firefoxでこのページにアクセスして動画が自動再生される事を確認して下さい。<br>
初期設定では自動再生がブロックされています。<br>
下記手順で自動再生を許可してください。<br>
<br>
<br>
<br>
<img src="/img/manual/1.jpg" style="width:900px;"><br>
<br>
<br>
<br>
自動再生を許可してください。<br>
<br>
<img src="/img/manual/2.jpg" style="width:900px;"><br>
<br>
設定後もう一度このページへアクセスして動画が自動再生される事を確認してください。<br>
<br>
この設定が完了しましたらアラート動画が再生されるようになります。<br>
</p>




</div> <!-- content -->
@stop
@include('common.footer')

