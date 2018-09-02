<?php

/**
 * FlagManager.php – FlagModels
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

use jacknoordhuis\flagmodels\FlagModels;

/**
 * Simple class to keep track of all the flag objects
 */
class FlagManager {

	/** @var FlagModels */
	private $plugin;

	/** @var Flag[] */
	private $flags = [];

	public function __construct(FlagModels $plugin) {
		$this->plugin = $plugin;
	}

	/**
	 * @return FlagModels
	 */
	public function getPlugin() : FlagModels {
		return $this->plugin;
	}

	/**
	 * Register a flag to the manager
	 *
	 * @param Flag $flag
	 */
	public function registerFlag(Flag $flag) : void {
		if(isset($this->flags[$flag->name()])) {
			throw new \RuntimeException("Tried to register flag with '{$flag->name()}' name when a flag with the same name already exists!");
		}

		$this->flags[$flag->name()] = $flag;
	}

	/**
	 * Get a flag
	 *
	 * @param string $name
	 *
	 * @return Flag|null
	 */
	public function flag(string $name) : ?Flag {
		return $this->flags[$name] ?? null;
	}

}