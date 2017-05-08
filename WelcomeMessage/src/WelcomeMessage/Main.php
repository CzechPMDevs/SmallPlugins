<?php

namespace WelcomeMessage;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener {

    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this,$this);
        if(!file_exists($this->getDataFolder())) {
            @mkdir($this->getDataFolder());
            $this->getConfig()->set("welcome-message", "Welcome @player on my server...{LINE}- plugin WelcomeMessage by CzechPMDevs");
            $this->getConfig()->save();
        }

    }
    public function onJoin(PlayerJoinEvent $event) {
        $player = $event->getPlayer();
        $event->setJoinMessage("");
        $msg = $this->getConfig()->get("welcome-message");
        $msg = str_replace("{LINE}", "\n", $msg);
        $msg = str_replace("@player", $player->getName(), $msg);
        $player->sendMessage($msg);
    }
}
