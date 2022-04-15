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
if ( uri.href.match(/localhostt/)) {apiUrl = "http://localhost/heroseye/public";}

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



window.addEventListener('load', function(){
      if(firefox && topPage){
        $('#foxOnly').removeClass('displayNone');
        setTimeout(reloadTop, 1000*60*5, '');
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


