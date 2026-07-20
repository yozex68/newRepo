<?php
namespace App\Helpers;

use PDO;
use Exception;

class Backup {
    /**
     * Generate complete database structure and data backup as SQL text.
     *
     * @return string
     * @throws Exception
     */
    public static function generateBackup(): string {
        // Instantiate a PDO connection manually
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];
        
        try {
            $db = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (Exception $e) {
            throw new Exception("Backup failed: Cannot connect to database. " . $e->getMessage());
        }

        $sql = "-- SmartHUB Digital Library - SQL Database Backup\n";
        $sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
        $sql .= "-- PHP Version: " . phpversion() . "\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";

        // Get list of tables
        $tablesStmt = $db->query("SHOW TABLES");
        $tables = $tablesStmt->fetchAll(PDO::FETCH_COLUMN);

        foreach ($tables as $table) {
            // 1. Structure
            $createStmt = $db->query("SHOW CREATE TABLE `{$table}`");
            $createRow = $createStmt->fetch();
            $sql .= "-- Table structure for table `{$table}`\n";
            $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
            $sql .= $createRow['Create Table'] . ";\n\n";

            // 2. Data
            $rowsStmt = $db->query("SELECT * FROM `{$table}`");
            $rows = $rowsStmt->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($rows)) {
                $sql .= "-- Dumping data for table `{$table}`\n";
                $columns = array_keys($rows[0]);
                $escapedCols = array_map(fn($col) => "`{$col}`", $columns);
                $sql .= "INSERT INTO `{$table}` (" . implode(', ', $escapedCols) . ") VALUES\n";

                $valuesList = [];
                foreach ($rows as $row) {
                    $rowValues = [];
                    foreach ($row as $val) {
                        if ($val === null) {
                            $rowValues[] = "NULL";
                        } else {
                            $rowValues[] = $db->quote($val);
                        }
                    }
                    $valuesList[] = "  (" . implode(', ', $rowValues) . ")";
                }
                $sql .= implode(",\n", $valuesList) . ";\n\n";
            }
            $sql .= "\n";
        }

        $sql .= "SET FOREIGN_KEY_CHECKS = 1;\n";
        return $sql;
    }
}
