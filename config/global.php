<?php

//api ヘッダ
$headers = [
    'Connection'=> 'keep-alive',
    'sec-ch-ua'=> '" Not A;Brand";v="99", "Chromium";v="96", "Google Chrome";v="96"',
    'DNT'=> '1',
    'sec-ch-ua-mobile'=> '?0',
    'User-Agent'=> 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.45 Safari/537.36',
    'Content-Type'=> 'application/json;charset=UTF-8',
    'Accept'=> 'application/json, text/plain, */*',
    'ir-ticket'=> '4e33eb8cb59f1f1a74774484cb59ee51ee326004',
    'sec-ch-ua-platform'=> '"Windows"',
    'Origin'=> 'https://el-drado.com',
    'Sec-Fetch-Site'=> 'same-site',
    'Sec-Fetch-Mode'=> 'cors',
    'Sec-Fetch-Dest'=> 'empty',
    'Referer'=> 'https://el-drado.com',
    'Accept-Language'=> 'ja-JP,ja;q=0.9',
];

//api url
$daiApiUrlSlo = "https://api.el-drado.com/machine/detailSlot";
$daiApiUrlPachi = "https://api.el-drado.com/machine/detailPachinko";
$hallApiUrl_addHallId = "https://api.el-drado.com/floor/info/";

$daiApiUrl = [];
$daiApiUrl[1] = $daiApiUrlSlo;
$daiApiUrl[2] = $daiApiUrlPachi;

$detail = [];
$detail[1] = "slot_detail";
$detail[2] = "pachinko_detail";


//ホール番号に足すとホールIDになる。
$toHallId = "100100000";

//階数に足すとフロアIDになる。
$toFloorId = [2=>"100300000", 3=>"100330000"];

//台番に足すとマシンIDになる。
$toMachineId = [2=>"100500000", 3=>"100530000"];

return [
    'apiHead' => $headers,
    'toHallId' => $toHallId,
    'toFloorId' => $toFloorId,
    'toMachineId' => $toMachineId,
    'url_S' => $daiApiUrlSlo,
    'url_P' => $daiApiUrlPachi,
    'url_hall' => $hallApiUrl_addHallId,
    'daiApiUrl' => $daiApiUrl,
    'detail' => $detail,

];