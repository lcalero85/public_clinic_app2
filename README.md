MyClinicSystem
MyClinicSystem is a secure and scalable web-based clinic management application designed to streamline patient records, appointments, and administrative processes for healthcare providers. Built with PHP, MySQL, HTML, CSS, and JavaScript, it follows MVC architecture and applies OWASP security best practices.

Features
User Roles: Admin, Doctor, and Receptionist with role-based access.

Patient Management: Add, edit, and search patient records with detailed personal and medical information.

Appointment Scheduling: Manage patient appointments and availability.

Medical Records: Store allergies, blood type, insurance, and emergency contacts.

Responsive Design: Works on desktop, tablet, and mobile devices.

Secure Authentication: Login with CSRF protection, input validation, and hashed passwords.

Search & Filters: Quickly find patients and appointments with smart filtering.

Reports: Export patient data and appointment summaries.

Configurable Settings: Update system preferences easily.

Technology Stack
Backend: PHP 8+ (MVC architecture)

Database: MySQL 5.7+

Frontend: HTML5, CSS3 (Bootstrap), JavaScript (jQuery)

Security: OWASP best practices, bcrypt password hashing, CSRF protection

Installation
Clone or download the repository.

Import the provided database.sql file into MySQL.

Update database credentials in /config.php.

Ensure the /uploads folder is writable.

Open in browser and log in with the default admin credentials (change immediately).

Requirements
PHP 8.0+

MySQL 5.7+

Apache/Nginx server with mod_rewrite enabled

License
This project is licensed under the MIT License.
