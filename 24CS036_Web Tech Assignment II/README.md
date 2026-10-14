# Web Technology Assignment II - Complete Multi-Tier Student Portal

An end-to-end web technology project implementing client-side validation, server-side PHP/MySQL CRUD with secure file uploads, and an enterprise Java Servlets & JSP portal with JDBC data access and binary file streaming.

Developed inside the **`Dan`** workspace as a unified multi-tier project sharing a single MySQL database schema.

---

## 📂 Project Architecture & Directory Structure

```text
Dan/
├── README.md                      # Comprehensive project documentation & run guide
├── .gitignore                     # Git ignore rules (builds, uploads, credentials, IDE files)
├── database/
│   └── schema.sql                 # Unified MySQL database schema and seed data
├── Question1/                     # Client-Side Web Application
│   ├── index.html                 # Responsive student registration form
│   ├── style.css                  # Modern CSS (Flexbox, Grid, CSS Variables)
│   ├── script.js                  # Real-time client-side validation & strength meter
│   └── screenshots/               # Screenshot artifacts directory
│       └── README.md
├── Question2/                     # Server-Side PHP & MySQL CRUD Application
│   ├── index.php                  # Student records dashboard with search & filter
│   ├── db.php                     # Persistent PDO database connection
│   ├── add_student.php            # Add student with file upload validation
│   ├── edit_student.php           # Edit student details and replace documents
│   ├── delete_student.php         # Safe deletion with physical disk file cleanup
│   ├── view_student.php           # Detailed profile card with file metadata
│   ├── upload.php                 # Reusable secure file upload & MIME validation engine
│   ├── style.css                  # Modern responsive dashboard & card styling
│   ├── uploads/                   # Stored student photos and documents
│   │   ├── .gitkeep               # Preserves uploads folder in repository
│   │   ├── sample_avatar_1.png    # Seed test avatar
│   │   └── dan_resume.pdf         # Seed test document
│   └── screenshots/               # Question 2 screenshot artifacts
│       └── README.md
└── Question3/                     # Enterprise Java Servlets, JSP & JDBC Portal
    ├── pom.xml                    # Maven project configuration (Jakarta EE 10, MySQL)
    ├── src/main/java/             # Backend Java Source Code
    │   └── com/dan/web/
    │       ├── model/
    │       │   ├── Student.java   # Student entity model
    │       │   └── User.java      # Authenticated user model
    │       ├── util/
    │       │   └── DBConnection.java # JDBC connection factory & SHA-256 hasher
    │       ├── dao/
    │       │   ├── StudentDAO.java   # JDBC PreparedStatements for students
    │       │   └── UserDAO.java      # Authentication queries
    │       ├── servlet/
    │       │   ├── LoginServlet.java # Session management & login handler
    │       │   ├── LogoutServlet.java# Session termination
    │       │   ├── StudentServlet.java # Portal loader & request dispatcher
    │       │   └── FileStreamServlet.java # Secure binary file streaming
    │       └── filter/
    │           └── AuthFilter.java   # Security interceptor for protected views
    ├── src/main/webapp/           # Web Presentation Layer
    │   ├── WEB-INF/
    │   │   └── web.xml            # Deployment descriptor & session config
    │   ├── login.jsp              # Admin login page
    │   ├── students.jsp           # Dynamic profile grid with streamed files
    │   └── css/
    │       └── style.css          # Portal UI stylesheet
    └── screenshots/               # Question 3 screenshot artifacts
        └── README.md
```

---

## 🛠️ Prerequisites & Environment Setup

### 1. Recommended Visual Studio Code Extensions
Install the following extensions from the VS Code Marketplace:
- **Live Server** (`ritwickdey.liveserver`) — for Question 1 live reload.
- **PHP Intelephense** (`bmewburn.vscode-intelephense-client`) — for PHP syntax and code intelligence.
- **Extension Pack for Java** (`vscjava.vscode-java-pack`) — provides Java language support, debugger, and Maven integration.
- **Community Server Connectors** or **Tomcat for Java** — to manage Apache Tomcat directly from VS Code.
- **MySQL** (`cweijan.vscode-database-client2`) — to explore and query the MySQL database inside VS Code.

---

