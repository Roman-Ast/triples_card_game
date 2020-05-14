<?php

namespace App;

use Illuminate\Console\Command;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use App\Socket\ChatSocket;

class Admin
{
    private $server;

    public function runServer()
    {
        $port = 8050;

        $this->server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new ChatSocket()
                )
            ),
            $port
        );

        $this->server->run();

        if ($this->server) {
            return ['server_is_run' => true];
        }
    }

    public function stopServer()
    {
        $this->server->close();

        return ['server_is_stopped' => true];
    }
}
