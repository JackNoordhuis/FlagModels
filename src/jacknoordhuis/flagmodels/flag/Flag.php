<?php

/**
 * Flag.php – FlagModels
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

namespace jacknoordhuis\flagmodels\flag;

use pocketmine\entity\Skin;

/**
 * Simple class to keep track of all data associated with a flag.
 */
class Flag {

	/** @var string */
	private $name;

	/** @var string */
	private $geometry;

	/** @var string */
	private $texture;

	/** @var Skin */
	private $skin;

	public function __construct(string $name, string $geometry, string $texture) {
		$this->name = $name;
		$this->geometry = $geometry;
		$this->texture = $texture;

		$this->skin = new Skin("Standard_Custom", $texture, "", "geometry.flag", $geometry);
		$this->skin->debloatGeometryData();
	}

	public function name() : string {
		return $this->name;
	}

	public function geometry() : string {
		return $this->geometry;
	}

	public function texture() : string {
		return $this->texture;
	}

	public function skin() : Skin {
		return clone $this->skin;
	}

}