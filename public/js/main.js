var floorCount = 0;

//Firefoxかどうか
var firefox = 0;
if (window.navigator.userAgent.match(/Firefox/)) { firefox = 1; }

//toppageかどうか
var topPage = 0;
var uri = new URL(window.location.href);
if (uri.pathname == '/heroseye/public/') { topPage = 1; }

//localhostかどうか
//var apiUrl = "http://ec2-54-84-54-214.compute-1.amazonaws.com";
var apiUrl = "https://allshare.thick.jp/elkatsu/el/public/";
if (uri.href.match(/localhost/)) { apiUrl = "http://localhost/"; }

//intervalId
var intervalId1;
var intervalId2;
var intervalId3;
var intervalId4;

//seika
var seika = '';
var dull = '';

//ドゥルアタック
var attackStart = ''; //ドゥル開始時間
var attackMokuhyo = ''; //目標時間



//ボタンを押したタイミングで発火する
$("#aj").click(function() {
    floorCount = 0;
    $("#floorCount").text(floorCount + '/75');
    for (let f = 1; f < 16; f++) {
        for (let l = 1; l < 6; l++) {
            koshin(f, l)
        }
    }
});

//サイト読み込み時
window.addEventListener('load', function() {
    //ウブラブ
    if (firefox) {
      playAlert();
        $('.foxOnly').removeClass('displayNone');
        setTimeout(reloadTop, 1000 * 60 * 5, '');
    }

    //成果取得時間
    $('.seikaTime').text(showClock2());

    //シナモンパトロールGOなら
    if ($('.goStop').text() == 1) {
        imgChangeSagasu();
        GOGO();
    }

});

function playAlert() {
    var n = $('.zanTime').text();
    if(n=="no" || n<-600 ) return false;
    if(n<70){
      ag2ytControl('playVideo');
      return false;
    } 
    var interval = (n-60) * 1000; //1000で1秒
    setTimeout(function(){
        ag2ytControl('playVideo');
    },interval);
}

function reloadTop() {
    document.location.href = apiUrl;
}


function heroAjax() {
    $.ajax({
        type: "get",
        url: apiUrl + "/apiPlayNow",
        dataType: "json",
    }).done((res) => {
        console.log(res.message);
        if (res.message == 'sound') { $("#sound").get(0).play(); }
    }).fail((error) => {});

}


function koshin(f, l) {
    $.ajax({
        type: "get",
        url: apiUrl + "/apidataget?f=" + f + '&l=' + l,
        dataType: "json",
    }).done((res) => {
        floorCount++;
        $("#floorCount").text(floorCount + '/75');
        if (floorCount == 75) {
            document.location.href = apiUrl;
            //        matome();
        }
    }).fail((error) => { floorCount++; });
}


function matome() {
    $.ajax({
        type: "get",
        url: apiUrl + "/public/apiMatome",
        dataType: "json",
    }).done((res) => { location.href = './'; });
}
//dataType: "json",}).done((res) => {alert('75到達');location.href='./';});}


// 現在時刻を返す
function set2fig(num) {
    // 桁数が1桁だったら先頭に0を加えて2桁に調整する
    var ret;
    if (num < 10) { ret = "0" + num; } else { ret = num; }
    return ret;
}

function showClock2() {
    var nowTime = new Date();
    var MM = set2fig(nowTime.getMonth() + 1);
    var DD = set2fig(nowTime.getDate());
    var nowHour = set2fig(nowTime.getHours());
    var nowMin = set2fig(nowTime.getMinutes());
    var nowSec = set2fig(nowTime.getSeconds());
    var msg = MM + "/" + DD + " " + nowHour + ":" + nowMin + ":" + nowSec + "";
    return msg;
}


//■シナモンパトロール
//クロール実行
function GOGO() {
    console.log(showClock2() + 'メインパトロールGo'); //★★★test★★★
    for (i = 1; i <= 15; i++) {
        setTimeout(GOGOajax, 700 * i, i); //0.7秒置き
    }
}

function GOGOajax(floor) {
    $.ajax({
        type: "get",
        url: apiUrl + "/gogo?f=" + floor,
        dataType: "json",
    }).
    done((res) => {
        //      console.log(res);
        $('.cpError').text('');
    }).fail((error) => {
        //    console.log(error);
        $('.cpError').text('シナモンパトロール失敗');
    });

}

