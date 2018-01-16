<?php

declare(strict_types=1);

namespace bank;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\Task;
use onebone\economyapi\EconomyAPI;

/**
 * Class BankPlugin
 * @package bank
 */
class BankPlugin extends PluginBase implements Listener {

    /** @var BankPlugin $instance */
    public static $instance;

    /** @var Player[] $players */
    public $players = [];

    /** @var Task $refreshTask */
    public $refreshTask;

    public function onEnable() {
        if (!class_exists(EconomyAPI::class)) {
            $this->getLogger()->critical("EconomyAPI plugin does not found!");
            $this->getServer()->getPluginManager()->disablePlugin($this);
            return;
        }
        self::$instance = $this;
        $this->refreshTask = new class extends Task {
            public function onRun(int $currentTick){
                BankPlugin::$instance->check();
            }
        };
        $this->getServer()->getScheduler()->scheduleRepeatingTask($this->refreshTask, 20*10);
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    /**
     * @param PlayerJoinEvent $event
     */
    public function onJoin(PlayerJoinEvent $event) {
        $this->players[$event->getPlayer()->getName()] = $event->getPlayer();
    }

    public function check() {
        foreach ($this->players as $name => $player) {
            if (!$player instanceof Player || !$player->isOnline()) {
                unset($this->players[$name]);
                return;
            }
            $this->addMoney($player, $this->getMoney($player) / 100);
        }
    }

    /**
     * @param Player $player
     * @param int $money
     */
    private function addMoney(Player $player, int $money) {
        return EconomyAPI::getInstance()->addMoney($player, $money);
    }

    /**
     * @param Player $player
     */
    private function getMoney(Player $player) {
        return EconomyAPI::getInstance()->myMoney($player);
    }

}