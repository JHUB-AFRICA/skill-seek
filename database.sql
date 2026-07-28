-- =============================================
-- SkillSeek Database Schema
-- Version: 1.0
-- Description: Complete database for SkillSeek platform
-- =============================================

-- Drop database if exists and create fresh
DROP DATABASE IF EXISTS skillseek;
CREATE DATABASE skillseek;
USE skillseek;

-- =============================================
-- 1. USERS TABLE
-- Stores all user accounts (both employers and students)
-- =============================================
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    role ENUM('employer', 'student') NOT NULL,
    phone VARCHAR(20),
    profile_pic VARCHAR(255) DEFAULT 'default-avatar.png',
    bio TEXT,
    location VARCHAR(100),
    is_active BOOLEAN DEFAULT TRUE,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 2. EMPLOYER PROFILES TABLE
-- Additional information for employers
-- =============================================
CREATE TABLE employer_profiles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL UNIQUE,
    company_name VARCHAR(255) NOT NULL,
    company_description TEXT,
    industry VARCHAR(100),
    company_size VARCHAR(50),
    website VARCHAR(255),
    logo VARCHAR(255),
    established_year YEAR,
    is_verified BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_company_name (company_name),
    INDEX idx_industry (industry)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 3. STUDENT PROFILES TABLE
-- Additional information for students
-- =============================================
CREATE TABLE student_profiles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL UNIQUE,
    skills TEXT,
    education TEXT,
    experience TEXT,
    portfolio VARCHAR(255),
    resume VARCHAR(255),
    github_url VARCHAR(255),
    linkedin_url VARCHAR(255),
    hourly_rate DECIMAL(10,2),
    is_available BOOLEAN DEFAULT TRUE,
    rating DECIMAL(3,2) DEFAULT 0.00,
    total_jobs_completed INT DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_is_available (is_available),
    INDEX idx_rating (rating)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 4. CATEGORIES TABLE
-- Job categories for organizing listings
-- =============================================
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    icon VARCHAR(50),
    color VARCHAR(7) DEFAULT '#3498db',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 5. JOBS TABLE
