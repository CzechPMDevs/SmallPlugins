<?php

namespace UnlimitedSlots;

use pocketmine\event\Listener;
use pocketmine\event\server\QueryRegenerateEvent;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener {

    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this,$this);
    }

    public function onQuerry(QueryRegenerateEvent $event) {
        $event->setMaxPlayerCount(intval(count($this->getServer()->getOnlinePlayers())+1));
    }
}
