<?php

declare(strict_types=1);

namespace simplehome\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use simplehome\Home;
use simplehome\SimpleHome;

/**
 * Class RemovehomeCommand
 * @package simplehome\commands
 */
class RemovehomeCommand extends Command {

    /**
     * @var SimpleHome $plugin
     */
    private $plugin;

    /**
     * SethomeCommand constructor.
     * @param SimpleHome $plugin
     */
    public function __construct(SimpleHome $plugin) {
        parent::__construct("delhome", "Remove home", null, ["rmhome", "removehome", "deletehome"]);
        $this->setPermission("sh.cmd.delhome");
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
            $sender->sendMessage($this->getPlugin()->messages["prefix"]." ".$this->getPlugin()->messages["delhome-usage"]);
            return false;
        }
        $this->getPlugin()->removeHome($sender, Home::fromPosition($sender->asPosition(), $args[0], $sender));
        $sender->sendMessage(str_replace("%1", $args[0],$this->getPlugin()->messages["prefix"]." ".$this->getPlugin()->messages["delhome-message"]));
        return false;
    }

    /**
     * @return SimpleHome $plugin
     */
    public function getPlugin():SimpleHome {
        return $this->plugin;
    }
}
