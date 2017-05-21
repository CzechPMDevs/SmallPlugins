<?php

namespace BlockInfo;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener {

    // players
    public $players = [];

    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this,$this);
    }

    public function onCommand(CommandSender $s, Command $cmd, $label, array $args) {
        if($s instanceof Player && $s->isOp()) {
            if($cmd->getName() == "bi" && $args[0] == "add") {
                $s->sendMessage("§aSucess.");
                array_push($this->players, $s->getName());
            }
            elseif($cmd->getName() == "bi" && $args[0] == "remove") {
                unset($this->players[0]);
            }
        }
        else {
            $s->sendMessage("§cYou does not have permissions to use this command!");
        }
    }

    public function onTouch(PlayerInteractEvent $e) {
        if(in_array($e->getPlayer()->getName(), $this->players)) {
            $e->getPlayer()->sendMessage("§5X: §6{$e->getBlock()->getX()}\n".
                "§5Y: §6{$e->getBlock()->getY()}\n".
                "§5Z: §6{$e->getBlock()->getZ()}\n".
                "§5ID: §6{$e->getBlock()->getId()}\n".
                "§5NAME: §6{$e->getBlock()->getName()}");
        }
    }
}