//成果の表示
function seikaHyouji() {
    $('.seikaTime').text(showClock2());
    /*
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
      */

    $.ajax({
        type: "get",
        url: apiUrl + "/dull",
        dataType: "json",
    }).
    done((res) => {
        dull = '';
        res.forEach(e => {
            //dullがなければアタック終了
            if (e.mess == 'nothing') attackEnd();
            //          else{
            dull += '・' + e.mess + '<br>';
            //        console.log(e.time);//★★★test★★★
            //            if(e.time){ //ドゥルアタック
            //              if(attackMokuhyo>e.time || attackMokuhyo==""){
            //                console.log('目標セット');//★★★test★★★
            //                attackMokuhyo = e.time;
            //                if(intervalId3){clearInterval(intervalId3);}
            //                intervalId3 = setInterval(dAttack, 1000*1, ''); //1秒毎に
            //              }
            //            }
            //        }
        });
        $('.dull').html(dull);
    }).fail((error) => {});
}

//ドゥルアタック
function dAttack() {
    console.log('1秒ごとアタック待機'); //★★★test★★★
    var diff = attackMokuhyo - Math.round(Date.now() / 1000);
    console.log(diff + '秒後にアタック'); //★★★test★★★
    if (diff < 5) {
        console.log('アタックGo'); //★★★test★★★
        if (intervalId3) { clearInterval(intervalId3); }
        attackStart = Date.now();
        intervalId4 = setInterval(dAttackAjax, 500, ''); //0.5秒毎に
    }
}

//アタック実行
function dAttackAjax() {
    //  if(intervalId2){clearInterval(intervalId2);}
    //  intervalId2="";
    console.log('アタック実行'); //★★★test★★★
    if ((attackStart + 6000) < Date.now()) { //6秒で終わり
        attackEnd();
        console.log('6秒オーバー'); //★★★test★★★
    }
    $.ajax({
        type: "get",
        url: apiUrl + "/dAttack",
        dataType: "json",
    }).
    done((res) => {
        if (res == 'end') { attackEnd(); }
    }).fail((error) => {});

}

//アタック終了
function attackEnd() {
    /*
        if(intervalId3){clearInterval(intervalId3);}
        if(intervalId4){clearInterval(intervalId4);}
        attackStart="";
        attackMokuhyo="";
        intervalId3="";
        intervalId4="";
    //    if(!intervalId2){intervalId2 = setInterval(GOGO, 1000*3*1, '');} //bonus中探し
        console.log('nothing　アタック終了');//★★★test★★★
    */
}




//発動セット
$('.serch').click(function() {
    $('.mainShina').attr('src', 'img/shina_ryo.gif?' + (new Date).getTime());
    setTimeout(imgChangeSagasu, 1000 * 1.7 * 1, '');
    $.ajax({
        type: "get",
        url: apiUrl + "/goStop?go=1",
        dataType: "json",
    });
});

//発動実行
function imgChangeSagasu() {
    $('.mainShina').attr('src', 'img/shina_sagasu.gif?' + (new Date).getTime());
    $('.serch').addClass('displayNone');
    $('.serchNow').removeClass('displayNone');
    intervalId1 = setInterval(seikaHyouji, 1000 * 10 * 1, ''); //成果表示
    intervalId2 = setInterval(GOGO, 1000 * 30 * 1, ''); //bonus中探し
    //全台クロール
}

//解除
$('.serchNow').click(function() {
    $('.mainShina').attr('src', 'img/shina_neru.gif?' + (new Date).getTime());
    $('.serchNow').addClass('displayNone');
    $('.serch').removeClass('displayNone');
    $.ajax({
        type: "get",
        url: apiUrl + "/goStop?go=0",
        dataType: "json",
    });
    if (intervalId1) { clearInterval(intervalId1); }
    if (intervalId2) { clearInterval(intervalId2); }
    intervalId1 = "";
    intervalId2 = "";
});



const ytiframe= 'audio2';
//iframeで表示しているwindowオブジェクトを取得
const targetWindow = document.getElementById(ytiframe).contentWindow;

//APIのコマンドを送信する関数
const ag2ytControl = function(action,arg=null){
  targetWindow.postMessage('{"event":"command", "func":"'+action+'", "args":'+arg+'}', '*');
};

//クリックイベントで動画を操作
//再生
document.getElementById('ytplay').addEventListener('click', function(event){
  ag2ytControl('playVideo');
});