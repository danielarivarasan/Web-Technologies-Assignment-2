package com.dan.web.dao;

import com.dan.web.model.Student;
import com.dan.web.util.DBConnection;

import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.List;

/**
 * ==============================================================================
 * Web Technology Assignment II - Question 3: Student Data Access Object (DAO)
 * Package: com.dan.web.dao.StudentDAO
 * 
 * Handles database operations for student profile retrieval using PreparedStatements.
 * ==============================================================================
 */
public class StudentDAO {

    /**
     * Retrieves all student records along with uploaded file metadata.
     * 
     * @return List of Student entities
     */
    public List<Student> getAllStudents() {
        List<Student> students = new ArrayList<>();
        String sql = "SELECT id, roll_number, full_name, email, course, " +
                     "profile_image, profile_image_type, profile_image_size, " +
                     "document, document_type, document_size, created_at, updated_at " +
                     "FROM students ORDER BY id DESC";

        Connection conn = null;
        PreparedStatement stmt = null;
        ResultSet rs = null;

        try {
            conn = DBConnection.getConnection();
            stmt = conn.prepareStatement(sql);
            rs = stmt.executeQuery();

            while (rs.next()) {
                Student s = mapResultSetToStudent(rs);
                students.add(s);
            }
        } catch (SQLException e) {
            System.err.println("Error fetching student records: " + e.getMessage());
            e.printStackTrace();
        } finally {
            DBConnection.close(rs, stmt, conn);
        }

        return students;
    }

    /**
     * Retrieves a single student record by their primary key ID.
     * 
     * @param id The student ID
     * @return Student object or null if not found
     */
    public Student getStudentById(int id) {
        String sql = "SELECT id, roll_number, full_name, email, course, " +
                     "profile_image, profile_image_type, profile_image_size, " +
                     "document, document_type, document_size, created_at, updated_at " +
                     "FROM students WHERE id = ? LIMIT 1";

        Connection conn = null;
        PreparedStatement stmt = null;
        ResultSet rs = null;

        try {
            conn = DBConnection.getConnection();
            stmt = conn.prepareStatement(sql);
            stmt.setInt(1, id);
            rs = stmt.executeQuery();

            if (rs.next()) {
                return mapResultSetToStudent(rs);
            }
        } catch (SQLException e) {
            System.err.println("Error fetching student by ID " + id + ": " + e.getMessage());
            e.printStackTrace();
        } finally {
            DBConnection.close(rs, stmt, conn);
        }

        return null;
    }

    /**
     * Helper mapper to convert a ResultSet row into a Student model.
     */
    private Student mapResultSetToStudent(ResultSet rs) throws SQLException {
        Student s = new Student();
        s.setId(rs.getInt("id"));
        s.setRollNumber(rs.getString("roll_number"));
        s.setFullName(rs.getString("full_name"));
        s.setEmail(rs.getString("email"));
        s.setCourse(rs.getString("course"));
        s.setProfileImage(rs.getString("profile_image"));
        s.setProfileImageType(rs.getString("profile_image_type"));
        s.setProfileImageSize(rs.getInt("profile_image_size"));
        s.setDocument(rs.getString("document"));
        s.setDocumentType(rs.getString("document_type"));
        s.setDocumentSize(rs.getInt("document_size"));
        s.setCreatedAt(rs.getTimestamp("created_at"));
        s.setUpdatedAt(rs.getTimestamp("updated_at"));
        return s;
    }
}
