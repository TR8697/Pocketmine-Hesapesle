<?php

namespace TR8697\mysql;

use mysqli;

class MySQLManager {
    private $db;

    public function __construct()
    {
        $this->db = new mysqli("127.0.0.1", "TR8697", "", "hesapesle");
        if ($this->db->connect_error) {
            die("Database connection failed: " . $this->db->connect_error);
        }
    }

    public function saveHeseyiToDatabase(string $playerName, string $heseyi): void
    {
        $stmt = $this->db->prepare("INSERT INTO heseyi_data (player_name, heseyi) VALUES (?, ?) ON DUPLICATE KEY UPDATE heseyi = ?");
        $stmt->bind_param("sss", $playerName, $heseyi, $heseyi);
        $stmt->execute();
        $stmt->close();
    }

    public function getHeseyiFromDatabase(string $playerName): ?string
    {
        $stmt = $this->db->prepare("SELECT heseyi FROM heseyi_data WHERE player_name = ?");
        $stmt->bind_param("s", $playerName);
        $stmt->execute();
        $stmt->bind_result($heseyi);
        $stmt->fetch();
        $stmt->close();

        return $heseyi ?: null;
    }
}
