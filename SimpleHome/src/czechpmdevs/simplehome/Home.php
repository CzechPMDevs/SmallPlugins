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

use czechpmdevs\simplehome\event\PlayerHomeTeleportEvent;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\Position;

final class Home extends Position {

	private Player $owner;
	private string $name;

	/**
	 * @phpstan-param array{0: int, 1: int, 2: int, 3: string} $data
	 */
	public function __construct(Player $player, array $data, string $name) {
		if(!$player->getServer()->getWorldManager()->isWorldLoaded((string)$data[3])) {
			$player->getServer()->getWorldManager()->loadWorld((string)$data[3]);
		}
		parent::__construct((int)$data[0], (int)$data[1], (int)$data[2], Server::getInstance()->getWorldManager()->getWorldByName((string)$data[3]));
		$this->owner = $player;
		$this->name = $name;
	}

	public static function fromPosition(Position $position, string $name, Player $player): Home {
		return new Home($player, [(int)$position->getX(), (int)$position->getY(), (int)$position->getZ(), $position->getWorld()->getFolderName()], $name);

	}

	public final function getName(): string {
		return $this->name;
	}

	public final function teleport(Player $player): void {
		$event = new PlayerHomeTeleportEvent($player, $this);

		$event->call();

		if(!$event->isCancelled()) {
			$player->teleport($this->asPosition());
		}
	}

	public final function getOwner(): Player {
		return $this->owner;
	}
}
