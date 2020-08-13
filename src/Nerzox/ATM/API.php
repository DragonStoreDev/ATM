<?php

namespace Nerzox\ATM;

use pocketmine\Player;
use function strtolower;

class API{

    protected static $bdd;

    protected static function getDatabase(){
        self::$bdd = new \SQLite3(Loader::getInstance()->getDataFolder() . 'Database/ATM.db');
        return self::$bdd;
    }

    public static function init(){
        $db = self::getDatabase();

        $db->query('CREATE TABLE IF NOT EXISTS atm(playername TEXT, timelogin TIMESTAMP)');
        $db->close();
    }
    public static function setATM(Player $player, $timestamp){
        $db = self::getDatabase();
        $name = strtolower($player->getName());

        $db->query("UPDATE atm SET timelogin = '$timestamp' WHERE playername = '$name'");
        $db->close();
    }
    public static function isRegisted(Player $player){
        $db = self::getDatabase();
        $name = strtolower($player->getName());

        $res = $db->query("SELECT * FROM atm WHERE playername = '$name'");
        $arr = $res->fetchArray();
        $db->close();

        if(empty($arr)) {
            return false;
            }else {
            return true;
        }
        return false;
    }
    public static function register(Player $player, $timestamp){
        $db = self::getDatabase();
        $name = strtolower($player->getName());

        $db->query("INSERT INTO atm(playername, timelogin) VALUES ('$name', '$timestamp')");
        $db->close();
    }
    public static function getATM(Player $player, $timestamp){
        $db = self::getDatabase();
        $name = strtolower($player->getName());

        $res = $db->query("SELECT * FROM atm WHERE playername = '$name'");
        $arr = $res->fetchArray();
        $db->close();

        return $timestamp - $arr['timelogin'];
    }
}
