<?php

namespace TR8697;

use pocketmine\plugin\PluginBase;
use TR8697\Command\HesapesleCommand;
use TR8697\mysql\MySQLManager;

class Main extends PluginBase {

	public function onEnable(): void
	{
		$dbManager = new MySQLManager();
		$hesapesleCommand = new HesapesleCommand($dbManager);

		$this->getServer()->getCommandMap()->register("hesapesle", $hesapesleCommand);
	}
}
