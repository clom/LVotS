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

        return response()->json([], 200);
    }
}
