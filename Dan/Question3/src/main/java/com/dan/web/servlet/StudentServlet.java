package com.dan.web.servlet;

import com.dan.web.dao.StudentDAO;
import com.dan.web.model.Student;
import jakarta.servlet.ServletException;
import jakarta.servlet.annotation.WebServlet;
import jakarta.servlet.http.HttpServlet;
import jakarta.servlet.http.HttpServletRequest;
import jakarta.servlet.http.HttpServletResponse;

import java.io.IOException;
import java.util.List;

/**
 * ==============================================================================
 * Web Technology Assignment II - Question 3: Student Portal Servlet
 * Package: com.dan.web.servlet.StudentServlet
 * 
 * Fetches dynamic student profiles and file metadata from MySQL and forwards to JSP.
 * ==============================================================================
 */
@WebServlet(name = "StudentServlet", urlPatterns = {"/students"})
public class StudentServlet extends HttpServlet {
    private static final long serialVersionUID = 1L;
    private StudentDAO studentDAO;

    @Override
    public void init() throws ServletException {
        super.init();
        this.studentDAO = new StudentDAO();
    }

    @Override
    protected void doGet(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {
        // Fetch all student records with file metadata from MySQL
        List<Student> students = studentDAO.getAllStudents();

        // Attach student list to request scope for JSP rendering
        request.setAttribute("students", students);
        request.setAttribute("totalStudents", students.size());

        // Forward to the presentation view (JSP)
        request.getRequestDispatcher("/students.jsp").forward(request, response);
    }
}
