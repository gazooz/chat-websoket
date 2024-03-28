<?php

namespace App;

use Amp\Http\Server\HttpServer;
use Amp\Http\Server\Options;
use Amp\Http\Server\Router;
use Amp\Log\ConsoleFormatter;
use Amp\Log\StreamHandler;
use Amp\Loop;
use Amp\Promise;
use Amp\Socket\Server as SocketServer;
use Monolog\Logger;

use function Amp\ByteStream\getStdout;

class Application
{
    //Todo: move to config
    public Router $router;
    public Logger $logger;
    public Options $options;


    public array $modules;
    private array $components = [];

    public function __construct($config)
    {
        foreach ($config as $param => $value) {
            if (property_exists($this, $param)) {
                $this->$param = $value;
            }
        }
    }

    public function run()
    {
        Loop::run(
            function (): Promise {
                $this->logger = new Logger('Application');
                $consoleHandler = new StreamHandler(getStdout());
                $consoleHandler->setFormatter(new ConsoleFormatter);
                $this->logger->pushHandler($consoleHandler);

                $this->router = new Router();
                $this->options = new Options();

                $sockets = [SocketServer::listen('0.0.0.0:8085')];

                foreach ($this->modules as $moduleConfig) {
                    if (isset($moduleConfig['id'], $moduleConfig['class']) && class_exists(
                            $class = $moduleConfig['class']
                        )) {
                        /** @var Module $module */

                        $module = new $class($this);
                        $module->init();
                        $this->components[$moduleConfig['id']] = $module;
                    }
                }

                $server = new HttpServer($sockets, $this->router, $this->logger, $this->options);

                return $server->start();
            }
        );
    }
}
