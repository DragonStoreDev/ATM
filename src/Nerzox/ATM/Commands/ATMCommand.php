<?php

namespace Nerzox\ATM\Commands;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use Nerzox\ATM\{
    Loader,
    API
};
use pocketmine\Player;
use jojoe77777\FormAPI\SimpleForm;
use onebone\economyapi\EconomyAPI;
use pocketmine\utils\Config;

class ATMCommand extends PluginCommand{

    protected $config;
    private $loader;

    #Message
    private $noplayingame;
    private $atmclaim;
    private $nowait;
    private $nopermission;

    #Config
    private $dividedtime;
    private $boolpermission;
    private $permissionname;
    private $timewaitbeforeuse;
    private $language;

    public function __construct(Loader $loader)
    {
        $this->description = 'Permet de recuperer votre ATM.';
        $this->usageMessage = '/atm';
        parent::__construct('atm', $loader);
        $this->loader = $loader;
        $this->config = new Config($this->loader->getDataFolder() . 'config.yml', Config::YAML);
    }

    public function execute(CommandSender $player, string $commandLabel, array $args)
    {
        #Message

        $this->noplayingame = $this->getConfig()->get("command-no-use-in-game");
        $this->atmclaim = $this->getConfig()->get("atm-claim");
        $this->nopermission = $this->getConfig()->get("no-permission");
        $this->nowait = $this->getConfig()->get('no-wait-can-recover-atm-time');

        #Config

        $this->boolpermission = $this->getConfig()->get("activate-permission");
        $this->dividedtime = $this->getConfig()->get("divided-time");
        $this->permissionname = $this->getConfig()->get("permission-name");
        $this->timewaitbeforeuse = $this->getConfig()->get("time-after-can-recover-atm");
        $this->language = $this->getConfig()->get("language");

        if (!$player instanceof Player) {
            $player->sendMessage($this->noplayingame);
            return true;
        }
        if ($this->boolpermission === true) {
            if (!$player->hasPermission($this->permissionname)) {
                $player->sendMessage($this->nopermission);
                return true;
            }
        }
        $form = new SimpleForm(function (Player $player, int $data = null) {
            if (is_null($data)) {
                return true;
            }
            switch ($data) {
                case 0:
                    if (API::getATM($player, time()) > $this->timewaitbeforeuse) {
                        EconomyAPI::getInstance()->addMoney($player, number_format(API::getATM($player, time()) / $this->dividedtime, 2));
                        API::setATM($player, time());
                        $player->sendMessage($this->atmclaim);
                    } else {
                        $player->sendMessage($this->nowait);
                    }
                    break;
            }
        });
        if ($this->language === 'fr') {
            $form->setTitle('§f§l>> §4ATM');
            $form->setContent("Voulez-vous prendre votre ATM ? Nombre total des gains : " . round(API::getATM($player, time()) / $this->dividedtime, 1) . '$');
            $form->addButton('§2Oui !');
            $form->addButton('§4Non.');
        }elseif($this->language === 'en'){
            $form->setTitle('§f§l>> §4ATM');
            $form->setContent("Do you want to take your ATM ? Total number of wins : " . round(API::getATM($player, time()) / $this->dividedtime, 1) . '$');
            $form->addButton('§2Yes !');
            $form->addButton('§4No.');
        }else{
            $form->setTitle('§f§l>> §4ATM');
            $form->setContent("Do you want to take your ATM ? Total number of wins : " . round(API::getATM($player, time()) / $this->dividedtime . '$', 1));
            $form->addButton('§2Yes !');
            $form->addButton('§4No.');
        }
        $form->sendToPlayer($player);
        return $form;
    }
    protected function getConfig() : Config{
        return $this->config;
    }
}
