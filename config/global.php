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
    //    'ir-ticket'=> '4e33eb8cb59f1f1a74774484cb59ee51ee326004',631e5703-bcc6-11ec-bac2-5254001d8a87
    //ir-ticket: 21f2448b76b98dd01463fbd0c4ab074d71a3588a
    'ir-ticket'=> '645752632b35c674e47d1cd5fd6bbdd562ebadb9',
    'sec-ch-ua-platform'=> '"Windows"',
    'Origin'=> 'https://el-drado.com',
    'Sec-Fetch-Site'=> 'same-site',
    'Sec-Fetch-Mode'=> 'cors',
    'Sec-Fetch-Dest'=> 'empty',
    'Referer'=> 'https://el-drado.com',
    'Accept-Language'=> 'ja-JP,ja;q=0.9',
];

$headers2 = [
    'Accept'=> 'application/json, text/plain, */*',
    'Accept-Encoding'=> 'gzip, deflate, br',
    'Accept-Language'=> 'ja,en-US;q=0.9,en;q=0.8',
    'User-Agent'=> 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/100.0.4896.127 Safari/537.36',
    'Content-Length'=> '77',
    'Content-Type'=> 'application/json;charset=UTF-8',
    'ir-ticket'=> '645752632b35c674e47d1cd5fd6bbdd562ebadb9',
    'Referer'=> 'https://www.el-drado.com/',
    'Origin'=> 'https://www.el-drado.com',
    'sec-ch-ua'=> '" Not A;Brand";v="99", "Chromium";v="100", "Google Chrome";v="100"',
    'sec-ch-ua-mobile'=> '?0',
    'sec-ch-ua-platform'=> '"Windows"',
    'Sec-Fetch-Dest'=> 'empty',
    'Sec-Fetch-Mode'=> 'cors',
    'Sec-Fetch-Site'=> 'same-site',
    'Connection'=> 'keep-alive',
    'Host'=> 'api.el-drado.com',
];


//api url
$daiApiUrlSlo = "https://api.el-drado.com/machine/detailSlot";
$daiApiUrlPachi = "https://api.el-drado.com/machine/detailPachinko";
$hallApiUrl_addHallId = "https://api.el-drado.com/floor/info/";
$url_list = "https://api.el-drado.com/machine/list";
$sitdownUrl = "https://api.el-drado.com/seat/sitdown";

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
    'apiHead2' => $headers2,
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