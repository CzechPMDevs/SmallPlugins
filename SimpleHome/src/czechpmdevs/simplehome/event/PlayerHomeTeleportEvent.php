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

namespace czechpmdevs\simplehome\event;

use pocketmine\event\Cancellable;
use pocketmine\event\plugin\PluginEvent;
use pocketmine\Player;
use czechpmdevs\simplehome\Home;

/**
 * Class PlayerHomeTeleportEvent
 * @package simplehome\event
 */
class PlayerHomeTeleportEvent extends PluginEvent implements Cancellable {

    /** @var null $handlerList */
    public static $handlerList = \null;

    /** @var Player $owner */
    protected $owner;

    /** @var Home $home */
    protected $home;

    /**
     * PlayerHomeTeleportEvent constructor.
     * @param Player $owner
     * @param Home $home
     */
    public function __construct(Player $owner, Home $home) {
        $this->owner = $owner;
        $this->home = $home;
    }

    /**
     * @api
     *
     * @return Player $player
     */
    public function getPlayer() {
        return $this->owner;
    }

    /**
     * @api
     *
     * @return Home $home
     */
    public function getHome(): Home {
        return $this->home;
    }
}
