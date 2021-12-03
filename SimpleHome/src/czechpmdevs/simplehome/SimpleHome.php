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

namespace czechpmdevs\simplehome;

use czechpmdevs\simplehome\commands\HomeCommand;
use czechpmdevs\simplehome\commands\RemovehomeCommand;
use czechpmdevs\simplehome\commands\SethomeCommand;
use pocketmine\command\Command;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\AssumptionFailedError;
use pocketmine\utils\Config;
use pocketmine\utils\Utils;
use function array_map;
use function basename;
use function count;
use function glob;
use function implode;
use function is_array;
use function is_dir;
use function is_file;
use function is_int;
use function is_string;
use function mkdir;
use function str_replace;
use function yaml_parse_file;

class SimpleHome extends PluginBase {

	private static SimpleHome $instance;

	/** @var array<string, string> */
	public array $messages = [];
	/** @phpstan-var array<string, array<string, array{0: int, 1: int, 2: int, 3: string}>> $homes */
	public array $homes = [];
	/** @var Command[] $commands */
	private array $commands = [];

	public function onEnable(): void {
		self::$instance = $this;
		$this->registerCommands();
		$this->loadData();
	}

	public function onDisable(): void {
		$this->saveData();
	}

	/**
	 * @return string[]
	 */
	public function getHomeList(Player $player): array {
		$list = [];

		if(!isset($this->homes[$player->getName()])) {
			$this->homes[$player->getName()] = [];
		}

		foreach($this->homes[$player->getName()] as $homeName => $homeData) {
			$list[] = $homeName;
		}

		return $list;
	}

	public function getDisplayHomeList(Player $player): string {
		$list = $this->getHomeList($player);

		if(count($list) == 0) {
			return $this->messages["no-home"];
		}

		return str_replace(
			["%1", "%2"],
			[(string)count($list), implode(", ", $list)],
			$this->messages["home-list"]
		);
	}

	public function removeHome(Player $player, Home $home): void {
		unset($this->homes[$player->getName()][$home->getName()]);
	}

	public function setPlayerHome(Player $player, Home $home): void {
		if($this->messages["limit"] != -1 && !$player->hasPermission("simplehome.limit")) {
			if(count($this->getHomeList($player)) > $this->messages["limit"]) {
				$player->sendMessage(str_replace("%1", $home->getName(), $this->messages["sethome-max"]));
				return;
			}
		}
		$this->homes[$player->getName()][$home->getName()] = [$home->getX(), $home->getY(), $home->getZ(), $home->getWorld()->getFolderName()];
	}

	public function getPlayerHome(Player $player, string $home): ?Home {
		if(isset($this->homes[$player->getName()][$home])) {
			return new Home($player, $this->homes[$player->getName()][$home], $home);
		} else {
			return null;
		}
	}

	private function registerCommands(): void {
		$this->commands["delhome"] = new RemovehomeCommand($this);
		$this->commands["home"] = new HomeCommand($this);
		$this->commands["sethome"] = new SethomeCommand($this);
		foreach($this->commands as $command) {
			$this->getServer()->getCommandMap()->register("simplehome", $command);
		}
	}


	private function saveData(): void {
		foreach($this->homes as $name => $data) {
			$config = new Config($this->getDataFolder() . "players/$name.yml", Config::YAML);
			$config->set("homes", $data);
			$config->save();
		}
	}

	private function loadData(): void {
		if(!is_dir($this->getDataFolder())) {
			@mkdir($this->getDataFolder());
		}
		if(!is_dir($this->getDataFolder() . "players")) {
			@mkdir($this->getDataFolder() . "players");
		} else {
			$files = glob($this->getDataFolder() . "players/*.yml");
			if(!$files) {
				$files = [];
			}
			foreach($files as $file) {
				$config = yaml_parse_file($file);
				if(!is_array($config)) {
					continue;
				}

				$homes = $config["homes"] ?? [];
				$this->homes[basename($file, ".yml")] = array_map([$this, 'loadHome'], is_array($homes) ? $homes : []);
			}
		}
		if(!is_file($this->getDataFolder() . "/config.yml")) {
			$this->saveResource("/config.yml");
		}

		$messages = yaml_parse_file($this->getDataFolder() . "/config.yml");
		if(!is_array($messages)) {
			throw new AssumptionFailedError("Invalid or corrupted file with messages");
		}

		foreach(Utils::stringifyKeys($messages) as $key => $value) {
			$this->messages[$key] = $value;
		}
	}

	/**
	 * @phpstan-return array{0: int, 1: int, 2: int, 3: string}
	 */
	private function loadHome(mixed $configData): array {
		if(
			!is_array($configData) ||
			!isset($configData[0]) || !isset($configData[1]) || !isset($configData[2]) || !isset($configData[3]) ||
			!is_int($configData[0]) || !is_int($configData[1]) || !is_int($configData[2]) ||
			!is_string($configData[3])
		) {
			throw new AssumptionFailedError("Invalid or corrupted data received");
		}

		return $configData;
	}

	public function getPrefix(): string {
		return $this->messages["prefix"] . " ";
	}

	public static function getInstance(): SimpleHome {
		return self::$instance;
	}
}