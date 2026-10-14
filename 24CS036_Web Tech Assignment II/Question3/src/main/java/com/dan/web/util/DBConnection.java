package com.dan.web.util;

import java.nio.charset.StandardCharsets;
import java.security.MessageDigest;
import java.security.NoSuchAlgorithmException;
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;

/**
 * ==============================================================================
 * Web Technology Assignment II - Question 3: Database Connection Utility
 * Package: com.dan.web.util.DBConnection
 * 
 * Provides centralized JDBC connection factory and password hashing.
 * Consistent with database/schema.sql and Question 2 PHP configuration.
 * ==============================================================================
 */
public class DBConnection {

    // Database Connection Parameters
    private static final String DB_HOST = System.getProperty("db.host", "localhost");
    private static final String DB_PORT = System.getProperty("db.port", "3306");
    private static final String DB_NAME = System.getProperty("db.name", "student_portal_db");
    private static final String DB_USER = System.getProperty("db.user", "root");
    private static final String DB_PASS = System.getProperty("db.pass", "");

    private static final String JDBC_URL = String.format(
            "jdbc:mysql://%s:%s/%s?useSSL=false&allowPublicKeyRetrieval=true&serverTimezone=UTC&characterEncoding=UTF-8",
            DB_HOST, DB_PORT, DB_NAME
    );

    static {
        try {
            // Explicitly load MySQL JDBC driver class
            Class.forName("com.mysql.cj.jdbc.Driver");
        } catch (ClassNotFoundException e) {
            System.err.println("CRITICAL: MySQL JDBC Driver not found in classpath. Ensure mysql-connector-j is included.");
            e.printStackTrace();
        }
    }

    /**
     * Establishes and returns a new MySQL Connection.
     * 
     * @return Connection active JDBC connection
     * @throws SQLException if a database access error occurs
     */
    public static Connection getConnection() throws SQLException {
        return DriverManager.getConnection(JDBC_URL, DB_USER, DB_PASS);
    }

    /**
     * Hashes a plaintext password using SHA-256 to match database/schema.sql.
     * 
     * @param password Plaintext password
     * @return 64-character hexadecimal SHA-256 digest string
     */
    public static String hashPassword(String password) {
        if (password == null) {
            return "";
        }
        try {
            MessageDigest digest = MessageDigest.getInstance("SHA-256");
            byte[] encodedHash = digest.digest(password.getBytes(StandardCharsets.UTF_8));
            StringBuilder hexString = new StringBuilder();
            for (byte b : encodedHash) {
                String hex = Integer.toHexString(0xff & b);
                if (hex.length() == 1) {
                    hexString.append('0');
                }
                hexString.append(hex);
            }
            return hexString.toString();
        } catch (NoSuchAlgorithmException e) {
            throw new RuntimeException("SHA-256 algorithm not supported in this JVM environment.", e);
        }
    }

    /**
     * Safe resource cleanup helper.
     */
    public static void close(AutoCloseable... resources) {
        for (AutoCloseable res : resources) {
            if (res != null) {
                try {
                    res.close();
                } catch (Exception ignored) {
                }
            }
        }
    }

    /**
     * Standalone diagnostic entry point to verify connection and SHA-256 algorithm.
     */
    public static void main(String[] args) {
        System.out.println("=== DBConnection Self-Diagnostic ===");
        System.out.println("JDBC Target URL: " + JDBC_URL);
        System.out.println("Default User: " + DB_USER);

        String testPass = "Admin@123";
        String hash = hashPassword(testPass);
        System.out.println("SHA-256 of '" + testPass + "': " + hash);
        boolean hashMatches = "e86f78a8a3caf0b60d8e74e5942aa6d86dc150cd3c03338aef25b7d2d7e3acc7".equalsIgnoreCase(hash);
        System.out.println("Matches schema.sql hash: " + (hashMatches ? "PASSED" : "FAILED"));

        System.out.println("Attempting database connection...");
        try (Connection conn = getConnection()) {
            if (conn != null && !conn.isClosed()) {
                System.out.println("Database connection SUCCESSFUL to " + DB_NAME);
            }
        } catch (SQLException e) {
            System.out.println("Database connection note: " + e.getMessage());
            System.out.println("(Ensure MySQL is started in XAMPP/WAMP on port 3306 and schema.sql is imported).");
        }
        System.out.println("====================================");
    }
}

