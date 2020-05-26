<?php

namespace App;

use Illuminate\Console\Command;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use App\Socket\ChatSocket;

class Admin
{
    private static $process;

    public static function runServer()
    {
        $descriptorspec = array(
            0 => array("pipe", "r"),  // stdin - канал, из которого дочерний процесс будет читать
            1 => array("pipe", "w"),  // stdout - канал, в который дочерний процесс будет записывать 
            2 => array("file", "/tmp/error-output.txt", "a") // stderr - файл для записи
         );
         
        self::$process = proc_open('cd .. && php artisan chat_server:serve', $descriptorspec, $pipes);
        
        if (self::$process) {
            return 'server is started...';
        } else {
            return 'error';
        }
    }

    public static function stopServer()
    {
        $responseCode = proc_close(self::$process);

        if ($responseCode !== -1) {
            return 'server is stoped...';
        } else {
            return 'error';
        }
    }
}
