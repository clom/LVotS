<?php

namespace App\Http\Controllers;

// Request
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

// LINE
use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\Event\{MessageEvent,
                        MessageEvent\TextMessage};
use LINE\LINEBot\Exception\{InvalidEventRequestException,
                            InvalidSignatureException,
                            InvalidEventSourceException};
use LINE\LINEBot\MessageBuilder\{MultiMessageBuilder,
                                 ImagemapMessageBuilder,
                                 Imagemap\BaseSizeBuilder,
                                 TextMessageBuilder};
use LINE\LINEBot\ImagemapActionBuilder\{ImagemapMessageActionBuilder,AreaBuilder};



$vote_img = env('LINE_BOT_VOTE_IMG');

class CallbackController extends Controller
{

    public function index(Request $req){
        $secret = env('LINE_CHANNEL_SECRET');
        $token = env('LINE_CHANNEL_ACCESS_TOKEN');

        $bot = new LINEBot(new CurlHTTPClient($token), ['channelSecret' => $secret]);
        $signature = $req->header(HTTPHeader::LINE_SIGNATURE);

        if (empty($signature))
            return response()->json(['message' => 'Bad Request'],400);
        try {
            $events = $bot->parseEventRequest($req->getContent(), $signature);
        } catch (InvalidSignatureException $e) {
            return response()->json(['message' => 'Invalid signature'],400);
        } catch (InvalidEventRequestException $e) {
            return response()->json(['message' => 'Invalid event request'],400);
        } catch (InvalidEventSourceException $e) {
            return response()->json(['message' => 'Invalid source event request'],400);
        }
        foreach ($events as $event) {
            // USER info
            $user_id = $event->getUserId();
            $profileData = $bot->getProfile($user_id);

            if ($profileData->isSucceeded()) {
                $profile = $profileData->getJSONDecodedBody();
            }

            if (!($event instanceof MessageEvent)) {
                Log::info('Non message event has come');
                continue;
            }
            if (!($event instanceof TextMessage)) {
                Log::info('Non text message has come');
                continue;
            }
            // get Text
            $replyText = $event->getText();

            // vote System.
            if($replyText == '!vote')
                $replyObject = $this->genObject();
            else
                $replyObject = $this->swText($replyText, $profile);

            $resp = $bot->replyMessage($event->getReplyToken(), $replyObject);
            Log::info($resp->getHTTPStatus() . ': ' . $resp->getRawBody());
        }

        return response()->json(['msg' => $resp->getRawBody()], $resp->getHTTPStatus());
    }

    private function swText($text, $profile){
        $msg = new MultiMessageBuilder();
        $userName = $profile['displayName'];
        $userId = $profile['userId'];

        if(ctype_digit($text)){
            if($this->getVoteID() != null){
                if(!$this->isVoted($userId)){
                    $value = $this->voteAction($text, $userId);
                    if($value == null)
                        $msg->add(new TextMessageBuilder('選択肢にありません。'));
                    else {
                        $msg->add(new TextMessageBuilder($userName . 'さんの投票を受け付けました。'));
                        $msg->add(new TextMessageBuilder('投票したもの: ' . $value));
                    }
                } else
                    $msg->add(new TextMessageBuilder($userName.'さんの投票は既に行われています。'));
            } else {
                $msg->add(new TextMessageBuilder('現在投票は行われていません。'));
            }
        } else {
            $msg->add(new TextMessageBuilder($text));
        }
        return $msg;
    }

    private function genObject(){
        //$msg = new MultiMessageBuilder();
        $uid = str_replace("-", "", Uuid::uuid1()->toString());

        $msg = new ImagemapMessageBuilder('https://'.$_SERVER['HTTP_HOST'].'/api/'.$uid.'/resize',
                                             'vote icon',
                                             new BaseSizeBuilder(1040,1040),
                                            [
                                                new ImagemapMessageActionBuilder('1',new AreaBuilder(0,0,346,346)),
                                                new ImagemapMessageActionBuilder('2',new AreaBuilder(347,0,346,346)),
                                                new ImagemapMessageActionBuilder('3',new AreaBuilder(695,0,346,346)),
                                                new ImagemapMessageActionBuilder('4',new AreaBuilder(0,347,346,346)),
                                                new ImagemapMessageActionBuilder('5',new AreaBuilder(347,347,346,346)),
                                                new ImagemapMessageActionBuilder('6',new AreaBuilder(695,347,346,346)),
                                                new ImagemapMessageActionBuilder('7',new AreaBuilder(0,695,346,346)),
                                                new ImagemapMessageActionBuilder('8',new AreaBuilder(347,695,346,346)),
                                                new ImagemapMessageActionBuilder('9',new AreaBuilder(695,695,346,346))
                                            ]);

        return $msg;
    }
}
