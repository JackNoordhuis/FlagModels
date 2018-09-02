<?php

/**
 * FlagsConfigurationLoader.php – FlagModels
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

namespace jacknoordhuis\flagmodels\utils\config;

use jacknoordhuis\flagmodels\flag\Flag;
use jacknoordhuis\flagmodels\utils\Skin;

class FlagsConfigurationLoader extends ConfigurationLoader {

	public function onLoad(array $data) : void {
		foreach($data["general"]["flags"] as $fData) {
			if(!is_file($this->getPlugin()->getDataFolder() . "geometry" . DIRECTORY_SEPARATOR . $fData["geometry"])) {
				$this->getPlugin()->getLogger()->warning("Unable to load flag '{$fData["name"]}' due to missing geometry file!");
				continue;
			}
			$geometry = file_get_contents($this->getPlugin()->getDataFolder() . "geometry" . DIRECTORY_SEPARATOR . $fData["geometry"]);

			if(!is_file($this->getPlugin()->getDataFolder() . "textures" . DIRECTORY_SEPARATOR . $fData["texture"])) {
				$this->getPlugin()->getLogger()->warning("Unable to load flag '{$fData["name"]}' due to missing texture file!");
				continue;
			}
			$texture = Skin::skinToBinary(imagecreatefrompng($this->getPlugin()->getDataFolder() . "textures" . DIRECTORY_SEPARATOR . $fData["texture"]));

			$this->getPlugin()->getFlagManager()->registerFlag(new Flag(strtolower($fData["name"]), $geometry, $texture));
		}
	}

}