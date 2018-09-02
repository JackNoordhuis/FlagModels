<?php

/**
 * FlagEntity.php – FlagModels
 *
 * Copyright (C) 2018 Jack Noordhuis
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author Jack
 *
 */

declare(strict_types=1);

namespace jacknoordhuis\flagmodels\entity;

use jacknoordhuis\flagmodels\flag\Flag;
use jacknoordhuis\flagmodels\FlagModels;
use pocketmine\entity\Human;
use pocketmine\network\mcpe\protocol\AddPlayerPacket;
use pocketmine\network\mcpe\protocol\PlayerListPacket;
use pocketmine\network\mcpe\protocol\types\PlayerListEntry;
use pocketmine\Player;
use pocketmine\Server;

class FlagEntity extends Human {

	/** @var Flag */
	private $flag;

	/**
	 * @return Flag
	 */
	public function getFlag() : Flag {
		return $this->flag;
	}

	/**
	 * @param Flag $flag
	 */
	public function setFlag(Flag $flag) : void {
		$this->flag = $flag;

		$this->setSkin($flag->getSkin());
	}

	public function initEntity(): void {
		parent::initEntity();

		if($this->namedtag->hasTag("FlagName")) {
			/** @var FlagModels $plugin */
			$plugin = Server::getInstance()->getPluginManager()->getPlugin("FlagModels");
			$flag = $plugin->getFlagManager()->flag($this->namedtag->getString("FlagName"));
			if($flag !== null) {
				$this->setFlag($flag);
			} else {
				$this->kill();
			}
		} else {
			$this->kill();
		}
	}

	public function saveNBT(): void {
		parent::saveNBT();

		$this->namedtag->setString("FlagName", $this->flag->name());
	}

	protected function sendSpawnPacket(Player $player) : void {

		if(!($this instanceof Player)) {
			/* we don't use Server->updatePlayerListData() because that uses batches, which could cause race conditions in async compression mode */
			$pk = new PlayerListPacket();
			$pk->type = PlayerListPacket::TYPE_ADD;
			$pk->entries = [PlayerListEntry::createAdditionEntry($this->uuid, $this->id, $this->getName(), $this->getName(), 0, $this->skin)];
			$player->dataPacket($pk);
		}

		$pk = new AddPlayerPacket();
		$pk->uuid = $this->getUniqueId();
		$pk->username = $this->getName();
		$pk->entityRuntimeId = $this->getId();
		$pk->position = $this->asVector3();
		$pk->motion = $this->getMotion();
		$pk->yaw = $this->yaw;
		$pk->pitch = $this->pitch;
		$pk->item = $this->getInventory()->getItemInHand();
		$pk->metadata = $this->propertyManager->getAll();
		$player->dataPacket($pk);

		//TODO: Hack for MCPE 1.2.13: DATA_NAMETAG is useless in AddPlayerPacket, so it has to be sent separately
		$this->sendData($player, [self::DATA_NAMETAG => [self::DATA_TYPE_STRING, $this->getNameTag()]]);

		$this->armorInventory->sendContents($player);

		$pk = new PlayerListPacket();
		$pk->type = PlayerListPacket::TYPE_REMOVE;
		$pk->entries = [PlayerListEntry::createRemovalEntry($this->uuid)];
		$player->dataPacket($pk);
	}

}