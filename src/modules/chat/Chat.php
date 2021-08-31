<?php

namespace App\modules\chat;

use Amp\Websocket\Server\Websocket;
use App\Module;
use App\modules\chat\handlers\ChatClientHandler;
use App\Application;

class Chat implements Module
{
    private Application $server;

    public function __construct(Application $server)
    {
        $this->server = $server;
    }

    public function init(): void
    {
        $websocket = new Websocket(new ChatClientHandler());
        $this->server->router->addRoute('GET', 'ws/chat', $websocket);
    }
}
