<?php

namespace Nerzox\ATM;

use pocketmine\event\Listener;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use Nerzox\ATM\Listener\BaseListener;

class Loader extends PluginBase implements Listener {

    public static $instance;
    protected $bdd;

    public function onEnable(){
        @mkdir($this->getDataFolder() . 'Database/');
        self::$instance = $this;
        $this->bdd = new \SQLite3($this->getDataFolder() . 'Database/ATM.db');
        Server::getInstance()->getPluginManager()->registerEvents(new BaseListener($this), $this);
        Server::getInstance()->getCommandMap()->register('Commands', new Commands\ATMCommand($this));
        API::init();

        if(!Server::getInstance()->getPluginManager()->getPlugin('EconomyAPI')){
            $this->getLogger()->error('Le plugin EconomyAPI n\'a pas été trouvé. Veuillez l\'installer pour utiliser ce plugin.');
            Server::getInstance()->getPluginManager()->disablePlugin($this);
            return true;
        }
        //By Virvolta
        if (!in_array(ProtocolInfo::CURRENT_PROTOCOL, [407, 408])) {
            $this->getLogger()->error("votre protocole n'est pas 407 ou 408");
            $this->getServer()->shutdown();
        }
    }

    public static function getInstance() : self{
        return self::$instance;
    }
}