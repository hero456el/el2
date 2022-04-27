var floorCount=0;

//Firefoxかどうか
var firefox = 0;
if ( window.navigator.userAgent.match(/Firefox/)) {firefox = 1;}

//toppageかどうか
var topPage = 0;
var uri = new URL(window.location.href);
if(uri.pathname == '/heroseye/public/'){topPage = 1;}

//localhostかどうか
var apiUrl = "http://ec2-54-84-54-214.compute-1.amazonaws.com";
if ( uri.href.match(/localhost/)) {apiUrl = "http://localhost/heroseye/public";}

//intervalId
var intervalId;

//seika
var seika = '';

//ボタンを押したタイミングで発火する
$("#aj").click(function () {
  floorCount=0;
  $("#floorCount").text(floorCount+'/75');
  for(let f=1; f<16; f++){
    for(let l=1; l<6; l++){
      koshin(f,l)
    }
  }
});


//サイト読み込み時
window.addEventListener('load', function(){
      //ウブラブ
      if(firefox){
        $('#foxOnly').removeClass('displayNone');
        setTimeout(reloadTop, 1000*60*5, '');
      }

      //成果取得時間
      $('.seikaTime').text(showClock2());

	  //シナモンパトロールGOなら
      if($('.goStop').text()==1){
        imgChangeSagasu();
      }
});

function reloadTop(){
  document.location.href = apiUrl;
}


function heroAjax(){
  $.ajax({
    type: "get",
    url: apiUrl+"/apiPlayNow",
    dataType: "json",}).done((res) => {
      console.log(res.message);
      if(res.message == 'sound'){$("#sound").get(0).play();}
    }).fail((error)=>{ });

}


   function koshin(f,l){
  $.ajax({
    type: "get",
    url: apiUrl+"/apidataget?f="+f+'&l='+l,
    dataType: "json",}).done((res) => {
      floorCount++;
      $("#floorCount").text(floorCount+'/75');
      if(floorCount==75){matome();}
    }).fail((error)=>{floorCount++;});}


   function matome(){
   $.ajax({
    type: "get",
    url: apiUrl+"/public/apiMatome",
    dataType: "json",}).done((res) => {location.href='./';});}
    //dataType: "json",}).done((res) => {alert('75到達');location.href='./';});}


// 現在時刻を返す
function set2fig(num) {
   // 桁数が1桁だったら先頭に0を加えて2桁に調整する
   var ret;
   if( num < 10 ) { ret = "0" + num; }
   else { ret = num; }
   return ret;
}
function showClock2() {
   var nowTime = new Date();
   var MM = set2fig( nowTime.getMonth() + 1 );
   var DD = set2fig( nowTime.getDate() );
   var nowHour = set2fig( nowTime.getHours() );
   var nowMin  = set2fig( nowTime.getMinutes() );
   var nowSec  = set2fig( nowTime.getSeconds() );
   var msg = MM+"/"+DD+" " + nowHour + ":" + nowMin + ":" + nowSec + "";
   return msg;
}


//■シナモンパトロール
//クロール実行
function GOGO(){
  for(i=1; i<=15; i++){
    GOGOajax(i);
  }
}

function GOGOajax(floor){
  $.ajax({
    type: "get",
    url: apiUrl+"/gogo?f="+floor,
    dataType: "json",}).
    done((res) => {
      $('.cpError').text('');
    }).fail((error)=>{
    console.log(error);
      $('.cpError').text('シナモンパトロール失敗');
    });

}

//成果の表示
function seikaHyouji() {
  $('.seikaTime').text(showClock2());
  $.ajax({
    type: "get",
    url: apiUrl+"/seika",
    dataType: "json",}).
    done((res) => {
      seika = '';
      res.forEach(e => {
          seika += '・'+e.target+'<br>';
      });
      $('.seika').html(seika);
    }).fail((error)=>{});

}


//発動セット
$('.serch').click(function () {
  $('.mainShina').attr('src','img/shina_ryo.gif?'+(new Date).getTime());
  setTimeout(imgChangeSagasu, 1000*1.7*1, '');
  $.ajax({
    type: "get",
    url: apiUrl+"/goStop?go=1",
    dataType: "json",});
});

//発動実行
function imgChangeSagasu(){
  $('.mainShina').attr('src','img/shina_sagasu.gif?'+(new Date).getTime());
  $('.serch').addClass('displayNone');
  $('.serchNow').removeClass('displayNone');
  intervalId1 = setInterval(seikaHyouji, 1000*1*1, '');
  intervalId2 = setInterval(GOGO, 1000*1*3, '');
}

//解除
$('.serchNow').click(function () {
  $('.mainShina').attr('src','img/shina_neru.gif?'+(new Date).getTime());
  $('.serchNow').addClass('displayNone');
  $('.serch').removeClass('displayNone');
  $.ajax({
    type: "get",
    url: apiUrl+"/goStop?go=0",
    dataType: "json",});
  clearInterval(intervalId1 );
  clearInterval(intervalId2 );
});

