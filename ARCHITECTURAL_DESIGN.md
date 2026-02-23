# Architectural Design & Technical Architecture
## Online LMS Platform

**Version:** 1.0  
**Last Updated:** February 23, 2026  
**Architecture Owner:** Technical Lead

---

## Table of Contents
1. [System Architecture Overview](#system-architecture-overview)
2. [Technology Stack](#technology-stack)
3. [Application Structure](#application-structure)
4. [Database Design](#database-design)
5. [API Architecture](#api-architecture)
6. [Frontend Architecture](#frontend-architecture)
7. [Security Architecture](#security-architecture)
8. [Scalability Considerations](#scalability-considerations)
9. [Deployment Architecture](#deployment-architecture)

---

## System Architecture Overview

### High-Level Architecture Diagram

```
┌─────────────────────────────────────────────────────────────┐
│                     Client Layer                             │
│  ┌──────────────┐  ┌───────────────┐  ┌──────────────────┐ │
│  │ Web Browser  │  │ Mobile App    │  │ Admin Dashboard  │ │
│  └──────────────┘  └───────────────┘  └──────────────────┘ │
└────────────────────────────┬────────────────────────────────┘
                             │ HTTPS
┌────────────────────────────▼────────────────────────────────┐
│               API & Application Server                       │
│  ┌──────────────────────────────────────────────────────┐  │
│  │  Laravel Framework (PHP 8.x)                         │  │
│  │  ├─ Vue.js / Blade Templates (Frontend)             │  │
│  │  ├─ RESTful API Endpoints                           │  │
│  │  └─ Business Logic & Controllers                    │  │
│  └──────────────────────────────────────────────────────┘  │
└────────────────────────────┬────────────────────────────────┘
                             │
        ┌────────────────────┼────────────────────┐
        │                    │                    │
┌───────▼──────┐  ┌──────────▼────────┐  ┌──────▼────────┐
│   Database   │  │  File Storage     │  │  Cache Layer  │
│  (MySQL)     │  │  (Public Disk)    │  │  (Redis)      │
└──────────────┘  └───────────────────┘  └───────────────┘
```

### Architecture Principles

1. **Separation of Concerns:** Each component has a single, well-defined responsibility
2. **MVC Pattern:** Models handle data, Views handle presentation, Controllers handle logic
3. **RESTful Design:** API endpoints follow REST conventions
4. **Modularity:** Features are organized into self-contained modules
5. **Security First:** Authentication, authorization, and data protection at all layers
6. **Scalability:** Design allows for horizontal scaling and caching

---

## Technology Stack

### Backend
| Component | Technology | Version | Purpose |
|-----------|-----------|---------|---------|
| Framework | Laravel | 11.x | Web application framework |
| Language | PHP | 8.2+ | Server-side programming |
| Database | MySQL | 8.0+ | Primary data storage |
| Cache | Redis | 7.0+ | Session & query caching |
| Task Queue | Laravel Queue | 11.x | Async job processing |
| ORM | Eloquent | 11.x | Database abstraction |

### Frontend
| Component | Technology | Purpose |
|-----------|-----------|---------|
| Framework | Bootstrap | Responsive UI components |
| Icons | Bootstrap Icons | Icon library |
| Templating | Blade | Server-side template engine |
| Animations | AOS | Scroll animations |
| Build Tool | Vite | Asset bundling & HMR |

### External Services
| Service | Purpose |
|---------|---------|
| Zoom API | Live session integration |
| SMTP Mail | Email notifications |
| File Storage | Cloud storage (optional) |

---

## Application Structure

### Directory Organization

```
online_lms/
├── app/
│   ├── Http/
│   │   ├── Controllers/        # Request handlers
│   │   │   ├── Admin/          # Admin functionality
│   │   │   ├── Instructor/     # Instructor operations
│   │   │   └── Student/        # Student features
│   │   ├── Middleware/         # Request filters
│   │   └── Requests/           # Form validation
│   ├── Models/                 # Eloquent models
│   │   ├── User.php
│   │   ├── Course.php
│   │   ├── CourseContent.php
│   │   ├── CourseQuiz.php
│   │   ├── HomepageSetting.php
│   │   └── ...
│   ├── Services/               # Business logic
│   │   ├── CourseService.php
│   │   ├── EnrollmentService.php
│   │   └── ...
│   ├── Jobs/                   # Async job classes
│   ├── Mail/                   # Email classes
│   ├── Notifications/          # Notification classes
│   └── Traits/                 # Reusable code blocks
├── routes/
│   ├── web.php                 # Web routes
│   ├── api.php                 # API routes
│   └── admin.php               # Admin routes
├── resources/
│   ├── views/
│   │   ├── layouts/            # Base layouts
│   │   ├── admin/              # Admin pages
│   │   ├── instructor/         # Instructor pages
│   │   ├── student/            # Student pages
│   │   └── components/         # Reusable components
│   ├── css/                    # Stylesheets
│   └── js/                     # JavaScript files
├── database/
│   ├── migrations/             # Schema changes
│   ├── seeders/                # Sample data
│   └── factories/              # Test data factories
├── tests/                      # Test files
├── config/                     # Configuration files
├── storage/                    # File uploads
└── public/                     # Web root
    ├── uploads/                # User-uploaded files
    ├── css/                    # Compiled styles
    └── js/                     # Compiled scripts
```

### Key Models & Relationships

```
User
├── hasMany Courses (as instructor)
├── hasMany Enrollments
├── hasMany CoursePayments
└── hasMany QuizSubmissions

Course
├── belongsTo User (instructor)
├── hasMany Enrollments
├── hasMany CourseContent
├── hasMany CourseQuiz
├── hasMany CourseLiveSession
└── hasMany CourseVenue

CourseEnrollment
├── belongsTo User
├── belongsTo Course
├── hasMany CourseContentCompletion
└── hasMany QuizSubmission

CourseQuiz
├── belongsTo Course
├── hasMany QuizQuestions
└── hasMany QuizSubmissions

QuizSubmission
├── belongsTo User
├── belongsTo CourseQuiz
└── hasMany QuizSubmissionAnswers

HomepageSetting
├── Stores site branding
├── Stores design settings
├── Stores homepage content
└── Organized by sections
```

---

## Database Design

### Core Tables

#### Users Table
```sql
CREATE TABLE users (
    id BIGINT PRIMARY KEY,
    name VARCHAR(255),
    email VARCHAR(255) UNIQUE,
    password VARCHAR(255),
    role ENUM('admin', 'instructor', 'student', 'facilitator'),
    is_active BOOLEAN DEFAULT true,
    email_verified_at TIMESTAMP,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

#### Courses Table
```sql
CREATE TABLE courses (
    id BIGINT PRIMARY KEY,
    instructor_id BIGINT,
    title VARCHAR(255),
    description LONGTEXT,
    category_id BIGINT,
    level ENUM('Local', 'International', 'Diploma'),
    price DECIMAL(10, 2),
    featured_image VARCHAR(255),
    is_published BOOLEAN DEFAULT false,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY(instructor_id) REFERENCES users(id),
    FOREIGN KEY(category_id) REFERENCES course_categories(id)
);
```

#### Homepage Settings Table
```sql
CREATE TABLE homepage_settings (
    id BIGINT PRIMARY KEY,
    section VARCHAR(100),     -- e.g., 'branding', 'hero', 'about', 'design'
    key VARCHAR(100),         -- e.g., 'site_name', 'logo_light', 'show_logo'
    value LONGTEXT,           -- For text content
    image_path VARCHAR(255),  -- For image paths
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    UNIQUE KEY(section, key)
);
```

#### Course Content Table
```sql
CREATE TABLE course_content (
    id BIGINT PRIMARY KEY,
    course_id BIGINT,
    title VARCHAR(255),
    description LONGTEXT,
    content_type ENUM('text', 'video', 'file', 'document'),
    content_data LONGTEXT,    -- JSON or HTML
    sequence INT,
    is_published BOOLEAN DEFAULT false,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY(course_id) REFERENCES courses(id)
);
```

#### Course Quiz Table
```sql
CREATE TABLE course_quizzes (
    id BIGINT PRIMARY KEY,
    course_id BIGINT,
    title VARCHAR(255),
    description LONGTEXT,
    time_limit_minutes INT,
    passing_score INT,
    attempts_allowed INT,
    shuffle_questions BOOLEAN DEFAULT false,
    is_published BOOLEAN DEFAULT false,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY(course_id) REFERENCES courses(id)
);
```

### Database Indexing Strategy

```
Users Table:
- Primary: id
- Index: email (for login)
- Index: role (for filtering)

Courses Table:
- Primary: id
- ForeignKey: instructor_id
- ForeignKey: category_id
- Index: is_published (for homepage display)
- Index: created_at (for sorting)

Course Content:
- Primary: id
- ForeignKey: course_id
- Composite: (course_id, sequence) for ordering

Homepage Settings:
- Primary: id
- Unique: (section, key) for fast lookup
- Index: section (for section queries)
- Index: is_active (for visibility filtering)
```

---

## API Architecture

### RESTful API Design

#### Endpoints Structure

```
Authentication:
  POST   /api/auth/login
  POST   /api/auth/register
  POST   /api/auth/logout
  POST   /api/auth/refresh

Courses:
  GET    /api/courses
  GET    /api/courses/{id}
  POST   /api/courses              (Instructor)
  PUT    /api/courses/{id}         (Instructor/Admin)
  DELETE /api/courses/{id}         (Instructor/Admin)
  GET    /api/courses/{id}/content
  GET    /api/courses/{id}/quizzes

Enrollments:
  GET    /api/enrollments          (User's enrollments)
  POST   /api/enrollments          (Enroll in course)
  GET    /api/enrollments/{id}
  PUT    /api/enrollments/{id}

Quizzes:
  GET    /api/quizzes/{id}
  GET    /api/quizzes/{id}/questions
  POST   /api/quiz-submissions     (Submit answers)
  GET    /api/quiz-submissions/{id}

Homepage:
  GET    /api/homepage/settings
  PUT    /api/homepage/settings/{section}  (Admin)
  GET    /api/homepage/sections
```

### Response Format

```json
// Success Response
{
    "success": true,
    "data": {
        "id": 1,
        "title": "Course Title",
        ...
    },
    "message": "Operation completed successfully"
}

// Error Response
{
    "success": false,
    "error": "Invalid credentials",
    "code": "INVALID_CREDENTIALS",
    "status": 401
}

// Paginated Response
{
    "success": true,
    "data": [...],
    "pagination": {
        "total": 100,
        "per_page": 15,
        "current_page": 1,
        "last_page": 7
    }
}
```

---

## Frontend Architecture

### Blade Templating

```
Base Layout (layouts/landing.blade.php)
├── Head (Meta, CSS, Title)
├── Navbar
│   ├── Logo (Flexible visibility)
│   ├── Site Name (Toggleable)
│   ├── Tagline (Toggleable)
│   └── Navigation Links
├── Main Content (@yield)
└── Footer
    ├── Site Name (Conditional)
    ├── Footer Links
    └── Copyright

Component Structure (resources/views/components/)
├── course-search-bar.blade.php
├── course-card.blade.php
├── quiz-card.blade.php
└── certificates.blade.php
```

### Dynamic Homepage System

```
Homepage Sections (Stored in Database):

1. Hero Section
   - Title, subtitle, CTA button
   - Background image
   - Text color settings

2. About Section
   - Company description
   - Mission statement
   - Key points

3. Features Section
   - Feature cards (icon, title, description)
   - 3-6 features

4. Featured Courses
   - Display specific courses
   - Course cards with CTA

5. Testimonials
   - Student reviews
   - Ratings
   - Author info

6. Stats Section
   - Key metrics (students, courses, etc.)
   - Animated counters

7. CTA Section
   - Call-to-action content
   - Primary/secondary buttons

8. Contact Section
   - Contact form
   - Contact details
   - Map integration
```

### Design Settings

```
Background:
- main_bg_color: Hex color
- main_bg_image: Image path
- main_bg_opacity: 0-100%

Navbar:
- navbar_bg_color: Gradient or solid color
- navbar_text_color: Text color
- navbar_height: Computed

Container:
- container_bg_color: Background color
- container_padding: Default Bootstrap

Branding:
- site_name: Company name (toggleable)
- site_tagline: Short description (toggleable)
- logo_light: Normal logo (toggleable)
- logo_dark: Dark-background logo (toggleable)
- logo_height: 20-200px dynamically set
- favicon: Browser tab icon
```

---

## Security Architecture

### Authentication & Authorization

```
Authentication Flow:
1. User submits credentials
2. Laravel verifies against database
3. Password hashed with bcrypt verified
4. JWT token generated
5. Token stored in session/cookie
6. Token validated on each request

Authorization (Roles & Permissions):
- Middleware: CheckRole, CheckPermission
- Policies: Course policy, Quiz policy, etc.
- Gates: Custom authorization logic
```

### Data Protection

```
Encryption:
- Password: bcrypt hashing (Laravel's default)
- API responses: HTTPS only
- Sensitive data: AES-256 encryption

CSRF Protection:
- Token in form submissions
- X-CSRF-TOKEN header for AJAX
- Verified by middleware

SQL Injection Prevention:
- Parameterized queries (Eloquent ORM)
- Input validation rules
- Escape all user input

XSS Prevention:
- HTML escaping in Blade {{ }}
- Content Security Policy headers
- Input sanitization
```

### File Upload Security

```
Upload Validation:
- File type verification
- File size limits
- MIME type checking
- Virus scanning (optional)

Storage:
- Files stored outside public_html (optional)
- Unique filenames to prevent guessing
- Access controlled via storage routes
- Proper permissions (644 for files, 755 for dirs)
```

---

## Scalability Considerations

### Horizontal Scaling

```
Load Balancing:
- Multiple application servers
- Nginx or HAProxy as load balancer
- Session stored in Redis (shared)
- Database connection pooling

Caching Strategy:
- Redis for sessions
- Redis for query caching
- Redis for rate limiting
- Page caching for static content
```

### Database Optimization

```
Performance Improvements:
- Indexed foreign keys
- Composite indexes on frequently queried combinations
- Query optimization via Eloquent
- Database replication for read-heavy operations

Partitioning Strategy:
- Partition quiz_submissions by date
- Partition course_enrollments by year
- Archive old data to separate tables
```

### Content Delivery

```
CDN Integration:
- Static assets (CSS, JS) via CDN
- Images via CDN with optimization
- Videos via video platform (Vimeo, YouTube)
- Gzip compression enabled
```

---

## Deployment Architecture

### Development Environment
```
Local Machine:
- PHP 8.2+
- MySQL 8.0
- Redis 7.0
- Node.js (for asset building)
- Laravel Valet/Homestead for local server
```

### Production Environment
```
Recommended Setup:
- Web server: Ubuntu 22.04 LTS
- Web server software: Nginx/Apache
- PHP-FPM: PHP 8.2+
- Database: MySQL 8.0 on separate server
- Cache: Redis on separate server
- Queue: Redis for job queue
- Reverse proxy: Nginx
- SSL: Let's Encrypt certificates
- Monitoring: New Relic/DataDog
- Backup: Daily incremental backups
```

### CI/CD Pipeline

```
GitHub/GitLab → Webhook Trigger
        ↓
Code Push Detected
        ↓
Run Tests (PHPUnit)
        ↓
Run Linting (PHP Stan)
        ↓
Build Assets (Vite)
        ↓
Deploy to Server
        ↓
Run Migrations
        ↓
Clear Cache
        ↓
Smoke Tests
```

### Docker Containerization

```
docker-compose.yml
├── Web Container (PHP-FPM + Nginx)
│   - Port: 80, 443
│   - Volume: /app
│
├── Database Container (MySQL)
│   - Port: 3306
│   - Volume: /var/lib/mysql
│
├── Cache Container (Redis)
│   - Port: 6379
│   - Volume: /data
│
└── Queue Worker Container
    - Processes background jobs
    - Volume: /app
```

---

## System Monitoring & Logging

### Logging Strategy

```
Application Logs:
- Location: storage/logs/
- Rotation: Daily
- Retention: 14 days
- Level: debug in dev, error in production

Database Logs:
- Slow query logging
- Binary logging for replication
- Error logging

Access Logs:
- Web server access logs
- API request logging
- User action audit trail
```

### Monitoring Metrics

```
Performance:
- Response time (target: <200ms)
- Error rate (target: <0.1%)
- Database queries per request
- Cache hit rate

Resource Usage:
- CPU utilization
- Memory usage
- Disk space
- Network bandwidth

Business Metrics:
- Active users
- Course enrollments
- Quiz completion rate
- Certificate issued
```

---

## Backup & Disaster Recovery

### Backup Strategy

```
Database:
- Full backup: Daily at 2 AM
- Incremental: Hourly
- Retention: 30 days
- Cross-region replication

Files:
- User uploads: Versioned storage
- Configuration: In version control
- Retention: Same as database
```

### Recovery Procedures

```
Database Recovery:
- RPO: 1 hour (hourly incremental)
- RTO: 2-4 hours
- Test recovery monthly

File Recovery:
- RPO: Per-version (tracked)
- RTO: 30 minutes
- Restore from cloud storage
```

---

**Document Version:** 1.0  
**Last Updated:** February 23, 2026  
**Next Review Date:** June 23, 2026
