<%@ page language="java" contentType="text/html; charset=UTF-8" pageEncoding="UTF-8"%>
<%@ taglib prefix="c" uri="jakarta.tags.core" %>
<%-- 
  Web Technology Assignment II - Question 3: Secure Login Portal View
  File: Question3/src/main/webapp/login.jsp
  
  Authenticates administrators against the shared MySQL database with session protection.
--%>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login | Student Profile Portal (Question 3)</title>
  <link rel="stylesheet" href="${pageContext.request.contextPath}/css/style.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="login-page">

  <div class="login-card">
    <div class="login-header">
      <div class="login-icon">🎓</div>
      <h1>Student Portal Login</h1>
      <p>Question 3 &bull; Java Servlets, JSP, JDBC & Secure Sessions</p>
    </div>

    <!-- Demo Credentials Notice -->
    <div class="demo-credentials-box">
      <div><strong>Testing Credentials (from schema.sql):</strong></div>
      <div>Username: <code>admin</code></div>
      <div>Password: <code>Admin@123</code></div>
    </div>

    <!-- Error Messages -->
    <%
      String errorParam = request.getParameter("error");
      String logoutParam = request.getParameter("logout");
      String errorMsg = (String) request.getAttribute("errorMessage");
      String enteredUser = (String) request.getAttribute("enteredUsername");
      if (enteredUser == null) enteredUser = "";
    %>

    <% if (errorMsg != null) { %>
      <div class="alert alert-danger">
        <%= errorMsg %>
      </div>
    <% } else if ("unauthorized".equals(errorParam)) { %>
      <div class="alert alert-danger">
        Access denied. Please log in with valid credentials to view the student portal.
      </div>
    <% } %>

    <% if ("true".equals(logoutParam)) { %>
      <div class="alert alert-success">
        You have been logged out securely. Session invalidated.
      </div>
    <% } %>

    <form method="POST" action="${pageContext.request.contextPath}/login">
      <div class="form-group">
        <label for="username">Username</label>
        <input 
          type="text" 
          id="username" 
          name="username" 
          class="form-input" 
          placeholder="e.g. admin" 
          value="<%= enteredUser %>" 
          required 
          autofocus
        >
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input 
          type="password" 
          id="password" 
          name="password" 
          class="form-input" 
          placeholder="Enter password" 
          required
        >
      </div>

      <div style="margin-top: 24px;">
        <button type="submit" class="btn btn-primary btn-block">
          Secure Sign In &rarr;
        </button>
      </div>
    </form>
  </div>

</body>
</html>
