<?php

/**
 * Copyright (C) 2018-2019  CzechPMDevs
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

declare(strict_types=1);

namespace czechpmdevs\simplehome\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use czechpmdevs\simplehome\Home;
use czechpmdevs\simplehome\SimpleHome;

/**
 * Class RemovehomeCommand
 * @package simplehome\commands
 */
class RemovehomeCommand extends Command implements PluginIdentifiableCommand {

    /** @var SimpleHome $plugin */
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
        if(!isset($args[0])) {
            $sender->sendMessage($this->getPlugin()->getPrefix().$this->getPlugin()->messages["delhome-usage"]);
            return false;
        }
        if(!in_array($args[0], $this->getPlugin()->getHomeList($sender))) {
            $sender->sendMessage($this->getPlugin()->getPrefix().str_replace("%1", $args[0], $this->getPlugin()->messages["home-notexists"]));
            return false;
        }
        $this->getPlugin()->removeHome($sender, Home::fromPosition($sender->asPosition(), $args[0], $sender));
        $sender->sendMessage(str_replace("%1", $args[0],$this->getPlugin()->getPrefix().$this->getPlugin()->messages["delhome-message"]));
        return false;
    }

    /**
     * @return SimpleHome|Plugin $plugin
     */
    public function getPlugin():Plugin {
        return $this->plugin;
    }
}
