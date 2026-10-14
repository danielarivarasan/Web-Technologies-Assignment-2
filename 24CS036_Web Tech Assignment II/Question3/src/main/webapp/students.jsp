<%@ page language="java" contentType="text/html; charset=UTF-8" pageEncoding="UTF-8"%>
<%@ page import="java.util.List" %>
<%@ page import="com.dan.web.model.Student" %>
<%@ page import="com.dan.web.model.User" %>
<%--
  Web Technology Assignment II - Question 3: Dynamic Student Profile Portal
  File: Question3/src/main/webapp/students.jsp
  
  Displays multiple student profiles dynamically with uploaded file metadata
  and streams photos/documents securely through FileStreamServlet.
--%>
<%
  // Verify session user from AuthFilter or direct session check
  User currentUser = (User) session.getAttribute("user");
  if (currentUser == null) {
      response.sendRedirect(request.getContextPath() + "/login.jsp?error=unauthorized");
      return;
  }

  // Retrieve student records forwarded by StudentServlet
  @SuppressWarnings("unchecked")
  List<Student> students = (List<Student>) request.getAttribute("students");
  if (students == null) {
      // If accessed directly without going through servlet, redirect to servlet
      response.sendRedirect(request.getContextPath() + "/students");
      return;
  }

  int totalStudents = students.size();
%>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Profiles Portal | Question 3 (Servlets & JSP)</title>
  <link rel="stylesheet" href="${pageContext.request.contextPath}/css/style.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

  <div class="portal-container">
    <!-- Navbar with Session Information -->
    <header class="portal-navbar">
      <div class="brand-wrapper">
        <h2>Student Profile Portal</h2>
        <span>Question 3 &bull; Java Servlets, Dynamic JSP, JDBC & File Streaming</span>
      </div>

      <div class="user-session-panel">
        <div class="user-badge">
          <div class="user-avatar-circle">
            <%= currentUser.getFullName().substring(0, 1).toUpperCase() %>
          </div>
          <div>
            <div style="font-weight: 600; color: #0f172a;"><%= currentUser.getFullName() %></div>
            <div style="font-size: 0.75rem; color: #64748b;">Role: <strong><%= currentUser.getRole().toUpperCase() %></strong></div>
          </div>
        </div>

        <a href="${pageContext.request.contextPath}/logout" class="btn btn-secondary btn-sm" title="End Session">
          Logout &rarr;
        </a>
      </div>
    </header>

    <!-- Overview Stats Counters -->
    <section class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon">👥</div>
        <div>
          <div class="stat-value"><%= totalStudents %></div>
          <div class="stat-label">Enrolled Profiles</div>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">🗄️</div>
        <div>
          <div class="stat-value">MySQL</div>
          <div class="stat-label">Consistent DB Schema</div>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">⚡</div>
        <div>
          <div class="stat-value">Servlet API</div>
          <div class="stat-label">Binary Stream Engine</div>
        </div>
      </div>
    </section>

    <!-- Main Content: Dynamic Profile Cards -->
    <section>
      <div class="portal-section-header">
        <h3>Enrolled Student Profiles</h3>
        <span style="font-size: 0.85rem; color: #64748b;">Profiles retrieved via <code>StudentDAO</code> (JDBC PreparedStatement)</span>
      </div>

      <% if (students.isEmpty()) { %>
        <div class="login-card" style="max-width: 600px; margin: 40px auto; text-align: center;">
          <p>No student profiles found in MySQL database.</p>
          <p style="margin-top: 10px; color: #64748b; font-size: 0.85rem;">
            Add students via Question 2 PHP CRUD or verify <code>database/schema.sql</code> seed data.
          </p>
        </div>
      <% } else { %>
        <div class="profiles-grid">
          <% for (Student s : students) { %>
            <article class="profile-card">
              <!-- Card Header with Avatar streamed via Servlet -->
              <div class="profile-card-header">
                <% if (s.getProfileImage() != null && !s.getProfileImage().trim().isEmpty()) { %>
                  <!-- Stream image binary content dynamically through FileStreamServlet -->
                  <img 
                    src="${pageContext.request.contextPath}/stream-file?name=<%= java.net.URLEncoder.encode(s.getProfileImage(), "UTF-8") %>" 
                    alt="Photo of <%= s.getFullName() %>" 
                    class="student-img-streamed"
                    onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=<%= java.net.URLEncoder.encode(s.getFullName(), "UTF-8") %>&background=4f46e5&color=fff';"
                  >
                <% } else { %>
                  <div class="student-initials-placeholder">
                    <%= s.getInitials() %>
                  </div>
                <% } %>

                <div class="profile-title-block">
                  <h4><%= s.getFullName() %></h4>
                  <span class="roll-tag"><%= s.getRollNumber() %></span>
                </div>
              </div>

              <!-- Card Body with Metadata -->
              <div class="profile-card-body">
                <div class="meta-row">
                  <span class="label">Program / Course</span>
                  <span class="value" style="color: #4f46e5; font-weight: 600;"><%= s.getCourse() %></span>
                </div>

                <div class="meta-row">
                  <span class="label">Institutional Email</span>
                  <span class="value"><%= s.getEmail() %></span>
                </div>

                <!-- Stored File Metadata & Stream Controller -->
                <div class="stream-meta-box">
                  <div class="stream-meta-title">
                    <span>📁 Stored File Metadata</span>
                  </div>
                  <div class="stream-meta-list">
                    <div>
                      <strong>Photo:</strong> 
                      <% if (s.getProfileImage() != null) { %>
                        <code><%= s.getProfileImage() %></code> (<%= s.getFormattedProfileImageSize() %>)
                      <% } else { %>
                        <span style="color: #94a3b8;">None</span>
                      <% } %>
                    </div>
                    <div>
                      <strong>Document:</strong> 
                      <% if (s.getDocument() != null) { %>
                        <code><%= s.getDocument() %></code> (<%= s.getFormattedDocumentSize() %>)
                      <% } else { %>
                        <span style="color: #94a3b8;">None</span>
                      <% } %>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Card Footer with File Streaming Action Links -->
              <div class="profile-card-footer">
                <span style="font-size: 0.78rem; color: #64748b;">
                  Reg: <%= s.getCreatedAt() != null ? s.getCreatedAt().toString().substring(0, 10) : "N/A" %>
                </span>

                <div>
                  <% if (s.getDocument() != null && !s.getDocument().trim().isEmpty()) { %>
                    <a 
                      href="${pageContext.request.contextPath}/stream-file?name=<%= java.net.URLEncoder.encode(s.getDocument(), "UTF-8") %>&download=true" 
                      class="btn btn-stream" 
                      title="Stream and Download Attached Document via Servlet"
                    >
                      📥 Download Doc
                    </a>
                  <% } else { %>
                    <span style="font-size: 0.78rem; color: #94a3b8; font-style: italic;">No Document</span>
                  <% } %>
                </div>
              </div>
            </article>
          <% } %>
        </div>
      <% } %>
    </section>
  </div>

</body>
</html>
