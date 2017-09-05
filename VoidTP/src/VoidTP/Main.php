<?php

namespace VoidTP;

use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

/**
 * Class Main
 * @package VoidTP
 */
class Main extends PluginBase implements Listener {

    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    /**
     * @param EntityDamageEvent $event
     */
    public function onFall(EntityDamageEvent $event) {
        $entity = $event->getEntity();
        if($entity instanceof Player) {
            if($entity->getLevel()->getName() == $this->getServer()->getDefaultLevel()->getName()) {
                if($entity->getY() <= 0) {
                    $entity->teleport($this->getServer()->getDefaultLevel()->getSpawnLocation());
                    $event->setCancelled();
                }
            }
        }
    }
}