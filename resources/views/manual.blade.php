@extends('common.layout')
@include('common.header')
@include('common.topmenu')

@section('content')
<div id="content">

<h1>初期設定</h1>
<p>
■ブラウザは「Firefox」を使って下さい。<br>
Chromeはポリシーで音楽の自動再生ができません。<br>
下記の場合アラートの為音楽が自動再生されます。<br>
・プレイ中のアカウントがエラーで落ちた時。<br>
・ボーナス中の台がエラーで解放されるアラート<br>
<br>
<a href="https://www.mozilla.org/ja/firefox/windows/">【Windows 用 Mozilla Firefox をダウンロード】</a><br>
インストール後「スキップ」を何度かクリックしてブラウジングをスタートさせてください。<br>
<br>
下記ページでFirefoxの初期設定を完了させてください。<br>
<a href="/playtest">動画自動再生テスト</a><br>
<br>

</p>





</div> <!-- content -->
@stop
@include('common.footer')

