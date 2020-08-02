<?php

namespace tatchan\spinyplugin;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\plugin\PluginBase;
use pocketmine\resourcepacks\ZippedResourcePack;
use pocketmine\Server;
use ReflectionClass;
use ReflectionException;

class Main extends PluginBase
{

	public function onEnable(): void
	{
		$this->saveResource("music.zip");
		var_dump($this->saveResource("music.zip", true));

		$path = $this->getDataFolder() . "music.zip";
		$resourcePackManager = Server::getInstance()->getResourcePackManager();
		$newResourcePack = new ZippedResourcePack($path);
		try {
			$resourcePackManagerReflection = new ReflectionClass(get_class($resourcePackManager));
			//ResourcePackManager::$resourcePacks
			$resourcePacksProperty = $resourcePackManagerReflection->getProperty("resourcePacks");
			$resourcePacksProperty->setAccessible(true);
			$resourcePacksValue = $resourcePacksProperty->getValue($resourcePackManager);
			$resourcePacksValue[] = $newResourcePack;
			$resourcePacksProperty->setValue($resourcePackManager, $resourcePacksValue);
			//ResourcePackManager::$uuidList
			$uuidListProperty = $resourcePackManagerReflection->getProperty("uuidList");
			$uuidListProperty->setAccessible(true);
			$uuidListValue = $uuidListProperty->getValue($resourcePackManager);
			$uuidListValue[strtolower($newResourcePack->getPackId())] = $newResourcePack;
			$uuidListProperty->setValue($resourcePackManager, $uuidListValue);
		} catch (ReflectionException $reflectionException) {
			throw new LogicException("Caught ReflectionException.");
		}

	}

	function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
	{
		switch ($command->getName()) {
			case "spiny":

				$packet = new PlaySoundPacket;
				$packet->soundName = "music.spiny";
				$packet->volume = 1.0;
				$packet->pitch = 1.0;
				$player = $sender;
				$packet->x = $player->getX();
				$packet->y = $player->getY();
				$packet->z = $player->getZ();
				$player->dataPacket($packet);
				return true;
		}
	}
}
