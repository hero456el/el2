
var url = "https://api.el-drado.com/machine/detailSlot";
var purl = "https://api.el-drado.com/machine/detailPachinko";
var headers = {
  'Connection': 'keep-alive',
  'sec-ch-ua': '" Not A;Brand";v="99", "Chromium";v="96", "Google Chrome";v="96"',
  'DNT': '1',
  'sec-ch-ua-mobile': '?0',
  'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.45 Safari/537.36',
  'Content-Type': 'application/json;charset=UTF-8',
  'Accept': 'application/json, text/plain, */*',
  'ir-ticket': '4e33eb8cb59f1f1a74774484cb59ee51ee326004',
  'sec-ch-ua-platform': '"Windows"',
  'Origin': 'https://el-drado.com',
  'Sec-Fetch-Site': 'same-site',
  'Sec-Fetch-Mode': 'cors',
  'Sec-Fetch-Dest': 'empty',
  'Referer': 'https://el-drado.com',
  'Accept-Language': 'ja-JP,ja;q=0.9',
}
var data_for_write = []
function set_day_trigger(min){
  clear_trigger()
  for(i =10;i<23;i++){
    const time = new Date();
    time.setHours(i);
    time.setMinutes(min);
    ScriptApp.newTrigger('get_day_data').timeBased().at(time).create();
  }
}
function set_night_trigger(min){
  clear_trigger()
  set_get_last_night_data_trigger()
  for(j =22;j<25;j++){
    const time = new Date();
    time.setHours(j);
    time.setMinutes(min);
    ScriptApp.newTrigger('get_night_data').timeBased().at(time).create();
  }
  for(k =1;k<10;k++){
  const time = new Date();
  var day = time.getDate()
  time.setDate(day + 1)
  time.setHours(k);
  time.setMinutes(min);
  ScriptApp.newTrigger('get_night_data').timeBased().at(time).create();
  }
  const time = new Date();
  var day = time.getDate()
  time.setDate(day + 1)
  time.setHours(10);
  time.setMinutes(min);
  ScriptApp.newTrigger('get_day_data').timeBased().at(time).create();
}
function set_get_last_night_data_trigger(){
  const time = new Date();
  time.setHours(22);
  time.setMinutes(10);
  ScriptApp.newTrigger('get_yesterday_night_data').timeBased().at(time).create();
}

function clear_trigger(){
  const triggers = ScriptApp.getProjectTriggers();
  for(const trigger of triggers){
     if(trigger.getHandlerFunction() == "get_night_data" || trigger.getHandlerFunction() == "get_yesterday_night_data" ||trigger.getHandlerFunction() == "get_day_data" ){
      ScriptApp.deleteTrigger(trigger);
    }
  }
}

function set_trigger_night_zero_min(){
  set_night_trigger(0)
}
function set_trigger_night_ten_min(){
  set_night_trigger(10)
}
function set_trigger_night_two_ten_min(){
  set_night_trigger(20)
}
function set_trigger_night_three_ten_min(){
  set_night_trigger(30)
}
function set_trigger_night_four_ten_min(){
  set_night_trigger(10)
}
function set_trigger_night_five_ten_min(){
  set_night_trigger(50)
}


function set_trigger_day_zero_min(){
  set_day_trigger(0)
}
function set_trigger_day_ten_min(){
  set_day_trigger(10)
}
function set_trigger_day_two_ten_min(){
  set_day_trigger(20)
}
function set_trigger_day_three_ten_min(){
  set_day_trigger(30)
}
function set_trigger_day_four_ten_min(){
  set_day_trigger(10)
}
function set_trigger_day_five_ten_min(){
  set_day_trigger(50)
}


