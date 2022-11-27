<?php

$irList=[
    'cadd0e024cf812b618d4b0d74e140ebc216cbcfd',
];
$irList2=[
    'cadd0e024cf812b618d4b0d74e140ebc216cbcfd',
    '0ff2dc7ddd879267beb0ce440fa26df37725b29b',
    'e18abf01afa3efcad6da9d0038046baaf440e84c',
    '2d1e5796f7e201e82168b79411b310d6450c1f34',
    'c8c6c150cee38a14fcc45d65a7a7b6a1105f5060',
    'beba357d99d6b597365797afcbd72ea1ef44d213',
    '0833c81157803c28fd28af4a941d153fe1bf6aaf',
    'd8a79d96f8460bbd93d4168b228b76e6b0810792',
    '830ed52f7fa43eaef8066ce603f9bca6b2706c5e',
    'ee188f6c49385a2cf26f54e0b41f9d5a78fa16e2',
    '931a59072a06421734917990338de09a3a121740',
    '5ac6f274dba829b5fb432ba541e82aadf5f1984e',
    '149487b3ee36268a014fdf8ccaa0b233f6150aca',
    'dadcf21daa986c94ed732d82c2c46cd0be19d8a7',
    'ee0ad12f58fd9dc7fc971819ed183d29c8ab0d0a',
    '1f3987eab2c787480ce5a4febb2eadbcb17749db',
    'a456e3a11d553e26866ef2f49a982c91e52d6509',
    '11aa07afbea75b8be13a1ce01f67eeca584030ae',
    '5b943ac63d4cd949576c1a7fe2e0df9a57f3127b',
    'fd2b0f3ba615e95521fedb1e86040dd36027009d',
    '1fc7b891a1d454a1eab5f3b43a94a6c8dc5eef42',
    '99a282b6d629340fe4105f1803bea92c6a70442d',
    'a707f32f303ce98bc27b2b4f7559351b0fe7207c',
    '65342ae1ff9a035fa7eeaab653777b1ead62a6fa',
    '97418a8101bdbfa612a7f8e786ce0ef7f0a16610',
    '86838ada9dd8f8fced89ced1e013d912d733aab9',
    '6deb4bd9858119e69a75744be4299a438f1982bc',
    '816afcae8228be5949c062e0b8afc515351207e5',
    'ae50a2562dde3a084b4e16df9e33fe54608b2dbb',
    '0b8e3b35c58d3e1337410eabb36b3759adc28a7c',
    'b4d004910f478bf4e42418d00bda05974495dfc3',
    '5a4c30db1ef195e9f9c70891343c7f54cdf90e57',
    '6708b111fc4cbc75ebca059d304e24a7e2f4736b',
];
$key = array_rand($irList, 1);
$ir = $irList[$key];

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
    'ir-ticket'=> $ir,
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
    'ir-ticket'=> $ir,
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