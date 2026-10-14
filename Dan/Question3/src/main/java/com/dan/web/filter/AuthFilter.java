package com.dan.web.filter;

import jakarta.servlet.Filter;
import jakarta.servlet.FilterChain;
import jakarta.servlet.FilterConfig;
import jakarta.servlet.ServletException;
import jakarta.servlet.ServletRequest;
import jakarta.servlet.ServletResponse;
import jakarta.servlet.annotation.WebFilter;
import jakarta.servlet.http.HttpServletRequest;
import jakarta.servlet.http.HttpServletResponse;
import jakarta.servlet.http.HttpSession;

import java.io.IOException;

/**
 * ==============================================================================
 * Web Technology Assignment II - Question 3: Security & Session Filter
 * Package: com.dan.web.filter.AuthFilter
 * 
 * Intercepts protected portal endpoints and enforces active session authentication.
 * Prevents unauthorized access and direct JSP browsing.
 * ==============================================================================
 */
@WebFilter(filterName = "AuthFilter", urlPatterns = {"/students", "/students.jsp", "/stream-file"})
public class AuthFilter implements Filter {

    @Override
    public void init(FilterConfig filterConfig) throws ServletException {
        // Initialization if needed
    }

    @Override
    public void doFilter(ServletRequest request, ServletResponse response, FilterChain chain)
            throws IOException, ServletException {
        HttpServletRequest httpRequest = (HttpServletRequest) request;
        HttpServletResponse httpResponse = (HttpServletResponse) response;

        HttpSession session = httpRequest.getSession(false);
        boolean isLoggedIn = (session != null && session.getAttribute("user") != null);

        if (isLoggedIn) {
            // Prevent caching of protected pages in browser history
            httpResponse.setHeader("Cache-Control", "no-cache, no-store, must-revalidate");
            httpResponse.setHeader("Pragma", "no-cache");
            httpResponse.setDateHeader("Expires", 0);

            // Authorized, proceed to the requested resource
            chain.doFilter(request, response);
        } else {
            // Unauthorized access attempt: Redirect to login with alert notice
            String contextPath = httpRequest.getContextPath();
            httpResponse.sendRedirect(contextPath + "/login.jsp?error=unauthorized");
        }
    }

    @Override
    public void destroy() {
        // Cleanup if needed
    }
}
