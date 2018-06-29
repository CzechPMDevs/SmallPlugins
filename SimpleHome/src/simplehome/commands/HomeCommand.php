<?php

declare(strict_types=1);

namespace simplehome\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use simplehome\SimpleHome;

/**
 * Class HomeCommand
 * @package simplehome\commands
 */
class HomeCommand extends Command implements PluginIdentifiableCommand {

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
        if(!isset($args[0])) {
            $sender->sendMessage($this->getPlugin()->getPrefix().$this->getPlugin()->getDisplayHomeList($sender));
            return false;
        }
        if(!$this->getPlugin()->getPlayerHome($sender, $args[0])) {
            $sender->sendMessage($this->getPlugin()->getPrefix().str_replace("%1", $args[0],$this->getPlugin()->messages["home-notexists"]));
            return false;
        }
        $this->getPlugin()->getPlayerHome($sender, $args[0])->teleport($sender);
        $sender->sendMessage($this->getPlugin()->getPrefix().str_replace("%1", $args[0],$this->getPlugin()->messages["home-message"]));
        return false;
    }

    /**
     * @return SimpleHome|Plugin $plugin
     */
    public function getPlugin():Plugin {
        return $this->plugin;
    }
}