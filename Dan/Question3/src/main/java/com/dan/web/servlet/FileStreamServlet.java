package com.dan.web.servlet;

import jakarta.servlet.ServletException;
import jakarta.servlet.annotation.WebServlet;
import jakarta.servlet.http.HttpServlet;
import jakarta.servlet.http.HttpServletRequest;
import jakarta.servlet.http.HttpServletResponse;

import java.io.BufferedInputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.IOException;
import java.io.OutputStream;
import java.nio.file.Files;
import java.nio.file.Path;
import java.nio.file.Paths;

/**
 * ==============================================================================
 * Web Technology Assignment II - Question 3: File Streaming Servlet
 * Package: com.dan.web.servlet.FileStreamServlet
 * 
 * Securely streams profile photos and uploaded student documents directly
 * to the HTTP client using binary I/O buffers, with strict directory traversal protection.
 * ==============================================================================
 */
@WebServlet(name = "FileStreamServlet", urlPatterns = {"/stream-file"})
public class FileStreamServlet extends HttpServlet {
    private static final long serialVersionUID = 1L;
    private static final int BUFFER_SIZE = 8192; // 8 KB chunks

    @Override
    protected void doGet(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {
        String rawFilename = request.getParameter("name");
        boolean isDownload = "true".equalsIgnoreCase(request.getParameter("download"));

        // 1. Validation: Prevent empty filename
        if (rawFilename == null || rawFilename.trim().isEmpty()) {
            response.sendError(HttpServletResponse.SC_BAD_REQUEST, "Filename parameter 'name' is required.");
            return;
        }

        // 2. Directory Traversal Defense: Strip path separators and extract safe basename
        Path sanitizedPath = Paths.get(rawFilename).getFileName();
        if (sanitizedPath == null) {
            response.sendError(HttpServletResponse.SC_BAD_REQUEST, "Invalid filename specified.");
            return;
        }
        String safeFilename = sanitizedPath.toString();

        if (safeFilename.contains("..") || safeFilename.contains("/") || safeFilename.contains("\\")) {
            response.sendError(HttpServletResponse.SC_FORBIDDEN, "Security violation: Directory traversal attempt detected.");
            return;
        }

        // 3. Resolve file location on disk (Shared with Question 2 uploads)
        File targetFile = resolveUploadFile(safeFilename);

        if (targetFile == null || !targetFile.exists() || !targetFile.isFile()) {
            // If requested an avatar that does not exist, send a 404
            response.sendError(HttpServletResponse.SC_NOT_FOUND, "The requested asset was not found on the server: " + safeFilename);
            return;
        }

        // 4. Determine MIME Content Type
        String contentType = Files.probeContentType(targetFile.toPath());
        if (contentType == null) {
            // Fallback content types based on file extension
            String lower = safeFilename.toLowerCase();
            if (lower.endsWith(".jpg") || lower.endsWith(".jpeg")) {
                contentType = "image/jpeg";
            } else if (lower.endsWith(".png")) {
                contentType = "image/png";
            } else if (lower.endsWith(".webp")) {
                contentType = "image/webp";
            } else if (lower.endsWith(".pdf")) {
                contentType = "application/pdf";
            } else if (lower.endsWith(".doc")) {
                contentType = "application/msword";
            } else if (lower.endsWith(".docx")) {
                contentType = "application/vnd.openxmlformats-officedocument.wordprocessingml.document";
            } else {
                contentType = "application/octet-stream";
            }
        }

        // 5. Configure HTTP Response Headers
        response.setContentType(contentType);
        response.setContentLengthLong(targetFile.length());

        // Set Content-Disposition (inline for viewing in browser, attachment for forced file download)
        String disposition = isDownload ? "attachment" : "inline";
        response.setHeader("Content-Disposition", disposition + "; filename=\"" + safeFilename + "\"");
        response.setHeader("Cache-Control", "private, max-age=3600"); // 1 hour browser cache

        // 6. Stream binary data efficiently using buffered I/O streams
        try (BufferedInputStream in = new BufferedInputStream(new FileInputStream(targetFile));
             OutputStream out = response.getOutputStream()) {

            byte[] buffer = new byte[BUFFER_SIZE];
            int bytesRead;
            while ((bytesRead = in.read(buffer)) != -1) {
                out.write(buffer, 0, bytesRead);
            }
            out.flush();
        } catch (IOException e) {
            System.err.println("Streaming interrupted for file: " + safeFilename + " - " + e.getMessage());
        }
    }

    /**
     * Resolves the upload directory across different runtime environments:
     * - System property `upload.dir`
     * - Relative path `../Question2/uploads`
     * - Default project directory `C:/Users/daniel/Documents/Dan/Question2/uploads`
     */
    private File resolveUploadFile(String filename) {
        // Option A: System property configured in Tomcat/JVM
        String customDir = System.getProperty("upload.dir");
        if (customDir != null) {
            File f = new File(customDir, filename);
            if (f.exists()) return f;
        }

        // Option B: Relative path to Question 2 uploads
        File relDir = new File("Question2/uploads", filename);
        if (relDir.exists()) return relDir;

        File parentRelDir = new File("../Question2/uploads", filename);
        if (parentRelDir.exists()) return parentRelDir;

        // Option C: Hardcoded workspace absolute path for local development consistency
        File localWs = new File("C:\\Users\\daniel\\Documents\\Dan\\Question2\\uploads", filename);
        if (localWs.exists()) return localWs;

        // Fallback to webapp root uploads folder if created
        String webappRoot = getServletContext().getRealPath("/uploads");
        if (webappRoot != null) {
            File f = new File(webappRoot, filename);
            if (f.exists()) return f;
        }

        // Return standard path reference even if not present so caller can verify exists()
        return localWs;
    }
}
