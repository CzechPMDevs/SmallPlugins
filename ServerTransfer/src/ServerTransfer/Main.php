<?php

namespace ServerTransfer;

use pocketmine\network\protocol\TransferPacket;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

class ServerTransfer extends PluginBase {

    /** @var EventListener */
    public $listener;

    public function onEnable() {
        $this->listener = new EventListener($this);
        $this->getServer()->getPluginManager()->registerEvents($this->listener, $this);
    }

    public function transfer(Player $player, $ip, $port = 19132) {
        $pk = new TransferPacket();
        $pk->address = $ip;
        $pk->port = $port;
        $player->dataPacket($pk);
    }
}