-- Job listings posted by employers
-- =============================================
CREATE TABLE jobs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    employer_id INT NOT NULL,
    category_id INT,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    requirements TEXT,
    responsibilities TEXT,
    budget_min DECIMAL(10,2),
    budget_max DECIMAL(10,2),
    budget_type ENUM('fixed', 'hourly', 'negotiable') DEFAULT 'fixed',
    location VARCHAR(255),
    is_remote BOOLEAN DEFAULT FALSE,
    status ENUM('draft', 'open', 'in_progress', 'completed', 'cancelled') DEFAULT 'open',
    expires_at DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (employer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_employer (employer_id),
    INDEX idx_category (category_id),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 6. APPLICATIONS TABLE
-- Student applications for jobs
-- =============================================
CREATE TABLE applications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    job_id INT NOT NULL,
    student_id INT NOT NULL,
    cover_letter TEXT,
    proposed_rate DECIMAL(10,2),
    estimated_days INT,
    status ENUM('pending', 'reviewed', 'shortlisted', 'accepted', 'rejected', 'withdrawn') DEFAULT 'pending',
    employer_notes TEXT,
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_application (job_id, student_id),
    INDEX idx_status (status),
    INDEX idx_student (student_id),
    INDEX idx_job (job_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 7. PAYMENTS TABLE
-- MPESA payment tracking
-- =============================================
CREATE TABLE payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    job_id INT NOT NULL,
    employer_id INT NOT NULL,
    student_id INT NOT NULL,
    application_id INT,
    amount DECIMAL(10,2) NOT NULL,
    mpesa_checkout_id VARCHAR(100) UNIQUE,
    mpesa_code VARCHAR(50),
    phone_number VARCHAR(20),
    status ENUM('pending', 'processing', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    payment_type ENUM('deposit', 'milestone', 'full') DEFAULT 'full',
    milestone_description VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL,
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
    FOREIGN KEY (employer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_mpesa (mpesa_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 8. MESSAGES TABLE
-- Chat system for communication
-- =============================================
CREATE TABLE messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    job_id INT,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
    INDEX idx_sender (sender_id),
    INDEX idx_receiver (receiver_id),
    INDEX idx_job (job_id),
    INDEX idx_read (is_read),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 9. REVIEWS TABLE
-- Employer and student reviews
-- =============================================
CREATE TABLE reviews (
    id INT PRIMARY KEY AUTO_INCREMENT,
    job_id INT NOT NULL,
    reviewer_id INT NOT NULL,
    reviewee_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewee_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_review (job_id, reviewer_id, reviewee_id),
    INDEX idx_reviewee (reviewee_id),
    INDEX idx_rating (rating)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 10. NOTIFICATIONS TABLE
-- User notifications
-- =============================================
CREATE TABLE notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    type VARCHAR(50) NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    link VARCHAR(255),
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_read (is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- INSERT SAMPLE DATA
-- =============================================

-- Insert categories
INSERT INTO categories (name, description, icon, color) VALUES
('Web Development', 'Build websites, web applications, and web services', 'fa-code', '#3498db'),
('Mobile Development', 'Create mobile apps for iOS and Android platforms', 'fa-mobile-alt', '#e67e22'),
('Design & Creative', 'UI/UX design, graphic design, logo creation, branding', 'fa-paint-brush', '#9b59b6'),
('Writing & Content', 'Content writing, copywriting, technical writing, blogging', 'fa-pen-fancy', '#2ecc71'),
('Digital Marketing', 'SEO, social media marketing, email marketing, analytics', 'fa-bullhorn', '#f39c12'),
('Data Science', 'Data analysis, machine learning, AI, data visualization', 'fa-chart-bar', '#1abc9c'),
('Video & Animation', 'Video editing, animation, motion graphics, filming', 'fa-video', '#e74c3c'),
('Customer Support', 'Customer service, help desk, technical support', 'fa-headset', '#34495e'),
('Sales', 'Sales development, business development, lead generation', 'fa-handshake', '#27ae60'),
('Finance', 'Financial analysis, accounting, bookkeeping, auditing', 'fa-coins', '#d35400');

-- Insert sample employer
INSERT INTO users (email, password, full_name, role, phone, location) VALUES
('techcorp@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John Smith', 'employer', '+254712345678', 'Nairobi, Kenya');

-- Note: Password for techcorp@email.com is 'password123'

INSERT INTO employer_profiles (user_id, company_name, company_description, industry, company_size, website, is_verified) VALUES
(1, 'TechCorp Solutions', 'Leading technology company providing innovative solutions', 'Technology', '50-100', 'https://techcorp.com', TRUE);

-- Insert sample student
INSERT INTO users (email, password, full_name, role, phone, location) VALUES
('student@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jane Doe', 'student', '+254798765432', 'Mombasa, Kenya');

INSERT INTO student_profiles (user_id, skills, education, experience, github_url, linkedin_url, is_available) VALUES
(2, 'PHP, JavaScript, React, Node.js, MySQL, HTML5, CSS3', 'BSc Computer Science - University of Nairobi', '2 years freelance web development', 'https://github.com/janedoe', 'https://linkedin.com/in/janedoe', TRUE);

-- Insert sample job
INSERT INTO jobs (employer_id, category_id, title, description, requirements, budget_min, budget_max, budget_type, location, is_remote, status) VALUES
(1, 1, 'Full Stack Web Developer Needed', 
'We are looking for a talented full stack web developer to build a modern e-commerce platform. The project involves creating a responsive web application with user authentication, payment integration, and admin dashboard.', 
'Required skills:
- Strong PHP and MySQL experience
- Knowledge of JavaScript frameworks (React/Vue)
- Experience with REST APIs
- Understanding of security best practices
- Git version control

Nice to have:
- Experience with AWS or cloud services
- Knowledge of payment gateway integration
- UI/UX design skills',

50000, 80000, 'fixed', 'Nairobi, Kenya', TRUE, 'open');

-- Insert sample application
INSERT INTO applications (job_id, student_id, cover_letter, proposed_rate, estimated_days, status) VALUES
(1, 2, 
'I am very interested in this position. I have 2 years of experience in full stack web development and have successfully delivered similar projects. I am confident in my ability to build a robust e-commerce platform that meets your requirements.', 
70000, 30, 'pending');

-- Insert sample message
INSERT INTO messages (sender_id, receiver_id, job_id, message) VALUES
(2, 1, 1, 'Hello! I just applied for your Full Stack Web Developer position. I would love to discuss the project details further.');

-- Insert sample notification
INSERT INTO notifications (user_id, type, title, message, link) VALUES
(1, 'application', 'New Application Received', 'Jane Doe has applied for your Full Stack Web Developer job', '/employer/applications.php?job_id=1');

-- Insert sample review
INSERT INTO reviews (job_id, reviewer_id, reviewee_id, rating, comment) VALUES
(1, 1, 2, 5, 'Excellent work! Jane delivered the project on time and exceeded our expectations. Highly recommended!');

-- =============================================
-- CREATE VIEWS FOR COMMON QUERIES
-- =============================================

-- View: Job details with employer info
CREATE VIEW v_job_details AS
SELECT 
    j.*,
    u.full_name as employer_name,
    u.phone as employer_phone,
    u.email as employer_email,
    ep.company_name,
    c.name as category_name,
    c.color as category_color
FROM jobs j
JOIN users u ON j.employer_id = u.id
LEFT JOIN employer_profiles ep ON u.id = ep.user_id
LEFT JOIN categories c ON j.category_id = c.id;

-- View: Application details with student and job info
CREATE VIEW v_application_details AS
SELECT 
    a.*,
    j.title as job_title,
    j.budget_min,
    j.budget_max,
    u.full_name as student_name,
    u.email as student_email,
    u.phone as student_phone,
    sp.skills,
    sp.rating as student_rating
FROM applications a
JOIN jobs j ON a.job_id = j.id
JOIN users u ON a.student_id = u.id
LEFT JOIN student_profiles sp ON u.id = sp.user_id;

-- View: Message count for chat list
CREATE VIEW v_message_counts AS
SELECT 
    u.id as user_id,
    u.full_name,
    u.role,
    COUNT(m.id) as unread_count,
    MAX(m.created_at) as last_message
FROM users u
LEFT JOIN messages m ON (m.receiver_id = u.id AND m.is_read = FALSE)
WHERE m.receiver_id IS NOT NULL
GROUP BY u.id;

-- =============================================
-- CREATE STORED PROCEDURES
-- =============================================

-- Procedure: Apply for a job
DELIMITER //
CREATE PROCEDURE apply_for_job(
    IN p_job_id INT,
    IN p_student_id INT,
    IN p_cover_letter TEXT,
    IN p_proposed_rate DECIMAL(10,2),
    IN p_estimated_days INT
)
BEGIN
    INSERT INTO applications (job_id, student_id, cover_letter, proposed_rate, estimated_days)
    VALUES (p_job_id, p_student_id, p_cover_letter, p_proposed_rate, p_estimated_days);
    
    -- Create notification for employer
    INSERT INTO notifications (user_id, type, title, message, link)
    SELECT 
        j.employer_id,
        'application',
        'New Application Received',
        CONCAT((SELECT full_name FROM users WHERE id = p_student_id), ' has applied for ', j.title),
        CONCAT('/employer/applications.php?job_id=', p_job_id)
    FROM jobs j
    WHERE j.id = p_job_id;
END //
DELIMITER ;

-- Procedure: Mark messages as read
DELIMITER //
CREATE PROCEDURE mark_messages_read(
    IN p_user_id INT,
    IN p_sender_id INT
)
BEGIN
    UPDATE messages 
    SET is_read = TRUE, read_at = NOW()
    WHERE receiver_id = p_user_id 
    AND sender_id = p_sender_id 
    AND is_read = FALSE;
END //
DELIMITER ;

-- =============================================
-- CREATE TRIGGERS
-- =============================================

-- Trigger: Update student rating after review
DELIMITER //
CREATE TRIGGER update_student_rating
AFTER INSERT ON reviews
FOR EACH ROW
BEGIN
    DECLARE avg_rating DECIMAL(3,2);
    
    SELECT AVG(rating) INTO avg_rating
    FROM reviews
    WHERE reviewee_id = NEW.reviewee_id;
    
    UPDATE student_profiles 
    SET rating = avg_rating,
        total_jobs_completed = (
            SELECT COUNT(*) 
            FROM reviews 
            WHERE reviewee_id = NEW.reviewee_id
        )
    WHERE user_id = NEW.reviewee_id;
END //
DELIMITER ;

-- Trigger: Update job status when application is accepted
DELIMITER //
CREATE TRIGGER update_job_status
AFTER UPDATE ON applications
FOR EACH ROW
BEGIN
    IF NEW.status = 'accepted' AND OLD.status != 'accepted' THEN
        UPDATE jobs 
        SET status = 'in_progress' 
        WHERE id = NEW.job_id;
    END IF;
END //
DELIMITER ;

-- =============================================
-- INDEXES FOR PERFORMANCE
-- =============================================

-- Additional indexes for faster queries
CREATE INDEX idx_jobs_status_created ON jobs(status, created_at);
CREATE INDEX idx_applications_status_created ON applications(status, applied_at);
CREATE INDEX idx_messages_sender_receiver ON messages(sender_id, receiver_id, created_at);
CREATE INDEX idx_notifications_user_read ON notifications(user_id, is_read);

-- =============================================
-- FINAL OUTPUT MESSAGE
-- =============================================
SELECT '✅ SkillSeek database created successfully!' as status;
SELECT '📊 Tables created: 10' as info;
SELECT '📝 Sample data inserted' as info;
SELECT '👁️ Views created: 3' as info;
SELECT '⚡ Stored procedures: 2' as info;
SELECT '🔔 Triggers: 2' as info;