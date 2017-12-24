<?php

declare(strict_types=1);

namespace simplehome\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use simplehome\SimpleHome;

/**
 * Class HomeCommand
 * @package simplehome\commands
 */
class HomeCommand extends Command  {

    /**
     * @var SimpleHome $plugin
     */
    private $plugin;

    /**
     * HomeCommand constructor.
     */
    public function __construct(SimpleHome $plugin) {
        parent::__construct("home", "Teleport to your home");
        $this->setPermission("sh.cmd.home");
        $this->plugin = $plugin;
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     * @return bool
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if(!$sender instanceof Player) {
            $sender->sendMessage("This command can be used only in-game!");
            return false;
        }
        if(empty($args[0])) {
            $sender->sendMessage($this->getPlugin()->messages["prefix"]." ".$this->getPlugin()->messages["home-usage"]);
            return false;
        }
        if(!$this->getPlugin()->getPlayerHome($sender, $args[0])) {
            $sender->sendMessage(str_replace("%1", $args[0],$this->getPlugin()->messages["prefix"]." ".$this->getPlugin()->messages["home-notexists"]));
            return false;
        }
        $this->getPlugin()->getPlayerHome($sender, $args[0])->teleport($sender);
        $sender->sendMessage(str_replace("%1", $args[0],$this->getPlugin()->messages["prefix"]." ".$this->getPlugin()->messages["home-message"]));
        return false;
    }

    /**
     * @return SimpleHome $plugin
     */
    public function getPlugin():SimpleHome {
        return $this->plugin;
    }
}