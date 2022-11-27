@section('topmenu')


@if(isset($pinch) && $pinch=="pinch") <img class="topImge" src="{{url('/img/top2_v2.jpg')}}" style="width:100%;">
@elseif(isset($page) && $page=="cinnamon") <img class="topImge" src="{{url('/img/top3_v2.jpg')}}" style="width:100%;">
@else <img class="topImge" src="{{url('/img/top1_v2.jpg')}}" style="width:100%;">
@endif

<?php
$s1 = "";
$s2 = "";
$s3 = "";
$s4 = "";
$s5 = "";
if(!isset($page)) $s1 = "";
elseif($page=="top") $s1 = "s";
elseif($page=="koshin") $s2 = "s";
elseif($page=="hall") $s3 = "s";
elseif($page=="cinnamon") $s4 = "s";
elseif($page=="ep") $s5 = "s";
?>


<ul id="toplist">
<li><a href="{{ asset('/') }}"><img src="{{url('/img/menu/menu1'.$s1.'.jpg')}}"></a></li>
<li><a href="{{ asset('/dataget') }}"><img src="{{url('/img/menu/menu2'.$s2.'.jpg')}}"></a></li>
<li><a href="{{ asset('/list') }}"><img src="{{url('/img/menu/menu3'.$s3.'.jpg')}}"></a></li>
<li><a href="{{ asset('/cinnamonPatrol') }}"><img src="{{url('/img/menu/menu4'.$s4.'.jpg')}}"></a></li>
<li><a href="{{ asset('/syushi') }}"><img src="{{url('/img/menu/menu5'.$s5.'.jpg')}}"></a></li>
</ul>








@stop