function get_datasheat_id(fol_id){
  //idを取得するフォルダの指定
  const folderId = DriveApp.getFolderById(fol_id);
  //指定したフォルダ内のすべてのファイルを格納
  const files = folderId.getFiles();

  //データを格納する配列の宣言
  const arr = [];

  //2次元配列として追加
  //フォルダ内のすべてのファイルについて実行
  while (files.hasNext()) {
    //すべてのファイルから１つ取り出し
    const file = files.next();
  
    //配列にファイルのデータを追加
    //getName：ファイルの名前、getId：ファイルのID、getUrl:ファイルのURL
    arr.push([file.getName(),file.getId(),file.getUrl()]);
  }
  //データを格納した配列arrの確認
  console.log(arr);
  return arr
}
function get_w_rose_data(hall, floor, rate, mode) {
  if (hall == "da") {
    hall = "100100002"
    floor_id = '1003000' + floor
    m_id = 100500000
  }

  if (hall == "ni") {
    hall = "100100003"
    floor_id = '1003300' + floor
    m_id = 100530000
  }
  data_arr = {}
  for (var i = 1; i <= 40; i++) {
    if (floor == 1) {
      daiban = i
    } else {
      daiban = (Number(floor) - 1) * 40 + i
    }
    machine_id = String(m_id + daiban)

    var data = {
      "mst_hall_id": hall,
      "mst_floor_id": floor_id,
      "mst_machine_id": machine_id
    }
    var options = {
      "method": "post",
      "payload": JSON.stringify(data),
      "headers": headers
    }
    var res = UrlFetchApp.fetch(url, options);
    var res_json = JSON.parse(res.getContentText())
    bonus_at_data = res_json['body']['slot_detail'][mode]['bonus_history']
    total_spin = res_json['body']['slot_detail'][mode]['total_spin_count']
    bonus_count = bonus_at_data.length
    bb_count = 0
    rb_count = 0
    bb_hit_ave = 0
    rb_hit_ave = 0
    total_ave = 0
    direct_bb_count = 0
    continue_spin_count = 0
    direct_bb_ave = 0
    hist_log = ""
    if (bonus_count != 0) {
      for (var log = 0; log < bonus_count; log++) {
        if (bonus_at_data[log]["type"] == 1) {
          hist_log += String(bonus_at_data[log]["spin"]) + "B,"
        } else {
          hist_log += String(bonus_at_data[log]["spin"]) + "R,"
        }
      }
    }

    rb_spin_count = 0
    for (var j = 0; j < bonus_count; j++) {
      bonus_type = Number(res_json['body']['slot_detail'][mode]['bonus_history'][j]['type'])
      if (Number(bonus_at_data[j]['spin']) > 33) {
        if (bonus_type == 1) {
          direct_bb_count += 1
          bb_count += 1
        }
      }
      if (Number(bonus_at_data[j]['spin']) < 33) {
        continue_spin_count += Number(bonus_at_data[j]['spin'])
      }
      if (bonus_type == 1) {
        bb_count += 1
      }
      if (bonus_type == 2) {
        rb_count += 1
        rb_spin_count += Number(bonus_at_data[j]['spin'])
      }
    }
    if (bonus_count != 0) {
      if (bb_count != 0) {
        bb_hit_ave = total_spin / bb_count
      }
      if (rb_count != 0) {
        rb_hit_ave = rb_spin_count / rb_count
      }
    }
    today_medal = res_json['body']['slot_detail'][mode]['medal']
    var today = new Date();
    /*取得した日付を西暦月日で表示してformatdateに代入*/
    var formatDate = Utilities.formatDate(today, "JST", "yyyy年M月d日");
    var time_now = Utilities.formatDate(today, "JST", "HH:mm");
    if (bonus_count != 0) {
      total_ave = total_spin / bonus_count
    }
    if (direct_bb_count != 0) {
      direct_bb_ave = (total_spin - continue_spin_count) / direct_bb_count
    }
    data_arr[i] = [formatDate,
      time_now,
      floor,
      "ウィッチ" + rate,
      i,
      total_spin,
      "",
      "",
      "",
      "",
      "",
      "",
      "",
      today_medal,
      hist_log,
      "",
      "",
      "",
      ""]
  }
  return data_arr
}
function get_s_rose_data(hall, floor, rate, mode) {
  if (hall == "da") {
    hall = "100100002"
    floor_id = '1003000' + floor
    m_id = 100500000
  }

  if (hall == "ni") {
    hall = "100100003"
    floor_id = '1003300' + floor
    m_id = 100530000
  }
  data_arr = {}
  for (var i = 1; i <= 40; i++) {
    if (floor == 1) {
      daiban = i
    } else {
      daiban = (Number(floor) - 1) * 40 + i
    }
    machine_id = String(m_id + daiban)

    var data = {
      "mst_hall_id": hall,
      "mst_floor_id": floor_id,
      "mst_machine_id": machine_id
    }
    var options = {
      "method": "post",
      "payload": JSON.stringify(data),
      "headers": headers
    };
    var res = UrlFetchApp.fetch(url, options);
    var res_json = JSON.parse(res.getContentText())
    bonus_at_data = res_json['body']['slot_detail'][mode]['bonus_history']
    total_spin = res_json['body']['slot_detail'][mode]['total_spin_count']
    var slump = res_json['body']['slot_detail'][mode]['slump_graph']
    var go_to_mode_b = 0
    var go_to_mode_b_ratio = 0
    var base_game = 0
    var mode_flag = 0
    var in_medal = 0
    var out_medal = 0
    bb_count = 0
    rb_count = 0
    bb_hit_ave = 0
    rb_hit_ave = 0
    total_ave = 0
    normal_b_count = 0
    normal_spin_count = 0
    continue_b_count = 0
    continue_spin_count = 0
    first_hit_ave = 0
    bonus_count = bonus_at_data.length
    hist_log = ""
    if (bonus_count != 0) {
      for (var log = 0; log < bonus_count; log++) {
        if (bonus_at_data[log]["type"] == 1) {
          hist_log += String(bonus_at_data[log]["spin"]) + "B,"
        } else {
          hist_log += String(bonus_at_data[log]["spin"]) + "R,"
        }
      }
    }

    for (var j = 0; j < bonus_count; j++) {
      if (Number(bonus_at_data[j]['spin']) > 33) {
        mode_flag = 0
        normal_b_count += 1
        normal_spin_count += Number(bonus_at_data[j]['spin'])
      }
      if (Number(bonus_at_data[j]['spin']) < 33) {
        continue_b_count += 1
        continue_spin_count += Number(bonus_at_data[j]['spin'])
        if (j != 0){
          if(Number(bonus_at_data[j-1]['spin']) < 33 && bonus_at_data[j-1]["type"] == 1 && mode_flag != 1){
            mode_flag = 1
            go_to_mode_b += 1
          }
        }
      }
      bonus_type = Number(res_json['body']['slot_detail'][mode]['bonus_history'][j]['type'])
      if (bonus_type == 1) {
        bb_count += 1
      }
      if (bonus_type == 2) {
        rb_count += 1
      }
    }
    if (bonus_count != 0) {
      total_ave = total_spin / bonus_count
      if (bb_count != 0) {
        bb_hit_ave = total_spin / bb_count
      }
      if (rb_count != 0) {
        rb_hit_ave = total_spin / rb_count
      }
    }
    if (go_to_mode_b != 0) {
      go_to_mode_b_ratio = go_to_mode_b/normal_b_count

    }
    continue_b_ave = 0
    if (normal_b_count != 0) {
      first_hit_ave = normal_spin_count / normal_b_count
    }
    if (continue_b_count != 0) {
      continue_b_ave = continue_spin_count / continue_b_count
    }
    today_medal = res_json['body']['slot_detail'][mode]['medal']
    var today = new Date();
    /*取得した日付を西暦月日で表示してformatdateに代入*/
    var formatDate = Utilities.formatDate(today, "JST", "yyyy年M月d日");
    var time_now = Utilities.formatDate(today, "JST", "HH:mm");
    out_medal = bb_count * 307 + rb_count * 126
    var spend_medal = out_medal - Number(today_medal)
    var game_per_medal = 0
    if (total_spin != 0){
      game_per_medal = spend_medal/total_spin
    }
    if (game_per_medal != 0){
      base_game = 50/game_per_medal
    }
    data_arr[i] = [formatDate,
      time_now,
      floor,
      "シクレ" + rate,
      i,
      total_spin,
      "",
      "",
      "",
      "",
      "",
      "",
      "",
      "",
      "",
      "",
      "",
      today_medal,
      hist_log]
    console.log(data_arr[i])
  }
  return data_arr
}
function get_peach_data(hall, floor, rate, mode) {
  if (hall == "da") {
    hall = "100100002"
    floor_id = '1003000' + floor
    m_id = 100500000
  }

  if (hall == "ni") {
    hall = "100100003"
    floor_id = '1003300' + floor
    m_id = 100530000
  }
  data_arr = {}
  for (var i = 1; i <= 40; i++) {
    if (floor == 1) {
      daiban = i
    } else {
      daiban = (Number(floor) - 1) * 40 + i
    }
    machine_id = String(m_id + daiban)

    var data = {
      "mst_hall_id": hall,
      "mst_floor_id": floor_id,
      "mst_machine_id": machine_id
    }
    var options = {
      "method": "post",
      "payload": JSON.stringify(data),
      "headers": headers
    }
    var res = UrlFetchApp.fetch(url, options);
    var res_json = JSON.parse(res.getContentText())
    bonus_at_data = res_json['body']['slot_detail'][mode]['bonus_history']
    total_spin = res_json['body']['slot_detail'][mode]['total_spin_count']
    bonus_count = bonus_at_data.length
    bb_count = 0
    rb_count = 0
    at_count = 0
    bb_hit_ave = 0
    rb_hit_ave = 0
    at_hit_ave = 0
    total_ave = 0
    hist_log = ""
    if (bonus_count != 0) {
      for (var log = 0; log < bonus_count; log++) {
        if (bonus_at_data[log]["type"] == 1) {
          hist_log += String(bonus_at_data[log]["spin"]) + "B,"
        } else if (bonus_at_data[log]["type"] == 3) {
          hist_log += String(bonus_at_data[log]["spin"]) + "AT,"
        } else {
          hist_log += String(bonus_at_data[log]["spin"]) + "R,"
        }
      }
    }
    for (var j = 0; j < bonus_count; j++) {
      bonus_type = Number(res_json['body']['slot_detail'][mode]['bonus_history'][j]['type'])
      if (bonus_type == 1) {
        bb_count += 1
      }
      if (bonus_type == 2) {
        rb_count += 1
      }
      if (bonus_type == 3) {
        at_count += 1
      }
    }
    if (bonus_count != 0) {
      if (bb_count != 0) {
        bb_hit_ave = total_spin / bb_count
      }
      if (rb_count != 0) {
        rb_hit_ave = total_spin / rb_count
      }
      if (at_count != 0) {
        at_hit_ave = total_spin / at_count
      }
    }
    today_medal = res_json['body']['slot_detail'][mode]['medal']
    var today = new Date();
    /*取得した日付を西暦月日で表示してformatdateに代入*/
    var formatDate = Utilities.formatDate(today, "JST", "yyyy年M月d日");
    var time_now = Utilities.formatDate(today, "JST", "HH:mm");
    if (bonus_count != 0) {
      total_ave = total_spin / bonus_count
    }
    data_arr[i] = [formatDate,
      time_now,
      floor,
      "ピーチ" + rate,
      i,
      total_spin,
      "",
      "",
      "",
      "",
      "",
      "",
      "",
      today_medal,
      hist_log,
      "",
      "",
      "",
      ""]
    console.log(data_arr[i])
  }
  return data_arr
}
function get_rich_data(hall, floor, rate, mode) {
  if (hall == "da") {
    hall = "100100002"
    floor_id = '1003000' + floor
    m_id = 100500000
  }

  if (hall == "ni") {
    hall = "100100003"
    floor_id = '1003300' + floor
    m_id = 100530000
  }
  data_arr = {}
  for (var i = 1; i <= 40; i++) {
    if (floor == 1) {
      daiban = i
    } else {
      daiban = (Number(floor) - 1) * 40 + i
    }
    machine_id = String(m_id + daiban)

    var data = {
      "mst_hall_id": hall,
      "mst_floor_id": floor_id,
      "mst_machine_id": machine_id
    }
    var options = {
      "method": "post",
      "payload": JSON.stringify(data),
      "headers": headers
    }
    var res = UrlFetchApp.fetch(url, options);
    var res_json = JSON.parse(res.getContentText())
    bonus_at_data = res_json['body']['slot_detail'][mode]['bonus_history']
    total_spin = res_json['body']['slot_detail'][mode]['total_spin_count']
    bonus_count = bonus_at_data.length
    bb_count = 0
    rb_count = 0
    bb_hit_ave = 0
    rb_hit_ave = 0
    total_ave = 0
    hist_log = ""
    if (bonus_count != 0) {
      for (var log = 0; log < bonus_count; log++) {
        if (bonus_at_data[log]["type"] == 1) {
          hist_log += String(bonus_at_data[log]["spin"]) + "B,"
        } else {
          hist_log += String(bonus_at_data[log]["spin"]) + "R,"
        }
      }
    }
    for (var j = 0; j < bonus_count; j++) {
      bonus_type = Number(res_json['body']['slot_detail'][mode]['bonus_history'][j]['type'])
      if (bonus_type == 1) {
        bb_count += 1
      }
      if (bonus_type == 2) {
        rb_count += 1
      }
    }
    if (bonus_count != 0) {
      if (bb_count != 0) {
        bb_hit_ave = total_spin / bb_count
      }
      if (rb_count != 0) {
        rb_hit_ave = total_spin / rb_count
      }
    }
    today_medal = res_json['body']['slot_detail'][mode]['medal']
    var today = new Date();
    /*取得した日付を西暦月日で表示してformatdateに代入*/
    var formatDate = Utilities.formatDate(today, "JST", "yyyy年M月d日");
    var time_now = Utilities.formatDate(today, "JST", "HH:mm");
    if (bonus_count != 0) {
      total_ave = total_spin / bonus_count
    }
    data_arr[i] = [formatDate,
      time_now,
      floor,
      "リッチ" + rate,
      i,
      total_spin,
      "",
      "",
      "",
      "",
      "",
      today_medal,
      hist_log,
      "",
      "",
      "",
      "",
      "",
      ""]
    console.log(data_arr[i])
  }
  return data_arr
}
function get_dice_data(hall, floor, rate, mode) {
  if (hall == "da") {
    hall = "100100002"
    floor_id = '1003000' + floor
    m_id = 100500000
  }

  if (hall == "ni") {
    hall = "100100003"
    floor_id = '1003300' + floor
    m_id = 100530000
  }
  data_arr = {}
  for (var i = 1; i <= 40; i++) {
    if (floor == 1) {
      daiban = i
    } else {
      daiban = (Number(floor) - 1) * 40 + i
    }
    machine_id = String(m_id + daiban)

    var data = {
      "mst_hall_id": hall,
      "mst_floor_id": floor_id,
      "mst_machine_id": machine_id
    }
    var options = {
      "method": "post",
      "payload": JSON.stringify(data),
      "headers": headers
    }
    var res = UrlFetchApp.fetch(url, options);
    var res_json = JSON.parse(res.getContentText())
    bonus_at_data = res_json['body']['slot_detail'][mode]['bonus_history']
    total_spin = res_json['body']['slot_detail'][mode]['total_spin_count']
    bonus_count = bonus_at_data.length
    bb_count = 0
    rb_count = 0
    bb_hit_ave = 0
    rb_hit_ave = 0
    total_ave = 0
    hist_log = ""
    if (bonus_count != 0) {
      for (var log = 0; log < bonus_count; log++) {
        if (bonus_at_data[log]["type"] == 1) {
          hist_log += String(bonus_at_data[log]["spin"]) + "UJP,"
        } else {
          hist_log += String(bonus_at_data[log]["spin"]) + "JP,"
        }
      }
    }
    red_count = 0
    for (var j = 0; j < bonus_count; j++) {
      bonus_type = Number(res_json['body']['slot_detail'][mode]['bonus_history'][j]['type'])
      if (bonus_type == 1) {
        bb_count += 1
      }
      else if(bonus_type == 2){
        rb_count += 1
        red_count += 1
      }else{
        rb_count +=1
      }
      console.log(i)
      console.log (bonus_type)

    }
    if (bonus_count != 0) {
      if (bb_count != 0) {
        bb_hit_ave = total_spin / bb_count
      }
      if (rb_count != 0) {
        rb_hit_ave = total_spin / rb_count
      }
    }
    today_medal = res_json['body']['slot_detail'][mode]['medal']
    var today = new Date();
    /*取得した日付を西暦月日で表示してformatdateに代入*/
    var formatDate = Utilities.formatDate(today, "JST", "yyyy年M月d日");
    var time_now = Utilities.formatDate(today, "JST", "HH:mm");
    if (bonus_count != 0) {
      total_ave = total_spin / bonus_count
    }
    data_arr[i] = [formatDate,
      time_now,
      floor,
      "ダイス" + rate,
      i,
      total_spin,
      "",
      "",
      "",
      "",
      today_medal,
      hist_log,
      "",
      "",
      "",
      "",
      "",
      "",
      ""]
    //console.log(data_arr[i])
  }
  return data_arr
}
function get_cosmo_data(hall, floor, rate, mode) {
  if (hall == "da") {
    hall = "100100002"
    floor_id = '1003000' + floor
    m_id = 100500000
  }

  if (hall == "ni") {
    hall = "100100003"
    floor_id = '1003300' + floor
    m_id = 100530000
  }
  data_arr = {}
  for (var i = 1; i <= 40; i++) {
    if (floor == 1) {
      daiban = i
    } else {
      daiban = (Number(floor) - 1) * 40 + i
    }
    machine_id = String(m_id + daiban)

    var data = {
      "mst_hall_id": hall,
      "mst_floor_id": floor_id,
      "mst_machine_id": machine_id
    }
    var options = {
      "method": "post",
      "payload": JSON.stringify(data),
      "headers": headers
    }
    var res = UrlFetchApp.fetch(purl, options);
    var res_json = JSON.parse(res.getContentText())
    console.log(res.getContentText())
    bonus_data = res_json['body']['pachinko_detail'][mode]['bonus_history']
    total_spin = res_json['body']['pachinko_detail'][mode]['total_spin_count']
    bonus_count = bonus_data.length
    hist_log = ""
    if (bonus_count != 0) {
      for (var log = 0; log < bonus_count; log++) {
        if (bonus_data[log]["continue"] == 1) {
          hist_log += String(bonus_data[log]["spin"]) + "通,"
        } else {
          hist_log += String(bonus_data[log]["spin"]) + "確" + String(bonus_data[log]["continue"]) + "連,"
        }
      }
    }
    total_bonus_count = 0
    hit_only_count = 0
    seccond_hit_count = 0
    for (var m = 0; m < bonus_count; m++) {
      ren = Number(bonus_data[m]["continue"])
      total_bonus_count += ren
      if (ren == 1) {
        hit_only_count += 1
      }
      if (ren == 2) {
        seccond_hit_count += 1
      }
    }
    total_hit_ave = 0
    first_hit_ave = 0
    only_ratio = 0
    second_ratio = 0
    if (total_bonus_count != 0) {
      total_hit_ave = total_spin / total_bonus_count
      first_hit_ave = total_spin / bonus_count
      if (hit_only_count != 0) {
        only_ratio = hit_only_count / bonus_count
      }
      if (seccond_hit_count != 0) {
        second_ratio = seccond_hit_count / bonus_count
      }
    }

    today_ball = res_json['body']['pachinko_detail'][mode]['ball']
    var today = new Date();
    /*取得した日付を西暦月日で表示してformatdateに代入*/
    var formatDate = Utilities.formatDate(today, "JST", "yyyy年M月d日");
    var time_now = Utilities.formatDate(today, "JST", "HH:mm");
    data_arr[i] = [formatDate,
      time_now,
      floor,
      "コスモ" + rate,
      i,
      total_spin,
      "",
      "",
      "",
      "",
      "",
      "",
      "",
      "",
      today_ball,
      hist_log,
      "",
      "",
      ""]
  } return data_arr
}
function get_mate_data(hall, floor, rate, mode) {
  if (hall == "da") {
    hall = "100100002"
    floor_id = '1003000' + floor
    m_id = 100500000
  }

  if (hall == "ni") {
    hall = "100100003"
    floor_id = '1003300' + floor
    m_id = 100530000
  }
  data_arr = {}
  for (var i = 1; i <= 40; i++) {
    if (floor == 1) {
      daiban = i
    } else {
      daiban = (Number(floor) - 1) * 40 + i
    }
    machine_id = String(m_id + daiban)

    var data = {
      "mst_hall_id": hall,
      "mst_floor_id": floor_id,
      "mst_machine_id": machine_id
    }
    var options = {
      "method": "post",
      "payload": JSON.stringify(data),
      "headers": headers
    }
    var res = UrlFetchApp.fetch(purl, options);
    var res_json = JSON.parse(res.getContentText())
    bonus_data = res_json['body']['pachinko_detail'][mode]['bonus_history']
    total_spin = res_json['body']['pachinko_detail'][mode]['total_spin_count']
    bonus_count = bonus_data.length
    hist_log = ""
    if (bonus_count != 0) {
      for (var log = 0; log < bonus_count; log++) {
        if (bonus_data[log]["continue"] == 1) {
          hist_log += String(bonus_data[log]["spin"]) + "通,"
        } else {
          hist_log += String(bonus_data[log]["spin"]) + "確" + String(bonus_data[log]["continue"]) + "連,"
        }
      }
    }
    total_bonus_count = 0
    hit_only_count = 0
    seccond_hit_count = 0
    for (var m = 0; m < bonus_count; m++) {
      ren = Number(bonus_data[m]["continue"])
      total_bonus_count += ren
      if (ren == 1) {
        hit_only_count += 1
      }
      if (ren == 2) {
        seccond_hit_count += 1
      }
    }
    total_hit_ave = 0
    first_hit_ave = 0
    only_ratio = 0
    second_ratio = 0
    if (total_bonus_count != 0) {
      total_hit_ave = total_spin / total_bonus_count
      first_hit_ave = total_spin / bonus_count
      if (hit_only_count != 0) {
        only_ratio = hit_only_count / bonus_count
      }
      if (seccond_hit_count != 0) {
        second_ratio = seccond_hit_count / bonus_count
      }
    }

    today_ball = res_json['body']['pachinko_detail'][mode]['ball']
    var today = new Date();
    /*取得した日付を西暦月日で表示してformatdateに代入*/
    var formatDate = Utilities.formatDate(today, "JST", "yyyy年M月d日");
    var time_now = Utilities.formatDate(today, "JST", "HH:mm");
    data_arr[i] = [formatDate,
      time_now,
      floor,
      "メイト" + rate,
      i,
      total_spin,
      "",
      "",
      "",
      "",
      today_ball,
      hist_log,
      "",
      "",
      "",
      "",
      "",
      "",
      ""]
  } return data_arr
}
function CreateNewSpreadSheet(file_name, folder_id) {

  //【手順1】新規スプレッドシートをマイドライブに作成する
  const ss = SpreadsheetApp.create(file_name);

  /*addFile,removeFileメソッドのパラメータはFileオブジェクトを指定するため、
  【手順1】で作成したスプレッドシートをFileオブジェクトとして取得する */
  const file = DriveApp.getFileById(ss.getId());

  //【手順2】手順1で作成したスプレッドシートを指定フォルダに「追加」する
  const id = folder_id; //フォルダID
  DriveApp.getFolderById(id).addFile(file);

  //【手順3】手順1で作成したスプレッドシートをマイドライブから「削除」する
  DriveApp.getRootFolder().removeFile(file);
  return ss.getId()
}
function write_s_rose_data(hall, floor, rate, mode, sheet) {
  data = get_s_rose_data(hall, floor, rate, mode)
  var total_spin_sum = 0
  var total_bb_sum = 0
  var total_rb_sum = 0
  var total_first_hit_sum = 0
  var total_ren_ave_sum = 0
  var not_zero_m_num = 40
  var total_go_to_mode_b = 0
  var total_go_to_mode_b_ratio = 0
  var total_base_game = 0

  str = ["シクレ" + rate,"","","","","","","","","","","","","","","","","",""]
  data_for_write.push(str)
  datail = ["日付", "時間", "フロア", "機種名", "台番", "総回転", "BB回数", "BB確率", "RB回数", "RB確率", "合算", "初当たり回数", "初当たり確率", "連G平均","連突入回数","連突入率","コイン持ち", "差枚", "履歴"]
  data_for_write.push(datail)
  m_total_medal = 0
  for (var l = 0; l < 40; l++) {
    data_for_write.push(data[l + 1])
    m_total_medal += Number(data[l + 1][17])
    total_spin_sum += Number(data[l + 1][5])
    total_bb_sum += Number(data[l + 1][6])
    total_rb_sum += Number(data[l + 1][8])
    total_first_hit_sum += Number(data[l + 1][11])
    total_ren_ave_sum += Number(data[l + 1][13])
    total_go_to_mode_b += Number(data[l + 1][14])
    if (Number(data[l + 1][13]) == 0) {
      not_zero_m_num -= 1
    }
  }
  if (total_first_hit_sum != 0){
    total_go_to_mode_b_ratio = total_go_to_mode_b/total_first_hit_sum
  }
  total_bb_ave = 0
  if (total_bb_sum != 0) {
    total_bb_ave = total_spin_sum / total_bb_sum
  }
  total_rb_ave = 0
  if (total_rb_sum != 0) {
    total_rb_ave = total_spin_sum / total_rb_sum
  }
  total_b_ave = 0
  if ((total_bb_sum + total_rb_sum) != 0) {
    total_b_ave = total_spin_sum / (total_bb_sum + total_rb_sum)
  }
  total_ren_ave = 0
  if (total_ren_ave_sum != 0) {
    total_ren_ave = total_ren_ave_sum / not_zero_m_num
  }
  total_out_medal = total_bb_sum * 307 + total_rb_sum * 126
  var total_spend_medal = total_out_medal - Number(m_total_medal)
  var total_game_per_medal = 0
  if (total_spin_sum != 0){
    total_game_per_medal = total_spend_medal/total_spin_sum
  }
  if (total_game_per_medal != 0){
    total_base_game = 50/total_game_per_medal
  }  
  str = ["トータル", "", "", "", "", total_spin_sum, total_bb_sum, total_bb_ave, total_rb_sum, total_rb_ave, total_b_ave, total_first_hit_sum, total_ren_ave_sum, total_ren_ave, total_go_to_mode_b,total_go_to_mode_b_ratio,total_base_game,m_total_medal,""]
  console.log(str)
  data_for_write.push(str)
  str = ["余白","","","","","","","","","","","","","","","","","",""]
  data_for_write.push(str)
  str = ["余白","","","","","","","","","","","","","","","","","",""]
  data_for_write.push(str)
}
function write_w_rose_data(hall, floor, rate, mode, sheet) {
  data = get_w_rose_data(hall, floor, rate, mode)
  var total_spin_sum = 0
  var total_bb_sum = 0
  var total_rb_sum = 0
  var total_direct_bb_sum = 0
  var total_direct_bb_ave_sum = 0
  var not_zero_m_num = 40
  str = ["ウィッチ" + rate,"","","","","","","","","","","","","","","","","",""]
  data_for_write.push(str)
  datail = ["日付", "時間", "フロア", "機種名", "台番", "総回転", "BB回数", "BB確率", "RB回数", "RB確率", "合算", "BB直当たり回数", "BB直当たり確率", "差枚", "履歴","","","",""]
  data_for_write.push(datail)
  m_total_medal = 0
  for (var l = 0; l < 40; l++) {
    data_for_write.push(data[l + 1])
    m_total_medal += Number(data[l + 1][13])
    total_spin_sum += Number(data[l + 1][5])
    total_bb_sum += Number(data[l + 1][6])
    total_rb_sum += Number(data[l + 1][8])
    total_direct_bb_sum += Number(data[l + 1][11])
    total_direct_bb_ave_sum += Number(data[l + 1][12])
    if (Number(data[l + 1][12]) == 0) {
      not_zero_m_num -= 1
    }
  }
  total_bb_ave = 0
  if (total_bb_sum != 0) {
    total_bb_ave = total_spin_sum / total_bb_sum
  }
  total_rb_ave = 0
  if (total_rb_sum != 0) {
    total_rb_ave = total_spin_sum / total_rb_sum
  }
  total_b_ave = 0
  if ((total_bb_sum + total_rb_sum) != 0) {
    total_b_ave = total_spin_sum / (total_bb_sum + total_rb_sum)
  }
  total_direct_bb_ave = 0
  if (total_direct_bb_ave_sum != 0) {
    total_direct_bb_ave = total_direct_bb_ave_sum / not_zero_m_num
  }
  str = ["トータル", "", "", "", "", total_spin_sum, total_bb_sum, total_bb_ave, total_rb_sum, total_rb_ave, total_b_ave, total_direct_bb_sum, total_direct_bb_ave, m_total_medal,"","","","",""
  ]
  data_for_write.push(str)
  str = ["余白","","","","","","","","","","","","","","","","","",""]
  data_for_write.push(str)
  str = ["余白","","","","","","","","","","","","","","","","","",""]
  data_for_write.push(str)
}
function write_rich_data(hall, floor, rate, mode, sheet) {
  data = get_rich_data(hall, floor, rate, mode)
  var total_spin_sum = 0
  var total_bb_sum = 0
  var total_rb_sum = 0
  str = ["リッチ" + rate,"","","","","","","","","","","","","","","","","",""]
  data_for_write.push(str)
  datail = ["日付", "時間", "フロア", "機種名", "台番", "総回転", "BB回数", "BB確率", "RB回数", "RB確率", "合算", "差枚", "履歴","","","","","",""]
  data_for_write.push(datail)
  m_total_medal = 0
  for (var l = 0; l < 40; l++) {
    data_for_write.push(data[l + 1])
    m_total_medal += Number(data[l + 1][11])
    total_spin_sum += Number(data[l + 1][5])
    total_bb_sum += Number(data[l + 1][6])
    total_rb_sum += Number(data[l + 1][8])
  }
  total_bb_ave = 0
  if (total_bb_sum != 0) {
    total_bb_ave = total_spin_sum / total_bb_sum
  }
  total_rb_ave = 0
  if (total_rb_sum != 0) {
    total_rb_ave = total_spin_sum / total_rb_sum
  }
  total_b_ave = 0
  if ((total_bb_sum + total_rb_sum) != 0) {
    total_b_ave = total_spin_sum / (total_bb_sum + total_rb_sum)
  }
  str = ["トータル", "", "", "", "", total_spin_sum, total_bb_sum, total_bb_ave, total_rb_sum, total_rb_ave, total_b_ave, m_total_medal,"","","","","","",""]
  data_for_write.push(str)
  str = ["余白","","","","","","","","","","","","","","","","","",""]
  data_for_write.push(str)
  str = ["余白","","","","","","","","","","","","","","","","","",""]
  data_for_write.push(str)
}
function write_peach_data(hall, floor, rate, mode, sheet) {
  data = get_peach_data(hall, floor, rate, mode)
  var total_spin_sum = 0
  var total_bb_sum = 0
  var total_rb_sum = 0
  var total_at_sum = 0
  str = ["ピーチ" + rate,"","","","","","","","","","","","","","","","","",""]
  data_for_write.push(str)
  datail = ["日付", "時間", "フロア", "機種名", "台番", "総回転", "BB回数", "BB確率", "RB回数", "RB確率", "AT回数", "AT確率", "合算", "差枚", "履歴","","","",""]
  data_for_write.push(datail)
  m_total_medal = 0
  for (var l = 0; l < 40; l++) {
    data_for_write.push(data[l + 1])
    m_total_medal += Number(data[l + 1][13])
    total_spin_sum += Number(data[l + 1][5])
    total_bb_sum += Number(data[l + 1][6])
    total_rb_sum += Number(data[l + 1][8])
    total_at_sum += Number(data[l + 1][10])
  }
  total_bb_ave = 0
  if (total_bb_sum != 0) {
    total_bb_ave = total_spin_sum / total_bb_sum
  }
  total_rb_ave = 0
  if (total_rb_sum != 0) {
    total_rb_ave = total_spin_sum / total_rb_sum
  }
  total_at_ave = 0
  if (total_at_sum != 0) {
    total_at_ave = total_spin_sum / total_at_sum
  }
  total_b_ave = 0
  if ((total_bb_sum + total_rb_sum) != 0) {
    total_b_ave = total_spin_sum / (total_bb_sum + total_rb_sum)
  }
  str = ["トータル", "", "", "", "", total_spin_sum, total_bb_sum, total_bb_ave, total_rb_sum, total_rb_ave, total_at_sum, total_at_ave, total_b_ave, m_total_medal,"","","","",""]
  data_for_write.push(str)
  str = ["余白","","","","","","","","","","","","","","","","","",""]
  data_for_write.push(str)
  str = ["余白","","","","","","","","","","","","","","","","","",""]
  data_for_write.push(str)
}
function write_dice_data(hall, floor, rate, mode, sheet) {
  data = get_dice_data(hall, floor, rate, mode)
  var total_spin_sum = 0
  var total_bb_sum = 0
  var total_rb_sum = 0
  str = ["ダイス" + rate,"","","","","","","","","","","","","","","","","",""]
  data_for_write.push(str)
  datail = ["日付", "時間", "フロア", "機種名", "台番", "総回転", "JP回数", "JP確率", "UJP回数", "UJP確率", "差枚", "履歴","","","","","","",""]
  data_for_write.push(datail)
  m_total_medal = 0
  for (var l = 0; l < 40; l++) {
    data_for_write.push(data[l + 1])
    m_total_medal += Number(data[l + 1][10])
    total_spin_sum += Number(data[l + 1][5])
    total_bb_sum += Number(data[l + 1][6])
    total_rb_sum += Number(data[l + 1][8])
  }
  total_bb_ave = 0
  if (total_bb_sum != 0) {
    total_bb_ave = total_spin_sum / total_bb_sum
  }
  total_rb_ave = 0
  if (total_rb_sum != 0) {
    total_rb_ave = total_spin_sum / total_rb_sum
  }
  str = ["トータル", "", "", "", "", total_spin_sum, total_bb_sum, total_bb_ave, total_rb_sum, total_rb_ave, m_total_medal,"","","","","","","",""]
  data_for_write.push(str)
  str = ["余白","","","","","","","","","","","","","","","","","",""]
  data_for_write.push(str)
  str = ["余白","","","","","","","","","","","","","","","","","",""]
  data_for_write.push(str)
}
function write_cosmo_data(hall, floor, rate, mode, sheet) {
  data = get_cosmo_data(hall, floor, rate, mode)
  var total_spin_sum = 0
  var total_bb_sum = 0
  var total_first_hit_sum = 0
  var hit_only_count_sum = 0
  var hit_second_count_sum = 0

  str = ["コスモ" + rate,"","","","","","","","","","","","","","","","","",""]
  data_for_write.push(str)
  datail = ["日付", "時間", "フロア", "機種名", "台番", "総回転", "総B回数", "総B確率", "初当たり回数", "初当たり確率", "単発回数", "単発率", "2連回数", "2連率", "差玉", "履歴","","",""]
  data_for_write.push(datail)
  m_total_medal = 0
  for (var l = 0; l < 40; l++) {
    data_for_write.push(data[l + 1])
    m_total_medal += Number(data[l + 1][14])
    total_spin_sum += Number(data[l + 1][5])
    total_bb_sum += Number(data[l + 1][6])
    total_first_hit_sum += Number(data[l + 1][8])
    hit_only_count_sum += Number(data[l + 1][10])
    hit_second_count_sum += Number(data[l + 1][12])
  }

  total_bb_ave = 0
  if (total_bb_sum != 0) {
    total_bb_ave = total_spin_sum / total_bb_sum
  }
  total_f_hit_ave = 0
  if (total_first_hit_sum != 0) {
    total_f_hit_ave = total_spin_sum / total_first_hit_sum
  }
  total_hit_only_count_ave = 0
  if (hit_only_count_sum != 0) {
    total_hit_only_count_ave = hit_only_count_sum / total_first_hit_sum
  }
  total_hit_second_count_ave = 0
  if (hit_second_count_sum != 0) {
    total_hit_second_count_ave = hit_second_count_sum / total_first_hit_sum
  }
  str = ["トータル", "", "", "", "", total_spin_sum, total_bb_sum, total_bb_ave, total_first_hit_sum, total_f_hit_ave, hit_only_count_sum, total_hit_only_count_ave, hit_second_count_sum, total_hit_second_count_ave, m_total_medal,"","","",""]
  data_for_write.push(str)
  str = ["余白","","","","","","","","","","","","","","","","","",""]
  data_for_write.push(str)
  str = ["余白","","","","","","","","","","","","","","","","","",""]
  data_for_write.push(str)
}
function write_mate_data(hall, floor, rate, mode, sheet) {
  data = get_mate_data(hall, floor, rate, mode)
  var total_spin_sum = 0
  var total_bb_sum = 0
  var total_first_hit_sum = 0
  str = ["メイト" + rate,"","","","","","","","","","","","","","","","","",""]
  data_for_write.push(str)
  datail = ["日付", "時間", "フロア", "機種名", "台番", "総回転", "総B回数", "総B確率", "初当たり回数", "初当たり確率", "差玉", "履歴","","","","","","",""]
  data_for_write.push(datail)
  m_total_medal = 0
  for (var l = 0; l < 40; l++) {
    data_for_write.push(data[l + 1])
    m_total_medal += Number(data[l + 1][10])
    total_spin_sum += Number(data[l + 1][5])
    total_bb_sum += Number(data[l + 1][6])
    total_first_hit_sum += Number(data[l + 1][6])
  }
  total_bb_ave = 0
  if (total_bb_sum != 0) {
    total_bb_ave = total_spin_sum / total_bb_sum
  }
  total_f_hit_ave = 0
  if (total_first_hit_sum != 0) {
    total_f_hit_ave = total_spin_sum / total_first_hit_sum
  }
  str = ["トータル", "", "", "", "", total_spin_sum, total_bb_sum, total_bb_ave, total_first_hit_sum, total_f_hit_ave, m_total_medal,"","","","","","","",""]
  data_for_write.push(str)
  str = ["余白","","","","","","","","","","","","","","","","","",""]
  data_for_write.push(str)
  str = ["余白","","","","","","","","","","","","","","","","","",""]
  data_for_write.push(str)
}


