<?php
// filepath: c:\xampp1\htdocs\ghodwa\config\database.php
class Database {
    private static $connection = null;

    public static function getConnection() { // Ensure this method is static
        if (self::$connection === null) {
            try {
                self::$connection = new PDO("mysql:host=localhost;dbname=clyptor", "root", "");
                self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $exception) {
                die("Connection error: " . $exception->getMessage());
            }
        }
        return self::$connection;
    }
}
?>