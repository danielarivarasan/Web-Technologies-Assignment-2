package com.dan.web.dao;

import com.dan.web.model.User;
import com.dan.web.util.DBConnection;

import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;

/**
 * ==============================================================================
 * Web Technology Assignment II - Question 3: User DAO
 * Package: com.dan.web.dao.UserDAO
 * 
 * Handles secure authentication queries using SHA-256 hashes and PreparedStatement.
 * ==============================================================================
 */
public class UserDAO {

    /**
     * Authenticates a user against the `users` table.
     * 
     * @param username Provided username
     * @param plainPassword Plaintext password to hash and compare
     * @return User object on success, or null on invalid credentials
     */
    public User authenticate(String username, String plainPassword) {
        if (username == null || plainPassword == null || username.trim().isEmpty() || plainPassword.isEmpty()) {
            return null;
        }

        String hashedPassword = DBConnection.hashPassword(plainPassword);
        String sql = "SELECT id, username, full_name, role FROM users WHERE username = ? AND password_hash = ? LIMIT 1";

        Connection conn = null;
        PreparedStatement stmt = null;
        ResultSet rs = null;

        try {
            conn = DBConnection.getConnection();
            stmt = conn.prepareStatement(sql);
            stmt.setString(1, username.trim());
            stmt.setString(2, hashedPassword);
            rs = stmt.executeQuery();

            if (rs.next()) {
                User user = new User();
                user.setId(rs.getInt("id"));
                user.setUsername(rs.getString("username"));
                user.setFullName(rs.getString("full_name"));
                user.setRole(rs.getString("role"));
                return user;
            }
        } catch (SQLException e) {
            System.err.println("Authentication query error: " + e.getMessage());
            e.printStackTrace();
        } finally {
            DBConnection.close(rs, stmt, conn);
        }

        return null;
    }
}
