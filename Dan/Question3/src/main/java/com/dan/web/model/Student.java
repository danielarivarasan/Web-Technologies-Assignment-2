package com.dan.web.model;

import java.io.Serializable;
import java.sql.Timestamp;

/**
 * ==============================================================================
 * Web Technology Assignment II - Question 3: Student Entity Model
 * Package: com.dan.web.model.Student
 * 
 * Represents student records matching the MySQL `students` table in schema.sql.
 * ==============================================================================
 */
public class Student implements Serializable {
    private static final long serialVersionUID = 1L;

    private int id;
    private String rollNumber;
    private String fullName;
    private String email;
    private String course;
    private String profileImage;
    private String profileImageType;
    private int profileImageSize;
    private String document;
    private String documentType;
    private int documentSize;
    private Timestamp createdAt;
    private Timestamp updatedAt;

    public Student() {
    }

    public Student(int id, String rollNumber, String fullName, String email, String course,
                   String profileImage, String profileImageType, int profileImageSize,
                   String document, String documentType, int documentSize,
                   Timestamp createdAt, Timestamp updatedAt) {
        this.id = id;
        this.rollNumber = rollNumber;
        this.fullName = fullName;
        this.email = email;
        this.course = course;
        this.profileImage = profileImage;
        this.profileImageType = profileImageType;
        this.profileImageSize = profileImageSize;
        this.document = document;
        this.documentType = documentType;
        this.documentSize = documentSize;
        this.createdAt = createdAt;
        this.updatedAt = updatedAt;
    }

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public String getRollNumber() {
        return rollNumber;
    }

    public void setRollNumber(String rollNumber) {
        this.rollNumber = rollNumber;
    }

    public String getFullName() {
        return fullName;
    }

    public void setFullName(String fullName) {
        this.fullName = fullName;
    }

    public String getEmail() {
        return email;
    }

    public void setEmail(String email) {
        this.email = email;
    }

    public String getCourse() {
        return course;
    }

    public void setCourse(String course) {
        this.course = course;
    }

    public String getProfileImage() {
        return profileImage;
    }

    public void setProfileImage(String profileImage) {
        this.profileImage = profileImage;
    }

    public String getProfileImageType() {
        return profileImageType;
    }

    public void setProfileImageType(String profileImageType) {
        this.profileImageType = profileImageType;
    }

    public int getProfileImageSize() {
        return profileImageSize;
    }

    public void setProfileImageSize(int profileImageSize) {
        this.profileImageSize = profileImageSize;
    }

    public String getDocument() {
        return document;
    }

    public void setDocument(String document) {
        this.document = document;
    }

    public String getDocumentType() {
        return documentType;
    }

    public void setDocumentType(String documentType) {
        this.documentType = documentType;
    }

    public int getDocumentSize() {
        return documentSize;
    }

    public void setDocumentSize(int documentSize) {
        this.documentSize = documentSize;
    }

    public Timestamp getCreatedAt() {
        return createdAt;
    }

    public void setCreatedAt(Timestamp createdAt) {
        this.createdAt = createdAt;
    }

    public Timestamp getUpdatedAt() {
        return updatedAt;
    }

    public void setUpdatedAt(Timestamp updatedAt) {
        this.updatedAt = updatedAt;
    }

    /**
     * Formats bytes to KB with 1 decimal place.
     */
    public String getFormattedDocumentSize() {
        if (documentSize <= 0) return "0 KB";
        return String.format("%.1f KB", documentSize / 1024.0);
    }

    public String getFormattedProfileImageSize() {
        if (profileImageSize <= 0) return "0 KB";
        return String.format("%.1f KB", profileImageSize / 1024.0);
    }

    /**
     * Extracts first letter of the name for avatar placeholder.
     */
    public String getInitials() {
        if (fullName == null || fullName.trim().isEmpty()) return "S";
        return fullName.trim().substring(0, 1).toUpperCase();
    }
}
