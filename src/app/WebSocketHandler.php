<?php

namespace Millsoft\Queuer;

use Nekland\Woketo\Core\AbstractConnection;
use Nekland\Woketo\Message\TextMessageHandler;

class WebSocketHandler extends TextMessageHandler
{
    public $connection = null;

    public function onConnection(AbstractConnection $connection)
    {

        \writelog("websocket on.connect");
        // Doing something when the client is connected ?
        // This method is totally optional.
        $this->connection = $connection;
        $connection->write("CONNECTED WITH WEBSOCKET SERVER");
    }

    public function onMessage(string $data, AbstractConnection $connection)
    {
        // Sending back the received data
    }
}