function write_day_data_to_sheet(hall, fileId) {
  let file = DriveApp.getFileById(fileId); //ファイルIDでファイルを取得してみる
  var sheet = SpreadsheetApp.open(file).getActiveSheet();
  var range = sheet.getRange("E1:Z1000");
  var rule = SpreadsheetApp.newConditionalFormatRule()
    .whenNumberLessThan(0)
    .setFontColor("#FF0000")
    .setRanges([range])
    .build();
  var rules = sheet.getConditionalFormatRules();
  write_cosmo_data(hall, "01", "0.1", 0, sheet)
  write_w_rose_data(hall, "02", "0.05", 0, sheet)
  write_rich_data(hall, "03", "0.05", 0, sheet)
  write_s_rose_data(hall, "04", "0.05", 0, sheet)
  write_cosmo_data(hall, "05", "0.04", 0, sheet)
  write_mate_data(hall, "06", "0.04", 0, sheet)
  write_rich_data(hall, "07", "0.8", 0, sheet)
  write_peach_data(hall, "08", "0.2", 0, sheet)
  write_s_rose_data(hall, "09", "0.2", 0, sheet)
  write_rich_data(hall, "10", "0.2", 0, sheet)
  write_w_rose_data(hall, "11", "0.2", 0, sheet)
  write_s_rose_data(hall, "12", "0.2", 0, sheet)
  write_dice_data(hall, "13", "0.2", 0, sheet)
  write_s_rose_data(hall, "14", "0.4", 0, sheet)
  write_s_rose_data(hall, "15", "0.8", 0, sheet)
  sheet.clear()
  sheet.getRange("A1:Z1000").applyRowBanding(SpreadsheetApp.BandingTheme.LIGHT_GREEN, false, false);

  sheet.getRange("E1:Z1000").setNumberFormat('#,##0;[Red]-#,##0');
  for (var l = 0; l < data_for_write.length; l++) {
    sheet.appendRow(data_for_write[l])
  }
  sheet.getRange("L3:L43").setNumberFormat("0.0%")
  sheet.getRange("L183:L223").setNumberFormat("0.0%")
  sheet.getRange("N3:N43").setNumberFormat("0.0%")
  sheet.getRange("N183:N223").setNumberFormat("0.0%")
  sheet.getRange("P138:P673").setNumberFormat("0.0%") 
  replace_zero_to_nil(sheet)
}

