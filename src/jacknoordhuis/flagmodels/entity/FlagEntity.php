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

		$this->setSkin($flag->skin());
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

}