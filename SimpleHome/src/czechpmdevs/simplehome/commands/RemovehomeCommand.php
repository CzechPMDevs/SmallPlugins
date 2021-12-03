<?php

/**
 * Copyright (C) 2018-2021  CzechPMDevs
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

use czechpmdevs\simplehome\Home;
use czechpmdevs\simplehome\SimpleHome;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;
use function str_replace;

class RemovehomeCommand extends Command implements PluginOwned {

	private SimpleHome $plugin;

	public function __construct(SimpleHome $plugin) {
		parent::__construct("delhome", "Remove home", null, ["rmhome", "removehome", "deletehome"]);
		$this->setPermission("simplehome.command.delhome");
		$this->plugin = $plugin;
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args) {
		if(!$sender instanceof Player) {
			$sender->sendMessage("This command can be used only in-game!");
			return false;
		}
		if(!isset($args[0])) {
			$sender->sendMessage($this->plugin->getPrefix() . $this->plugin->messages["delhome-usage"]);
			return false;
		}
		if(!in_array($args[0], $this->plugin->getHomeList($sender))) {
			$sender->sendMessage($this->plugin->getPrefix() . str_replace("%1", $args[0], $this->plugin->messages["home-notexists"]));
			return false;
		}
		$this->plugin->removeHome($sender, Home::fromPosition($sender->getPosition(), $args[0], $sender));
		$sender->sendMessage(str_replace("%1", $args[0], $this->plugin->getPrefix() . $this->plugin->messages["delhome-message"]));
		return false;
	}

	public function getOwningPlugin(): Plugin {
		return $this->plugin;
	}
}
