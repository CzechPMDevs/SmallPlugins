<?php

declare(strict_types=1);

namespace simplehome;

use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\Server;
use simplehome\event\PlayerHomeTeleportEvent;

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
        parent::__construct(intval($data[0]), intval($data[1]), intval($data[2]), Server::getInstance()->getLevelByName(strval($data[3])));
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
    public static function fromPosition(Position $position, $name, $player):Home {
        return new Home($player, [intval($position->getX()), intval($position->getY()), intval($position->getZ()), $position->getLevel()->getName()], $name);
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
