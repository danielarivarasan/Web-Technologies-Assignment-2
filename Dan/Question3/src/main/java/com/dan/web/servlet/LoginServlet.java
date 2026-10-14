package com.dan.web.servlet;

import com.dan.web.dao.UserDAO;
import com.dan.web.model.User;
import jakarta.servlet.ServletException;
import jakarta.servlet.annotation.WebServlet;
import jakarta.servlet.http.HttpServlet;
import jakarta.servlet.http.HttpServletRequest;
import jakarta.servlet.http.HttpServletResponse;
import jakarta.servlet.http.HttpSession;

import java.io.IOException;

/**
 * ==============================================================================
 * Web Technology Assignment II - Question 3: Login Authentication Servlet
 * Package: com.dan.web.servlet.LoginServlet
 * 
 * Verifies user credentials against MySQL and manages secure user HTTP sessions.
 * ==============================================================================
 */
@WebServlet(name = "LoginServlet", urlPatterns = {"/login"})
public class LoginServlet extends HttpServlet {
    private static final long serialVersionUID = 1L;
    private UserDAO userDAO;

    @Override
    public void init() throws ServletException {
        super.init();
        this.userDAO = new UserDAO();
    }

    @Override
    protected void doGet(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {
        // If user is already authenticated, redirect directly to the student portal
        HttpSession session = request.getSession(false);
        if (session != null && session.getAttribute("user") != null) {
            response.sendRedirect(request.getContextPath() + "/students");
            return;
        }

        // Otherwise display login view
        request.getRequestDispatcher("/login.jsp").forward(request, response);
    }

    @Override
    protected void doPost(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {
        String username = request.getParameter("username");
        String password = request.getParameter("password");

        // 1. Validate form fields presence
        if (username == null || username.trim().isEmpty() || password == null || password.isEmpty()) {
            request.setAttribute("errorMessage", "Both username and password are required.");
            request.getRequestDispatcher("/login.jsp").forward(request, response);
            return;
        }

        // 2. Authenticate against MySQL users table
        User user = userDAO.authenticate(username.trim(), password);

        if (user != null) {
            // 3. Establish Secure HTTP Session
            HttpSession oldSession = request.getSession(false);
            if (oldSession != null) {
                oldSession.invalidate(); // Prevent session fixation attacks
            }

            HttpSession session = request.getSession(true);
            session.setAttribute("user", user);
            session.setMaxInactiveInterval(30 * 60); // 30 minutes idle timeout

            // Redirect to protected student dashboard
            response.sendRedirect(request.getContextPath() + "/students");
        } else {
            // Failed authentication
            request.setAttribute("errorMessage", "Invalid credentials. Please verify your username and password.");
            request.setAttribute("enteredUsername", username);
            request.getRequestDispatcher("/login.jsp").forward(request, response);
        }
    }
}
