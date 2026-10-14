package com.dan.web.model;

import java.io.Serializable;

/**
 * ==============================================================================
 * Web Technology Assignment II - Question 3: User Authentication Model
 * Package: com.dan.web.model.User
 * 
 * Represents authenticated users matching the MySQL `users` table in schema.sql.
 * Stored in the HttpSession upon successful login.
 * ==============================================================================
 */
public class User implements Serializable {
    private static final long serialVersionUID = 1L;

    private int id;
    private String username;
    private String fullName;
    private String role;

    public User() {
    }

    public User(int id, String username, String fullName, String role) {
        this.id = id;
        this.username = username;
        this.fullName = fullName;
        this.role = role;
    }

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public String getUsername() {
        return username;
    }

    public void setUsername(String username) {
        this.username = username;
    }

    public String getFullName() {
        return fullName;
    }

    public void setFullName(String fullName) {
        this.fullName = fullName;
    }

    public String getRole() {
        return role;
    }

    public void setRole(String role) {
        this.role = role;
    }
}
