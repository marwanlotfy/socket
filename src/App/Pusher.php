<?php
namespace App;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

class Pusher implements MessageComponentInterface {
    protected $clientsStorage;
    protected $authorizedClients = [];

    public function __construct() {
        $this->clientsStorage = new \SplObjectStorage;
    }
    public function onOpen(ConnectionInterface $conn) {
        $this->clientsStorage->attach($conn);

        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $parsedMsg = json_decode($msg);
        // here is check cred for user to mark as authenticated so can recive notifications
        if ($parsedMsg->header=='handshake') {
            $this->authorizeHandshake($from,$parsedMsg->userId,$parsedMsg->token);
            echo " Count" . count($this->authorizedClients) . "\n";
        }
        // we accept messages to be send if and only if the sender is the main server

        if ($this->isServer($parsedMsg)) {
            if ($parsedMsg->header == 'message') {
                $this->pushMsg($parsedMsg);
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clientsStorage->detach($conn);
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        $conn->close();
    }

    public function authorizeHandshake($from,$userId,$token)
    {
        $client = new Client($userId,$token);
        if ($client->validateAccess()) {
            $this->authorizedClients[$userId]=$from;
            echo "New Client connection! : id {$userId}\n";
        }else{
            $from->close();
            echo "UnAuthorized Attempt \n";
        }
    }

    public function pushMsg($msg)
    {
        if (isset($this->authorizedClients[$msg->to])) {
            $this->authorizedClients[$msg->to]->send(json_encode($msg->body));
        }else{
            echo "offline resource {$msg->to} \n";
        }
    }

    public function isServer($msg)
    {
        return ($msg->userId == env('MAIN_SERVER_ID') && $msg->token == env('MAIN_SERVER_SECRET'));
    }
}