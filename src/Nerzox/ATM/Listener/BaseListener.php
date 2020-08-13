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
use pocketmine\utils\Config;

class BaseListener implements Listener{

    protected $caller;
    protected $config;
    private $reset;

    public function __construct(Loader $caller){
        $this->caller = $caller;
    }
    protected function getCaller(){
        return $this->caller;
    }
    protected function getConfig() : Config{
        $this->config = new Config($this->getCaller()->getDataFolder() . 'config.yml');
        return $this->config;
    }
    public function onJoin(PlayerJoinEvent $e){
        $player = $e->getPlayer();
        $this->reset = $this->getConfig()->get("reset-deconnect-player");
        if($this->reset === true) {
            API::setATM($player, time());
        }else{
            if(!API::isRegisted($player)){
                API::setATM($player, time());
            }
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