### 2. Database Setup (MySQL)
Both Question 2 and Question 3 connect to the **same** database: `student_portal_db`.

#### Option A: Using phpMyAdmin (XAMPP / WAMP)
1. Start **Apache** and **MySQL** in your XAMPP/WAMP Control Panel.
2. Open your web browser and navigate to `http://localhost/phpmyadmin`.
3. Click the **Import** tab.
4. Choose the file located at: `Dan/database/schema.sql`.
5. Click **Import** (or **Go**).
6. Verify that `student_portal_db` is created with tables `users` and `students`.

#### Option B: Using MySQL Command Line
Run the following in PowerShell / Command Prompt:
```powershell
mysql -u root -p < "C:\Users\daniel\Documents\Dan\database\schema.sql"
```
*(Press Enter if you do not have a root password set).*

---

### 3. PHP Environment Setup (Question 2)
- Ensure PHP 8.0+ is installed (bundled with XAMPP or standalone).
- Verify in `php.ini` that `extension=pdo_mysql` and `extension=fileinfo` are enabled (default in modern XAMPP).
- Verify database credentials in `Question2/db.php`:
  - `DB_HOST`: `127.0.0.1`
  - `DB_PORT`: `3306`
  - `DB_NAME`: `student_portal_db`
  - `DB_USER`: `root`
  - `DB_PASS`: `""` (empty by default for local XAMPP/WAMP; change if your root has a password)

---

### 4. Java, Maven & Apache Tomcat Setup (Question 3)
- **Java JDK**: JDK 17, 21, or 25 (e.g. Eclipse Adoptium OpenJDK). Verify by running:
  ```powershell
  java -version
  ```
- **Apache Maven**: Verify installation:
  ```powershell
  mvn -version
  ```
- **Apache Tomcat**: Recommended **Tomcat 10.1.x** or **Tomcat 11.x** (which natively support the `jakarta.servlet` specification used in modern Java).

---

## 🚀 How to Run Each Project Separately

### Running Question 1 — HTML, CSS & JavaScript
1. Open the folder `Dan/Question1` in VS Code.
2. Right-click `index.html` and click **"Open with Live Server"**.
3. Alternatively, simply double-click `index.html` to open it directly in any modern browser (Chrome, Firefox, Edge).
4. URL: `http://127.0.0.1:5500/Question1/index.html` (or `file:///.../Question1/index.html`).

---

### Running Question 2 — PHP & MySQL CRUD
You can run Question 2 either via the PHP built-in web server or via XAMPP Apache:

#### Option A: PHP Built-in Server (Fastest)
1. Open PowerShell and navigate to the `Question2` directory:
   ```powershell
   cd "C:\Users\daniel\Documents\Dan\Question2"
   ```
2. Start the PHP server:
   ```powershell
   php -S localhost:8000
   ```
3. Open your browser and navigate to:
   ```text
   http://localhost:8000/index.php
   ```

