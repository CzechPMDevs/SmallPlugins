<?php

namespace ServerTransfer;

use pocketmine\network\mcpe\protocol\TransferPacket;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase {

    /** @var EventListener */
    public $listener;

    public function onEnable() {
        $this->listener = new EventListener($this);
        $this->getServer()->getPluginManager()->registerEvents($this->listener, $this);
    }
    
    public function onDisable(){
        $players = Server::getInstance()->getOnlinePlayers();
        if($this->getServer()->isRunning() == false){
        foreach($players as $p){
            $this->transfer($p, $this->getConfig()->get("disable-server-ip"), $this->getConfig()->get("disable-server-port"));
        }
        }
    }

    public function transfer(Player $player, $ip, $port = 19132) {
        $pk = new TransferPacket();
        $pk->address = $ip;
        $pk->port = $port;
        $player->dataPacket($pk);
    }
}
