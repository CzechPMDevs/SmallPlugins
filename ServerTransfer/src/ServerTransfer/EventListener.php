<?php

namespace ServerTransfer;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCommandPreprocessEvent;

class EventListener implements Listener {

    /** @var Main */
    public $plugin;

    public function __construct($plugin) {
        $this->plugin = $plugin;
    }

    public function onCommand(PlayerCommandPreprocessEvent $event) {
        $player = $event->getPlayer();
        $cmd = explode(" ", strtolower($event->getMessage()));
        if($cmd[0] == "/transfer" || $cmd[0] == "/transferserver") {

            if(isset($cmd[1]) && isset($cmd[2])) {
                if($player->hasPermission("st.transfer")) {
                    $this->plugin->transfer($player,$cmd[1], $cmd[2]);
                    $this->plugin->getServer()->broadcastMessage(str_replace(":19132","","[Transfer] Player {$player->getName()} is transfered to {$cmd[1]}:{$cmd[2]}"));
                }
            }
            elseif (isset($cmd[1]) && isset($cmd[2]) && isset($cmd[3])) {
                if($player->hasPermission("st.transfer.other")) {
                    $pl = $this->plugin->getServer()->getPlayer($cmd[3]);
                    if($pl->isOnline()) {
                        $this->plugin->transfer($pl, $cmd[1], $cmd[2]);
                        $this->plugin->getServer()->broadcastMessage(str_replace(":19132","","[Transfer] Player {$pl->getName()} is transfered to {$cmd[1]}:{$cmd[2]}"));
                    }
                }
                else {
                    $player->sendMessage("[Transfer] use /transfer <ip> <port> (player)");
                }
            }
            else {
                $player->sendMessage("[Transfer] use /transfer <ip> <port> (player)");
            }
            $event->setCancelled(true);
        }
    }
}
