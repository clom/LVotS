<?php

namespace App\Http\Controllers;

// Request
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

// LINE
use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\Event\{MessageEvent,
                        MessageEvent\TextMessage};
use LINE\LINEBot\Exception\{InvalidEventRequestException,
                            InvalidSignatureException,
                            InvalidEventSourceException};
use LINE\LINEBot\MessageBuilder\MultiMessageBuilder;

// Redis
use Predis\Client;


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
            $resp = $bot->replyText($event->getReplyToken(), $replyText);
            Log::info($resp->getHTTPStatus() . ': ' . $resp->getRawBody());
        }

        return response()->json([], 200);
    }
}
