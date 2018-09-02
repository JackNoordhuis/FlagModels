<?php

/**
 * FlagModels.php – FlagModels
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

namespace jacknoordhuis\flagmodels;

use jacknoordhuis\flagmodels\commands\SpawnFlagCommand;
use jacknoordhuis\flagmodels\entity\FlagEntity;
use jacknoordhuis\flagmodels\flag\FlagManager;
use jacknoordhuis\flagmodels\utils\config\FlagsConfigurationLoader;
use pocketmine\command\PluginCommand;
use pocketmine\entity\Entity;
use pocketmine\plugin\PluginBase;

class FlagModels extends PluginBase {

	/** @var FlagManager */
	private $flagManager;

	/** @var FlagsConfigurationLoader */
	private $flagConfigurationLoader;

	const SETTINGS_FILE = "Settings.yml";

	public function onEnable() {
		Entity::registerEntity(FlagEntity::class, true);

		$this->saveResource(self::SETTINGS_FILE);

		if(!is_dir($this->getDataFolder() . "geometry")) {
			mkdir($this->getDataFolder() . "geometry", 0777);
		}

		if(!is_dir($this->getDataFolder() . "textures")) {
			mkdir($this->getDataFolder() . "textures", 0777);
		}

		$this->flagManager = new FlagManager($this);

		$this->flagConfigurationLoader = new FlagsConfigurationLoader($this, $this->getDataFolder() . self::SETTINGS_FILE);

		/** @var PluginCommand $spawnFlagCmd */
		$spawnFlagCmd = $this->getCommand("spawnflag");
		$spawnFlagCmd->setExecutor(new SpawnFlagCommand($this));
	}

	/**
	 * @return FlagManager
	 */
	public function getFlagManager() : FlagManager {
		return $this->flagManager;
	}

}