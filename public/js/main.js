var floorCount=0;
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

		});


   function koshin(f,l){
  $.ajax({
    type: "get",
    url: "http://localhost/heroseye/public/apidataget?f="+f+'&l='+l,
    dataType: "json",}).done((res) => {
      floorCount++;
      $("#floorCount").text(floorCount+'/75');
      if(floorCount==75){matome();}
    }).fail((error)=>{floorCount++;});}


   function matome(){
   $.ajax({
    type: "get",
    url: "http://localhost/heroseye/public/apiMatome",
    dataType: "json",}).done((res) => {location.href='./';});}
    //dataType: "json",}).done((res) => {alert('75到達');location.href='./';});}