function write_night_data_to_sheet(hall, fileId, mode) {
  let file = DriveApp.getFileById(fileId); //ファイルIDでファイルを取得してみる
  var sheet = SpreadsheetApp.open(file).getActiveSheet();
  var range = sheet.getRange("E1:Z1000");
  var rule = SpreadsheetApp.newConditionalFormatRule()
    .whenNumberLessThan(0)
    .setFontColor("#FF0000")
    .setRanges([range])
    .build();
  var rules = sheet.getConditionalFormatRules();
  write_s_rose_data(hall, "01", "0.05", mode, sheet)
  write_s_rose_data(hall, "02", "0.05", mode, sheet)
  write_w_rose_data(hall, "03", "0.05", mode, sheet)
  write_rich_data(hall, "04", "0.05", mode, sheet)
  write_cosmo_data(hall, "05", "0.04", mode, sheet)
  write_mate_data(hall, "06", "0.04", mode, sheet)
  write_peach_data(hall, "07", "0.2", mode, sheet)
  write_dice_data(hall, "08", "0.2", mode, sheet)
  write_rich_data(hall, "09", "0.2", mode, sheet)
  write_s_rose_data(hall, "10", "0.2", mode, sheet)
  write_s_rose_data(hall, "11", "0.2", mode, sheet)
  write_w_rose_data(hall, "12", "0.2", mode, sheet)
  write_w_rose_data(hall, "13", "0.2", mode, sheet)
  write_w_rose_data(hall, "14", "0.4", mode, sheet)
  write_rich_data(hall, "15", "0.4", mode, sheet)
  sheet.clear()
  sheet.getRange("A1:Z1000").applyRowBanding(SpreadsheetApp.BandingTheme.LIGHT_GREEN, false, false);

  sheet.getRange("E1:Z1000").setNumberFormat('#,##0;[Red]-#,##0');
  sheet.getRange(1, 1, data_for_write.length, data_for_write[0].length).setValues(data_for_write);
  sheet.getRange("L183:L223").setNumberFormat("0.0%")
  sheet.getRange("N183:N223").setNumberFormat("0.0%")  
  replace_zero_to_nil(sheet)
}

