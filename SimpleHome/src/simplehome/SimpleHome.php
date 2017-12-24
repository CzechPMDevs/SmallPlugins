<?php

declare(strict_types=1);

namespace simplehome;

use pocketmine\command\Command;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use simplehome\commands\HomeCommand;

/**
 * Class SimpleHome
 * @package simplehome
 */
class SimpleHome extends PluginBase {

    /**
     * @var array $messages
     */
    public $messages = [];

    /**
     * @var array $homes
     */
    public $homes = [];

    /**
     * @var Command[] $commands;
     */
    private $commands = [];

    public function onEnable() {
        $this->registerCommands();
        $this->loadData();
        $this->getLogger()->info("\n".
            "§c--------------------------------\n".
            "§6§lCzechPMDevs §r§e>>> §bSimpleHome\n".
            "§o§9Simple home plugin.\n".
            "§aAuthors: §7VixikCZ\n".
            "§aVersion: §7".$this->getDescription()->getVersion()."\n".
            "§aStatus: §7Loading...\n".
            "§c--------------------------------");
    }

    public function onDisable() {
        $this->saveData();
    }

    public function getHomeList(Player $player) {
        if(isset($this->homes[$player->getName()])) {
            $list = "";
            foreach ($this->homes[$player->getName()] as $homeName => $homeData) {
                $list = $list.$homeName.",";
            }
            return $list;
        }
        else {
            return "";
        }
    }

    public function setPlayerHome(Player $player, Home $home) {
        $this->homes[$player->getName()][$home->getName()] = [$home->getX(), $home->getY(), $home->getZ(), $home->getLevel()->getName()];
    }

    /**
     * @param Player $player
     * @param string $home
     * @return Home
     */
    public function getPlayerHome(Player $player, string $home):Home {
        if(isset($this->homes[$player->getName()][$home])) {
            return new Home($player, $this->homes[$player->getName()][$home], $home);
        }
        else {
            return false;
        }
    }

    public function registerCommands() {
        $this->commands["home"] = new HomeCommand($this);
        $this->commands["sethome"] = new HomeCommand($this);
        foreach ($this->commands as $command) {
            $this->getServer()->getCommandMap()->register("simplehome", $command);
        }
    }


    public function saveData() {
        foreach ($this->homes as $name => $data) {
            $config = new Config($this->getDataFolder()."players/$name.yml", Config::YAML);
            $config->set("homes", $data);
            $config->save();
        }
    }

    public function loadData() {
        if(!is_dir($this->getDataFolder())) {
            @mkdir($this->getDataFolder());
        }
        if(!is_dir($this->getDataFolder()."players")) {
            @mkdir($this->getDataFolder()."players");
        }
        else {
            foreach (glob($this->getDataFolder()."players/*.yml") as $file) {
                $config = new Config($file, Config::YAML);
                $this->homes[basename($file, ".yml")] = $config->get("homes");
            }
        }
        if(!is_file($this->getDataFolder()."/config.yml")) {
            $this->saveResource("/config.yml");
        }
        else {
            $this->messages = $this->getConfig()->getAll();
        }
    }
}