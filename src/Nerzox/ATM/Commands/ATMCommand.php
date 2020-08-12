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

class ATMCommand extends PluginCommand{

    public function __construct(Loader $loader)
    {
        $this->description = 'Permet de recuperer votre ATM.';
        $this->usageMessage = '/atm';
        parent::__construct('atm', $loader);
    }

    public function execute(CommandSender $player, string $commandLabel, array $args)
    {
        if(!$player instanceof Player){
            $player->sendMessage('§4Veuillez utiliser cette commande en jeu.');
            return true;
        }
        $form = new SimpleForm(function(Player $player, int $data = null){
           if(is_null($data)){
               return true;
           }
            switch($data){
                case 0:
                    if(API::getATM($player, time()) > 2*60){
                        EconomyAPI::getInstance()->addMoney($player, API::getATM($player, time()) / 20);
                        API::setATM($player, time());
                        $player->sendMessage('§2L\'ATM a bien été pris.');
                    }else{
                        $player->sendMessage('§4Vous devez patienter au moins deux minutes après la connexion pour prendre votre ATM.');
                    }
                    break;
            }
        });
        $form->setTitle('§f§l>> §4ATM');
        $form->setContent("Voulez-vous prendre votre ATM ? Nombre total des gains : " . API::getATM($player, time()) / 20 . '$');
        $form->addButton('§2Oui !');
        $form->addButton('§4Non.');
        $form->sendToPlayer($player);
        return $form;
    }
}
