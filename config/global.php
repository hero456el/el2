<?php

//api ヘッダ
$header = [
'Accept' => 'application/json, text/plain, */*',
'Accept-Encoding' => 'gzip, deflate, br',
'Accept-Language' => 'ja',
'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 Safari/537.36',
'ir-ticket' => '885b8d99a10e4ec32bac58d682d79fcb04ce321d',
'Referer' => 'https://www.el-drado.com/select_hall',
'sec-ch-ua' => '"Not?A_Brand";v="8", "Chromium";v="108", "Google Chrome";v="108"',
'sec-ch-ua-mobile' => '?0',
'sec-ch-ua-platform' => '"Windows"',
'Sec-Fetch-Dest' => 'empty',
'Sec-Fetch-Mode' => 'cors',
'Sec-Fetch-Site' => 'same-origin',
'Connection' => 'keep-alive',
'Host' => 'www.el-drado.com',
];


//api url
// $daiApiUrlSlo = "https://api.el-drado.com/machine/detailSlot";
// $daiApiUrlPachi = "https://api.el-drado.com/machine/detailPachinko";
// $hallApiUrl_addHallId = "https://api.el-drado.com/floor/info/";
// $url_list = "https://api.el-drado.com/machine/list";
// $sitdownUrl = "https://api.el-drado.com/seat/sitdown";

//api url New
$daiApiUrlSlo = "https://www.el-drado.com/api/machine/detailSlot";
$daiApiUrlPachi = "https://www.el-drado.com/api/machine/detailPachinko";
$hallApiUrl_addHallId = "https://www.el-drado.com/api/floor/info/";
$url_list = "https://www.el-drado.com/api/machine/list";
$sitdownUrl = "https://www.el-drado.com/api/seat/sitdown";

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
    'apiHead' => $header,
    'apiHead2' => $header,
    'toHallId' => $toHallId,
    'toFloorId' => $toFloorId,
    'toMachineId' => $toMachineId,
    'url_S' => $daiApiUrlSlo,
    'url_P' => $daiApiUrlPachi,
    'url_hall' => $hallApiUrl_addHallId,
    'daiApiUrl' => $daiApiUrl,
    'sitdownUrl' => $sitdownUrl,
    'url_list' => $url_list,
    'detail' => $detail,

];