#### Option B: Via XAMPP Apache
1. Copy or create a junction/symbolic link of `Dan` inside `C:\xampp\htdocs\`:
   ```powershell
   New-Item -ItemType SymbolicLink -Path "C:\xampp\htdocs\Dan" -Target "C:\Users\daniel\Documents\Dan"
   ```
2. Open your browser at:
   ```text
   http://localhost/Dan/Question2/index.php
   ```

---

### Running Question 3 — Java Servlets, JSP & JDBC Portal

#### Step 1: Package the Application with Maven
1. Open PowerShell in `Question3`:
   ```powershell
   cd "C:\Users\daniel\Documents\Dan\Question3"
   ```
2. Build the WAR (Web Application Archive) file:
   ```powershell
   mvn clean package
   ```
3. A successful build will generate the file:
   `Question3/target/student-portal.war`

#### Step 2: Deploy to Apache Tomcat
1. Copy `target/student-portal.war` to your Apache Tomcat `webapps/` folder:
   ```powershell
   Copy-Item "target\student-portal.war" -Destination "C:\apache-tomcat-10.1.x\webapps\"
   ```
2. Start Apache Tomcat by running:
   ```powershell
   & "C:\apache-tomcat-10.1.x\bin\startup.bat"
   ```
3. Open your browser and navigate to:
   ```text
   http://localhost:8080/student-portal/
   ```
4. Log in using the verified seed credentials:
   - **Username**: `admin`
   - **Password**: `Admin@123`

---

## 🧪 Testing Instructions & Test Cases

### Question 1: Client-Side Validation Testing
| Test Case | Steps to Test | Expected Result |
| :--- | :--- | :--- |
| **TC1.1: Empty Submission** | Click "Register Student" with all fields empty | Form submission prevented; all required fields outlined in red with explicit error messages below each input; focus shifts to the first invalid field. |
| **TC1.2: Invalid Full Name** | Enter numbers or special characters (e.g. `Dan123!`) | Shows error: *"Full name can only contain alphabetic letters and single spaces."* |
| **TC1.3: Roll Number Pattern** | Enter short string (e.g. `12`) | Shows error requiring 3–20 alphanumeric characters or hyphens. |
| **TC1.4: Password Strength** | Type `pass`, then `Pass123`, then `Pass@1234` | Real-time strength meter transitions dynamically from **Weak (Red)** to **Fair (Amber)** to **Strong (Green)**. |
| **TC1.5: Confirm Password** | Type non-matching passwords | Shows error: *"Passwords do not match."* |
| **TC1.6: File Validation** | Drop an executable or file > 2MB (e.g. `.exe`) | Shows error: *"Invalid file type"* or *"exceeds 2 MB"*. Valid `.png`/`.pdf` shows filename and size preview. |
| **TC1.7: Valid Registration** | Fill valid data, accept declaration, submit | Displays interactive summary modal detailing all entered data with verified badge. |

---

### Question 2: PHP & MySQL CRUD Testing
| Test Case | Steps to Test | Expected Result |
| :--- | :--- | :--- |
| **TC2.1: View Records** | Navigate to `index.php` | Displays sample students seeded from `schema.sql` with avatars, roll numbers, and badges. |
| **TC2.2: Add Student** | Click "+ Add New Student", upload a picture and a PDF, submit | System validates MIME via `finfo_file`, generates unique filename, stores file in `uploads/`, records metadata in MySQL, redirects with green flash alert. |
| **TC2.3: Duplicate Rejection** | Attempt adding another student with roll number `WT-2026-001` | Server rejects with error: *"A student with Roll Number 'WT-2026-001' already exists."* |
| **TC2.4: Profile Card View** | Click "View" on any student | Navigates to `view_student.php`, showing rendered profile picture, details, and exact file metadata (stored filename, MIME, size in KB). |
| **TC2.5: Edit & Replace File** | Click "Edit", modify name, upload a replacement photo | Old photo is unlinked from `uploads/`, new photo is saved, database record is updated via prepared statement. |
| **TC2.6: Delete Student** | Click "Delete" and accept confirmation prompt | Record is deleted from MySQL; associated uploaded files are purged from disk; redirects with success flash message. |

---

### Question 3: Java Servlets & JSP Portal Testing
| Test Case | Steps to Test | Expected Result |
| :--- | :--- | :--- |
| **TC3.1: Unauthorized Access** | Directly visit `http://localhost:8080/student-portal/students` without logging in | `AuthFilter` intercepts the request and redirects to `login.jsp?error=unauthorized`. |
| **TC3.2: Invalid Login** | Enter `admin` / `WrongPass` | Shows red error alert: *"Invalid credentials. Please verify your username and password."* |
| **TC3.3: Successful Login** | Enter `admin` / `Admin@123` | Session created; redirects to `/students`; displays user badge with administrator role. |
| **TC3.4: Dynamic Profile Rendering** | Inspect student profile cards | `StudentDAO` queries MySQL `students` table via JDBC `PreparedStatement`; cards dynamically reflect database contents. |
| **TC3.5: Binary File Streaming** | Inspect the profile image `<img>` tag in `students.jsp` | Image source points to `/stream-file?name=...`. `FileStreamServlet` streams raw bytes directly from server storage with proper MIME headers. |
| **TC3.6: File Download Streaming** | Click "Download Doc" on any student card | Request triggers `/stream-file?name=...&download=true`; browser prompts standard file download with `Content-Disposition: attachment`. |
| **TC3.7: Secure Logout** | Click "Logout" | `LogoutServlet` invalidates `HttpSession`; redirects to `login.jsp?logout=true`. Pressing browser back button cannot access protected portal. |

