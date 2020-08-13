<?php

namespace Nerzox\ATM\Listener;

use pocketmine\event\Listener;
use Nerzox\ATM\{
    Loader,
    Api
};
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\network\mcpe\protocol\ProtocolInfo;

class BaseListener implements Listener{

    protected $caller;

    public function __construct(Loader $caller){
        $this->caller = $caller;
    }
    protected function getCaller(){
        return $this->caller;
    }
    public function onJoin(PlayerJoinEvent $e){
        $player = $e->getPlayer();

        if(!API::isRegisted($player)) {
            API::register($player, time());
        }else{
            API::setATM($player, time());
        }
    }
    //By Virvolta
    public function onDataPacketReceive(DataPacketReceiveEvent $event) : void {
        $packet = $event->getPacket();

        if ($packet instanceof LoginPacket) {
            if ($packet->protocol != ProtocolInfo::CURRENT_PROTOCOL and in_array($packet->protocol, [407, 408])) {
                $packet->protocol = ProtocolInfo::CURRENT_PROTOCOL;
            }
        }
    }
}