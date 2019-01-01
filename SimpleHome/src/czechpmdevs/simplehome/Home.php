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

namespace czechpmdevs\simplehome;

use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\Server;
use czechpmdevs\simplehome\event\PlayerHomeTeleportEvent;

/**
 * Class Home
 * @package simplehome
 */
final class Home extends Position {

    /**
     * @var Player $owner
     */
    private $owner;

    /**
     * @var string $name
     */
    private $name;

    /**
     * Home constructor.
     * @param Player $player
     * @param array $data
     * @param string $name
     */
    public function __construct(Player $player, array $data, string $name) {
        if(!$player->getServer()->isLevelLoaded((string)$data[3])) {
            $player->getServer()->loadLevel((string)$data[3]);
        }
        parent::__construct((int)$data[0], (int)$data[1], (int)$data[2], Server::getInstance()->getLevelByName((string)$data[3]));
        $this->owner = $player;
        $this->name = $name;
    }

    /**
     * @api
     *
     * @param Position $position
     * @param $name
     * @param $player
     *
     * @return Home
     */
    public static function fromPosition(Position $position, $name, $player): Home {
        return new Home($player, [(int)$position->getX(), (int)$position->getY(), (int)$position->getZ(), $position->getLevel()->getFolderName()], $name);

    }

    /**
     * @api
     *
     * @return string
     */
    public final function getName():string {
        return $this->name;
    }

    /**
     * @api
     *
     * @param Player $player
     */
    public final function teleport(Player $player) {
        $event = new PlayerHomeTeleportEvent($player, $this);

        $player->getServer()->getPluginManager()->callEvent($event);

        if(!$event->isCancelled()) {
            $player->teleport($this->asPosition());
        }
    }

    /**
     * @api
     *
     * @return Player
     */
    public final function getOwner():Player {
        return $this->owner;
    }
}