function get_day_data() {
  var today = new Date();
  /*取得した日付を西暦月日で表示してformatdateに代入*/
  var formatDate = Utilities.formatDate(today, "JST", "yyyy/M/d");
  day_data_list = get_datasheat_id("1cNDIpqw1CR3d8rcucObIBYRCYttyncLq")
  day_data_id = ""
  for (var p = 0; p < day_data_list.length; p++) {
    if (day_data_list[p][0] == formatDate) {
      day_data_id = day_data_list[p][1]
    }
  }
  if (day_data_id == ""){
    day_data_id = CreateNewSpreadSheet(formatDate, "1cNDIpqw1CR3d8rcucObIBYRCYttyncLq")
  }
  write_day_data_to_sheet("da", day_data_id)
}

function get_night_data() {
  night_data_list = get_datasheat_id("1gR7AxBakfccRZJoo251PxWule7j8ig_8")
  night_data_id = ""
  for (var p = 0; p < night_data_list.length; p++) {
    if (night_data_list[p][0] == "夜ドラ当日用") {
      night_data_id = night_data_list[p][1]
    }
  }
  write_night_data_to_sheet("ni", night_data_id,0)
}
function get_yesterday_night_data(){
  var today = new Date();
  var day = today.getDate()
  today.setDate(day - 1)
  /*取得した日付を西暦月日で表示してformatdateに代入*/
  var formatDate = Utilities.formatDate(today, "JST", "yyyy/M/d");
  night_data_list = get_datasheat_id("1gR7AxBakfccRZJoo251PxWule7j8ig_8")
  last_night_data_id = ""
  for (var p = 0; p < night_data_list.length; p++) {
    if (night_data_list[p][0] == formatDate) {
      last_night_data_id = night_data_list[p][1]
    }
  }
  if (last_night_data_id == ""){
    last_night_data_id = CreateNewSpreadSheet(formatDate, "1gR7AxBakfccRZJoo251PxWule7j8ig_8")
  }
  write_night_data_to_sheet("ni", last_night_data_id,1)

  
}
function replace_zero_to_nil(sheet){
  var textFinder2 = sheet.createTextFinder("余白");
  textFinder2.replaceAllWith("");
}