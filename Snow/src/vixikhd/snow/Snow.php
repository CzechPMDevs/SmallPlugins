<?php

declare(strict_types=1);

namespace vixikhd\snow;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\nbt\NetworkLittleEndianNBTStream;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\Task;

class Snow extends PluginBase implements Listener {

    public const MIN_WINTER_TEMPERATURE = -5;
    public const MAX_WINTER_TEMPERATURE = -4;

    /** @var string $dataForPacket */
    public static $dataForPacket;

    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);

        $compound = (new NetworkLittleEndianNBTStream())->read(file_get_contents(\pocketmine\RESOURCE_PATH . '/vanilla/biome_definitions.nbt'));
        $winterData = new CompoundTag();

        foreach ($compound->getValue() as $biomeName => $biomeCompound) {
            $biomeData = new CompoundTag($biomeName);
            foreach ($biomeCompound as $index => $value) {
                if($index == "temperature") {
                    $value = new FloatTag("temperature", (float)(rand(self::MIN_WINTER_TEMPERATURE, self::MAX_WINTER_TEMPERATURE) / 10));
                }

                $biomeData->offsetSet($index, $value);
            }

            $winterData->offsetSet($biomeName, $biomeData);
        }

        self::$dataForPacket = (new NetworkLittleEndianNBTStream())->write($winterData);
    }

    /**
     * @param PlayerJoinEvent $event
     */
    public function onJoin(PlayerJoinEvent $event) {
        $this->getScheduler()->scheduleDelayedTask(new class($event->getPlayer()) extends Task {

            /** @var Player $player */
            public $player;

            /**
             *  constructor.
             * @param Player $player
             */
            public function __construct(Player $player) {
                $this->player = $player;
            }

            /**
             * @param int $currentTick
             */
            public function onRun(int $currentTick) {
                $pk = new LevelEventPacket();
                $pk->evid = LevelEventPacket::EVENT_START_RAIN;
                $pk->data = 100000;

                $this->player->dataPacket($pk);
            }
        }, 20);
    }

    /**
     * @param DataPacketSendEvent $event
     */
    public function onSend(DataPacketSendEvent $event) {
        $packet = $event->getPacket();
        if(!($packet instanceof BiomeDefinitionListPacket) && ($packet instanceof \pocketmine\network\mcpe\protocol\BiomeDefinitionListPacket)) {
            $event->setCancelled();
            $event->getPlayer()->dataPacket(new BiomeDefinitionListPacket());
        }
    }
}