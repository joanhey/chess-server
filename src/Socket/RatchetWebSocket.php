<?php

namespace ChessServer\Socket;

use ChessServer\Command\LeaveCommand;
use ChessServer\Exception\InternalErrorException;
use ChessServer\Exception\ParserException;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class RatchetWebSocket extends ChesslaBlabSocket implements MessageComponentInterface
{
    public function onOpen(ConnectionInterface $conn)
    {
        $this->clientStorage->attach($conn);

        $this->clientStorage->getLogger()->info('New connection', [
            'id' => $conn->resourceId,
            'n' => $this->clientStorage->count()
        ]);
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        if (strlen($msg) > 4096) {
            return $this->getClientStorage()->sendToOne($from->resourceId, [
                'error' => 'Internal server error',
            ]);
        }

        try {
            $cmd = $this->parser->validate($msg);
        } catch (ParserException $e) {
            return $this->getClientStorage()->sendToOne($from->resourceId, [
                'error' => 'Command parameters not valid',
            ]);
        }

        try {
            $cmd->run($this, $this->parser->argv, $from->resourceId);
        } catch (InternalErrorException $e) {
            return $this->getClientStorage()->sendToOne($from->resourceId, [
                'error' => 'Internal server error',
            ]);
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        if ($gameMode = $this->gameModeStorage->getById($conn->resourceId)) {
            $this->gameModeStorage->delete($gameMode);
            $this->getClientStorage()->sendToMany($gameMode->getResourceIds(), [
                '/leave' => [
                    'action' => LeaveCommand::ACTION_ACCEPT,
                ],
            ]);
        }

        $this->clientStorage->dettachById($conn->resourceId);

        $this->clientStorage->getLogger()->info('Closed connection', [
            'id' => $conn->resourceId,
            'n' => $this->clientStorage->count()
        ]);
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $conn->close();

        $this->clientStorage->getLogger()->info('Occurred an error', ['message' => $e->getMessage()]);
    }
}
