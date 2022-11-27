<?php

namespace App\Http\Controllers;

use DB;
use Log;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

use App\Models\dai;
use App\Models\floor;
use App\Models\kisyu;
use App\Models\hall;
use App\Models\account;



class LineApiController extends Controller
{

    protected $access_token;
    protected $channel_secret;

    public function __construct()
    {
        // :point_down: アクセストークン
        $this->access_token = env('LINE_ACCESS_TOKEN');
        // :point_down: チャンネルシークレット
        $this->channel_secret = env('LINE_CHANNEL_SECRET');
    }

    // Webhook受取処理
    public function postWebhook(Request $request) {
        //return view ('test', ['test1' => "liness", 'test2' => "sss"]);
        $input = $request->all();
        Log::info($input);

/* $inputの中身 俺
 [2022-05-08 15:32:09] local.INFO: array (
  'destination' => 'Uedf36c5610b316476c45ac7ead371392',
  'events' =>
  array (
    0 =>
    array (
      'type' => 'message',
      'message' =>
      array (
        'type' => 'text',
        'id' => '16050162325945',
        'text' => 'てすと',
      ),
      'webhookEventId' => '01G2H5WHRXFBNZXKDKY17ECTQ2',
      'deliveryContext' =>
      array (
        'isRedelivery' => false,
      ),
      'timestamp' => 1651991529024,
      'source' =>
      array (
        'type' => 'user',
        'userId' => 'Uf89fbb3ef62700d920dd358aeda106f4',
      ),
      'replyToken' => 'a6224e91a62843719fafb4e4e705a90e',
      'mode' => 'active',
    ),
  ),
)



■ぽんちゃん
[2022-05-13 19:24:02] local.INFO: ユーザーを追加しました。 user_id = U8fa7c0643a7b087ec0f5baef09901465
[2022-05-13 19:24:22] local.INFO: array (
  'destination' => 'Uedf36c5610b316476c45ac7ead371392',
  'events' =>
  array (
    0 =>
    array (
      'type' => 'message',
      'message' =>
      array (
        'type' => 'image',
        'id' => '16079851565649',
        'contentProvider' =>
        array (
          'type' => 'line',
        ),
      ),
      'webhookEventId' => '01G2YF5BME04ZWXJE6XNH1CH1S',
      'deliveryContext' =>
      array (
        'isRedelivery' => false,
      ),
      'timestamp' => 1652437462298,
      'source' =>
      array (
        'type' => 'user',
        'userId' => 'U8fa7c0643a7b087ec0f5baef09901465',
      ),
      'replyToken' => '854edd1321ed4fd9be04663c32958ce3',
      'mode' => 'active',
    ),
  ),
)
[2022-05-13 19:24:23] local.INFO: 返信成功

 */

        // ユーザーがどういう操作を行った処理なのかを取得
        $type  = $input['events'][0]['type'];

        // タイプごとに分岐
        switch ($type) {
            // メッセージ受信
            case 'message':
                // 返答に必要なトークンを取得
                $reply_token = $input['events'][0]['replyToken'];
                // テスト投稿の場合
                if ($reply_token == '00000000000000000000000000000000') {
                    Log::info('Succeeded');
                    return;
                }
                // Lineに送信する準備
                $http_client = new \LINE\LINEBot\HTTPClient\CurlHTTPClient($this->access_token);
                $bot         = new \LINE\LINEBot($http_client, ['channelSecret' => $this->channel_secret]);
                // LINEの投稿処理
                $message_data = "ぷっぷくのご加護がありますように。";
                $response     = $bot->replyText($reply_token, $message_data);

                // Succeeded
                if ($response->isSucceeded()) {
                    Log::info('返信成功');
                    break;
                }
                // Failed
                Log::error($response->getRawBody());
                break;
                break;

                // 友だち追加 or ブロック解除
            case 'follow':
                // ユーザー固有のIDを取得
                $mid = $request['events'][0]['source']['userId'];
                // ユーザー固有のIDはどこかに保存しておいてください。メッセージ送信の際に必要です。
                //LineUser::updateOrCreate(['line_id' => $mid]);
                Log::info("ユーザーを追加しました。 user_id = " . $mid);
                break;

                // グループ・トークルーム参加
            case 'join':
                Log::info("グループ・トークルームに追加されました。");
                break;

                // グループ・トークルーム退出
            case 'leave':
                Log::info("グループ・トークルームから退出させられました。");
                break;

                // ブロック
            case 'unfollow':
                Log::info("ユーザーにブロックされました。");
                break;

            default:
                Log::info("the type is" . $type);
                break;
        }

        return;
    }

    // メッセージ送信用
    public function sendMessage(Request $request) {
        // Lineに送信する準備
        $http_client = new \LINE\LINEBot\HTTPClient\CurlHTTPClient($this->access_token);
        $bot         = new \LINE\LINEBot($http_client, ['channelSecret' => $this->channel_secret]);

        $line_user_id = "Uf89fbb3ef62700d920dd358aeda106f4"; //yan
        $message = "ぷっぷくのご加護がありますように。";
        $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($message);
        $response    = $bot->pushMessage($line_user_id, $textMessageBuilder);

        // 配信成功・失敗
        if ($response->isSucceeded()) {
            Log::info('Line 送信完了');
        } else {
            Log::error('投稿失敗: ' . $response->getRawBody());
        }
    }









}
