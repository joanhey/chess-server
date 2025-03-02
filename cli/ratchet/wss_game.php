<?php

namespace ChessServer\Cli\Ratchet;

use ChessServer\Command\CommandParser;
use ChessServer\Command\Game\CommandContainer;
use ChessServer\Socket\RatchetClientStorage;
use ChessServer\Socket\RatchetWebSocket;
use Dotenv\Dotenv;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use React\EventLoop\Factory;
use React\Socket\LimitingServer;
use React\Socket\Server;
use React\Socket\SecureServer;

require __DIR__  . '/../../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__.'/../../');
$dotenv->load();

$logger = new Logger('log');
$logger->pushHandler(new StreamHandler(__DIR__.'/../../storage' . '/pchess.log', Logger::INFO));

$clientStorage = new RatchetClientStorage($logger);

$parser = new CommandParser(new CommandContainer());

$webSocket = (new RatchetWebSocket($parser))->init($clientStorage);

$loop = Factory::create();

$server = new Server("{$_ENV['WSS_ADDRESS']}:{$_ENV['WSS_GAME_PORT']}", $loop);

$secureServer = new SecureServer($server, $loop, [
    'local_cert'  => __DIR__  . '/../../ssl/fullchain.pem',
    'local_pk' => __DIR__  . '/../../ssl/privkey.pem',
    'verify_peer' => false,
]);

$limitingServer = new LimitingServer($secureServer, 50);

$httpServer = new HttpServer(new WsServer($webSocket));

$ioServer = new IoServer($httpServer, $limitingServer, $loop);

$ioServer->run();
