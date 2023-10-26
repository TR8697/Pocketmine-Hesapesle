<?php

namespace TR8697\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use TR8697\mysql\MySQLManager;

class HesapesleCommand extends Command {
	
    private $dbManager;

	public function __construct(MySQLManager $dbManager)
	{
		parent::__construct("hesapesle", "Hesayı oluşturur", "/hesapesle");
		$this->setPermission("hesapesle.use");

		$this->dbManager = $dbManager;
	}

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$sender instanceof Player) return true;
        
    
        if ($commandLabel !== "hesapesle") return true;

        $this->accountCommand($sender);
    }

    public function accountCommand(Player $player): void {
        $existingHeseyi = $this->dbManager->getHeseyiFromDatabase($player->getName());

        if ($existingHeseyi === "Onaylandı") {
            $player->sendMessage("Heseyi zaten onaylandı.");
            return;
        }

        $heseyi = $this->generateCode();
        $this->dbManager->saveHeseyiToDatabase($player->getName(), $heseyi);
        $player->sendMessage("§bHesap eşleme kodun: §e" . $heseyi);
		$player->sendMessage("§aBu kodu discord sunucumuzda '§7#hesap-esle' §akanalına yaz.");
    }

    public function generateCode(int $length = 8): string
    {
        $characters = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $code = '';

        for ($i = 0; $i < $length; $i++) {
            if ($i % 3 === 0) {
                $code .= $this->getRandomChar($characters);
            } elseif ($i % 3 === 1) {
                $code .= $this->getRandomChar($numbers);
            } else {
                $code .= strtoupper($this->getRandomChar($characters));
            }
        }

        return $code;
    }

    private function getRandomChar(string $string): string
    {
        $randomIndex = random_int(0, strlen($string) - 1);
        return $string[$randomIndex];
    }
}