---

## 📸 Screenshot Submission Guidance

Place your assignment evidence screenshots into the dedicated folders:

1. **`Question1/screenshots/`**:
   - `01_initial_form.png`: Clean form layout on desktop.
   - `02_validation_errors.png`: Inline red error messages and validation highlights.
   - `03_password_strength.png`: Password complexity meter in action.
   - `04_success_modal.png`: Summary modal confirming successful client validation.
   - `05_mobile_view.png`: Responsive layout on simulated mobile screen.

2. **`Question2/screenshots/`**:
   - `01_dashboard.png`: Records table with thumbnails, download links, search, and filter.
   - `02_add_student.png`: Enrollment form with file pickers.
   - `03_student_profile.png`: Complete profile view with file metadata from MySQL.
   - `04_edit_and_delete.png`: Edit form and deletion success alert.
   - `05_mysql_table.png`: MySQL table view (phpMyAdmin or terminal) showing stored metadata.

3. **`Question3/screenshots/`**:
   - `01_login_view.png`: Admin login interface with testing credentials.
   - `02_unauthorized_redirect.png`: `AuthFilter` access denial notice.
   - `03_students_portal.png`: Dynamic profile cards dashboard with active user session.
   - `04_file_streaming.png`: DevTools Network tab showing `FileStreamServlet` 200 OK binary stream.
   - `05_maven_build.png`: Terminal output showing `mvn clean package` BUILD SUCCESS.

---

## 📝 Academic Conclusions

### Question 1 Conclusion
> Implementing client-side validation using vanilla JavaScript demonstrated how immediate feedback enhances user experience, minimizes server strain, and prevents malformed data submissions. Employing semantic HTML5 elements alongside responsive CSS Flexbox and Grid ensured high accessibility and consistent rendering across various device viewports. Furthermore, modular regular expressions and dynamic DOM state manipulations established robust data verification before transaction processing.

### Question 2 Conclusion
> The PHP and MySQL CRUD application highlighted the critical importance of defensive programming and parameterized queries using PDO to prevent SQL injection vulnerabilities. By combining strict server-side MIME verification through `finfo`, file size enforcement, and cryptographically unique filename generation, the application eliminated malicious file upload threats. Additionally, storing structured file metadata in MySQL while persisting binary content on the filesystem achieved optimal database performance and scalable asset management.

### Question 3 Conclusion
> Developing the enterprise Java profile portal demonstrated the robust separation of concerns provided by the Model-View-Controller (MVC) design pattern using Servlets, JSP, and JDBC. Leveraging `PreparedStatement` within DAO classes ensured safe relational data mapping, while `HttpSession` and `AuthFilter` enforced enterprise-grade session protection and prevented unauthorized URI traversal. Finally, streaming binary file data directly through a dedicated servlet using buffered I/O streams safeguarded sensitive server paths and provided granular control over HTTP content headers.

---

## 📦 Single Git Repository Submission Commands

All three questions are located in this single `Dan` directory. Follow these steps to commit and push the repository to GitHub / GitLab:

> **Note**: Verify you have Git installed (`git --version`). If not already initialized, execute the commands below directly inside `C:\Users\daniel\Documents\Dan`.

```powershell
# 1. Navigate to the Dan workspace
cd "C:\Users\daniel\Documents\Dan"

# 2. Check if Git is initialized (Initialize if not already a repo)
git status
# If "fatal: not a git repository", initialize it:
git init

# 3. Configure your Git user details (replace with your info)
git config user.name "Your Name"
git config user.email "your.email@example.com"

# 4. Review untracked files (.gitignore will exclude binaries and uploads)
git status

# 5. Stage all files across Question1, Question2, Question3, and database
git add .

# 6. Commit the entire project
git commit -m "Complete Web Technology Assignment II: Q1 (JS Form), Q2 (PHP CRUD), Q3 (Java Servlets/JSP)"

# 7. (Optional) Link to your remote GitHub repository and push
# Replace the URL below with your GitHub repository URL:
git branch -M main
git remote add origin https://github.com/your-username/Dan-WebTech-Assignment2.git
git push -u origin main
```
