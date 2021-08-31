<?php

namespace App\modules\chat\actions;

use Amp\Http\Server\Request;
use Amp\Websocket\Client;
use Amp\Websocket\Server\Gateway;

class SendMessageAction implements ActionInterface
{

    function run(Gateway $gateway, Client $client, Request $request, array $input)
    {
        if (!isset($input['message'])) {
            return;
        }
        $data = [];
        $data['clientId'] = $client->getId();
        $data['message'] = $input['message'];
        $message = json_encode($data);
        $gateway->broadcast($message);
    }
}
