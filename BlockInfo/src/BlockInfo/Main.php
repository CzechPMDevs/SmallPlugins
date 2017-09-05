<?php

namespace BlockInfo;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener {
    
    public $player;

    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this,$this);
    }

    public function onCommand(CommandSender $s, Command $cmd, string $label, array $args):bool {
        if($s instanceof Player && $s->isOp()) {
            if($cmd->getName() == "bi" && $args[0] == "add") {
                $s->sendMessage("§aSuccessfully added to list.");
                $this->player[$s->getName()] = 1;
            }
        }
        else {
            $s->sendMessage("§cYou does not have permissions to use this command!");
        }
    }

    public function onTouch(PlayerInteractEvent $e) {
        if(isset($this->player[$e->getPlayer()->getName()])) {
            $e->getPlayer()->sendMessage("§5X: §6{$e->getBlock()->getX()}\n".
                "§5Y: §6{$e->getBlock()->getY()}\n".
                "§5Z: §6{$e->getBlock()->getZ()}\n".
                "§5ID: §6{$e->getBlock()->getId()}\n".
                "§5NAME: §6{$e->getBlock()->getName()}");
        }
    }
}
