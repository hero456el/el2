@section('topmenu')
<div id="top_menu">
■トップメニュー　
<a href="{{ asset('/') }}">トップ</a><br><br>
<a href="{{ asset('/dataget') }}">エルドラから全データ取得（約4分）</a><br><br>
{{--<a href="{{ asset('/hallRefresh') }}">ホールリフレッシュ</a><br><br>--}}
<a href="{{ asset('/list') }}">ホールリスト</a><br><br>
<button id="aj">最新情報に更新</button><span id="floorCount"></span><br><br>

<img src="{{url('/img/top1.jpg')}}" style="width:700px;">

</div>




@stop

