<?php

namespace App\modules\chat\handlers;

use Amp\Http\Server\Request;
use Amp\Http\Server\Response;
use Amp\Promise;
use Amp\Success;
use Amp\Websocket\Client;
use Amp\Websocket\Message;
use Amp\Websocket\Server\ClientHandler;
use Amp\Websocket\Server\Gateway;

use App\modules\chat\actions\SendMessageAction;

use function Amp\call;

class ChatClientHandler implements ClientHandler
{
    public array $actions = [
        'actionMessage' => SendMessageAction::class
    ];
    public function handleHandshake(
        Gateway $gateway,
        Request $request,
        Response $response
    ): Promise {
        return new Success($response);
    }

    public function handleClient(Gateway $gateway, Client $client, Request $request, Response $response): Promise
    {
        return call(function () use ($gateway, $client, $request) {
            while ($message = yield $client->receive()) {
                assert($message instanceof Message);
                $data = yield $message->buffer();
                $input = json_decode($data, true);
                if (isset($input['action']) && isset($this->actions[$input['action']])) {
                    call_user_func([$this->actions[$input['action']], 'run'], $gateway, $client, $request, $input);
                }
            }
        });
    }
}
