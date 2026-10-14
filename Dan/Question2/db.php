<?php
/**
 * ==============================================================================
 * Web Technology Assignment II - Question 2: Database Connection
 * File: Question2/db.php
 * 
 * Provides a secure, persistent PDO connection to MySQL using prepared statements.
 * Configured for consistency with database/schema.sql and Question 3 Java JDBC.
 * ==============================================================================
 */

// Database Configuration Constants
define('DB_HOST', '127.0.0.1');       // Standard MySQL host
define('DB_PORT', '3306');            // Standard MySQL port
define('DB_NAME', 'student_portal_db'); // Database defined in schema.sql
define('DB_USER', 'root');            // Default local MySQL user (e.g. XAMPP/WAMP)
define('DB_PASS', '');                // Default local password (empty for local dev)
define('DB_CHARSET', 'utf8mb4');

/**
 * Returns a configured PDO instance with exception mode and prepared statement emulation disabled.
 * 
 * @return PDO
 * @throws PDOException
 */
function getDBConnection(): PDO {
    static $pdo = null;

    if ($pdo === null) {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            DB_HOST,
            DB_PORT,
            DB_NAME,
            DB_CHARSET
        );

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw exceptions on SQL errors
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Return associative arrays by default
            PDO::ATTR_EMULATE_PREPARES   => false,                  // Use native prepared statements for security
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Friendly error message for academic development setup
            die(sprintf(
                "<div style='font-family: sans-serif; padding: 20px; background: #fee2e2; border: 1px solid #ef4444; border-radius: 8px; margin: 20px; color: #991b1b;'>"
                . "<h3>Database Connection Error</h3>"
                . "<p>Could not connect to MySQL database <strong>%s</strong> on <strong>%s:%s</strong>.</p>"
                . "<p><strong>Details:</strong> %s</p>"
                . "<p><em>Tip: Ensure MySQL is running in XAMPP/WAMP and that <code>database/schema.sql</code> has been imported.</em></p>"
                . "</div>",
                htmlspecialchars(DB_NAME),
                htmlspecialchars(DB_HOST),
                htmlspecialchars(DB_PORT),
                htmlspecialchars($e->getMessage())
            ));
        }
    }

    return $pdo;
}

// Global connection instance for quick script access
$pdo = getDBConnection();
