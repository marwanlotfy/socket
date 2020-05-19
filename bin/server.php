<?php

use App\Pusher;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;

require __DIR__.'/../bootstrap.php';

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new Pusher()
        )
    ),
    8080
);

$server->run();