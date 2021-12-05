<?php

declare(strict_types=1);

namespace czechpmdevs\snow;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\network\mcpe\cache\StaticPacketCache;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\network\mcpe\protocol\serializer\NetworkNbtSerializer;
use pocketmine\network\mcpe\protocol\types\CacheableNbt;
use pocketmine\network\mcpe\protocol\types\LevelEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\AssumptionFailedError;
use RuntimeException;
use Throwable;
use Webmozart\PathUtil\Path;
use function file_get_contents;
use function lcg_value;
use const pocketmine\BEDROCK_DATA_PATH;

class Snow extends PluginBase implements Listener {
    public const LOCAL_DEFINITIONS_PATH = "biome_definitions.nbt";

    protected function onEnable(): void {
		$this->getServer()->getPluginManager()->registerEvents($this, $this);

		$compressedBiomeData = @file_get_contents($path = Path::join(BEDROCK_DATA_PATH, self::LOCAL_DEFINITIONS_PATH));
		if(!$compressedBiomeData) {
			throw new RuntimeException("Failed to read a file $path");
		}

		$nbt = (new NetworkNbtSerializer())->read($compressedBiomeData)->mustGetCompoundTag();
		foreach ($nbt->getValue() as $biomeName => $biomeCompound) {
			if(!$biomeCompound instanceof CompoundTag) {
				throw new AssumptionFailedError("Received invalid or corrupted biome data. Try looking for a new plugin version at poggit");
			}

			foreach ($biomeCompound as $index => $value) {
				if($index === "temperature") {
					$value = new FloatTag(-3 * lcg_value());
				}

				$biomeCompound->setTag($index, $value);
			}

			$nbt->setTag($biomeName, $biomeCompound);
		}

		try {
			StaticPacketCache::getInstance()->getBiomeDefs()->definitions = new CacheableNbt($nbt);
		} catch(Throwable) {
			throw new AssumptionFailedError("There were some changes in protocol library independent on protocol change. Try looking for a new plugin version at poggit");
		}
    }

    public function onJoin(PlayerJoinEvent $event): void {
		$event->getPlayer()->getNetworkSession()->sendDataPacket(LevelEventPacket::create(
			eventId: LevelEvent::START_RAIN,
			eventData: 100000, // Around 24 hours of snowing, hope player won't be afking that long time
			position: null
		));
    }
}