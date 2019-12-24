<?php

declare(strict_types=1);

namespace vixikhd\snow;

/**
 * Class BiomeDefinitionListPacket
 * @package vixikhd\snow
 */
class BiomeDefinitionListPacket extends \pocketmine\network\mcpe\protocol\BiomeDefinitionListPacket {

    protected function encodePayload() {
        $this->put($this->namedtag ?? Snow::$dataForPacket);
    }
}