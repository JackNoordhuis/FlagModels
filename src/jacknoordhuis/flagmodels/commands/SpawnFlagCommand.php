<?php

/**
 * SpawnFlagCommand.php – FlagModels
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

namespace jacknoordhuis\flagmodels\commands;

use jacknoordhuis\flagmodels\entity\FlagEntity;
use jacknoordhuis\flagmodels\FlagModels;
use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\entity\Entity;
use pocketmine\nbt\tag\ByteArrayTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class SpawnFlagCommand implements CommandExecutor {

	/** @var FlagModels */
	private $plugin;

	public function __construct(FlagModels $plugin) {
		$this->plugin = $plugin;
	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
		if(!($sender instanceof Player)) {
			$sender->sendMessage(TextFormat::RED . "Please run this command in-game!");
			return true;
		}

		if(!($sender->hasPermission("flagmodels.command.spawn"))) {
			$sender->sendMessage(TextFormat::RED . "You do not have permission to run this command!");
			return true;
		}

		if(!count($args) >= 1) {
			$sender->sendMessage("Usage: /spawnflag <name>");
			return true;
		}

		if(($flag = $this->plugin->getFlagManager()->flag(strtolower($args[0]))) === null) {
			$sender->sendMessage(TextFormat::GOLD . "Could not find flag with name '{$args[0]}'");
			return true;
		}

		$skin = $flag->skin();
		$nbt = Entity::createBaseNBT($sender->asVector3());
		$nbt->setTag(new CompoundTag("Skin", [
			new StringTag("Name", $skin->getSkinId()),
			new ByteArrayTag("Data", $skin->getSkinData()),
			new ByteArrayTag("CapeData", $skin->getCapeData()),
			new StringTag("GeometryName", $skin->getGeometryName()),
			new ByteArrayTag("GeometryData", $skin->getGeometryData())
		]));
		$nbt->setString("FlagName", $flag->name());

		$flagEntity = Entity::createEntity("FlagEntity", $sender->getLevel(), $nbt);

		if(!$flagEntity instanceof FlagEntity) {
			$sender->sendMessage(TextFormat::GOLD . "Could not spawn flag entity!");
			return true;
		}

		$flagEntity->spawnToAll();

		$sender->sendMessage(TextFormat::GREEN . "Spawned a flag entity at " . $sender->asPosition()->__toString());

		return true;
	}

}