<?php

namespace App\modules\chat\actions;

use Amp\Http\Server\Request;
use Amp\Websocket\Client;
use Amp\Websocket\Server\Gateway;

interface ActionInterface
{
    function run(Gateway $gateway, Client $client, Request $request, array $input);
}
