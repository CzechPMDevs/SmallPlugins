<?php

declare(strict_types=1);

namespace simplehome\event;

use pocketmine\event\Cancellable;
use pocketmine\event\plugin\PluginEvent;
use pocketmine\Player;
use simplehome\Home;

/**
 * Class PlayerHomeTeleportEvent
 * @package simplehome\event
 */
class PlayerHomeTeleportEvent extends PluginEvent implements Cancellable {

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
     * @return Player
     */
    public function getPlayer() {
        return $this->owner;
    }

    /**
     * @api
     *
     * @return Home
     */
    public function getHome(): Home {
        return $this->home;
    }
}
