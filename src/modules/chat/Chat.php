<?php

namespace App\modules\chat;

use Amp\Websocket\Server\Websocket;
use App\Module;
use App\modules\chat\handlers\ChatClientHandler;
use App\Server;

class Chat implements Module
{
    private Server $server;

    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    public function init(): void
    {
        $websocket = new Websocket(new ChatClientHandler());

        $this->server->router->addRoute('GET', '/chat/{token}', $websocket);
    }
}
