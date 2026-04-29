# Custom PHP MVC Application

A secure, custom-built PHP Model-View-Controller (MVC) system developed without external frameworks. This architecture emphasizes strict OOP principles, deterministic database boundaries via isolated DAOs, and rigorous native security integrations including CSRF tokens, strict sessions, and global exception handling.

## 🚀 Features
- **Custom MVC Architecture:** Front controller paradigm routing through `public/index.php`.
- **Complete CRUD Operations:** Fully modeled DAOs supporting relationships between Users, Categories, and Products.
- **Session-Based Authentication:** Robust user authentication mitigating Session Hijacking via `HttpOnly` and `SameSite` enforcements.
- **Secure File Management:** Dedicated Uploader utility and File Manager validating MIME types, extensions, size constraints, and isolating physical disk limits from the SQL layer.
- **Dynamic Search Engine:** Aggregation service utilizing prepared statements for multi-table filtering, pagination, and multi-criteria searches.

## 📚 Documentation & Reports
- **[View the AI Usage Report](AI_USAGE_REPORT.md)**: A detailed chronological log of AI assistance used for debugging, redesigning, and schema generation.

## 🎨 Demos & UI
The application features a fully modern, premium design system built with custom CSS:
- **Glassmorphism Interface:** Translucent panels and soft shadows.
- **Dynamic Interactions:** Hover animations and smooth transitions on all actionable elements.
- **Responsive Layout:** Clean, structured grids for the Product lists and File Manager.

*(Screenshots can be placed here to demonstrate the Login, Dashboard, and Advanced Search modules)*

## 🛠️ System Requirements
- **PHP:** 8.0 or higher
- **Database:** MySQL / MariaDB (8.0+)
- **Web Server:** Apache (with `mod_rewrite` enabled) or Nginx
- **Extensions:**
  - `pdo` and `pdo_mysql`
  - `fileinfo` (for MIME-type validation)

## 📦 Installation & Setup

1. **Clone the Repository**
   ```bash
   git clone https://github.com/antigravity-team/php-project.git
   cd php-project
   ```

2. **Configure the Database**
   - Create a fresh MySQL database (e.g., `php_mvc_db`).
   - Copy the configuration example setup to active configuration:
     ```bash
     cp config/db_config.example.php config/db_config.php
     ```
   - Edit `config/db_config.php` and insert your database credentials:
     ```php
     return [
         'host'     => '127.0.0.1',
         'dbname'   => 'php_mvc_db',
         'username' => 'root',
         'password' => ''
     ];
     ```

3. **Run Database Initialization**
   - Import the provided schema files located in the `sql/` directory to generate your tables in sequence directly into your database.
   - Example command: `mysql -u root -p php_mvc_db < sql/schema.sql`

4. **Configure your Server Document Root**
   - Point your web server's Document Root directly to the `public/` directory inside this repository. 
   - **Crucial:** Accessing the system from the parent folder circumvents the security logic. The `.htaccess` strictly pipes all traffic via `public/index.php`.
   - *Local Example URL*: `http://localhost/php-project/public/`

## 🔑 Default Credentials
Upon importing the required schema files, the system grants the following default access profile for administrative actions:

- **Email:** `admin@example.com`
- **Password:** `admin123`

## 📂 Folder Structure
```text
php-project/
├── config/             # Database and core environment configurations
├── controllers/        # Class routing controllers isolating logic
├── models/
│   ├── daos/           # Database Access Objects (PDO injection-safe boundaries)
│   └── entities/       # Strict OOP representations of database rows
├── public/             # 🌐 Web Document Root (DO NOT EXPOSE parent dirs)
│   ├── index.php       # Front controller / System entry point
│   ├── .htaccess       # Routing intercept rules
│   └── uploads/        # System storage mapped out of DB (Requires 0755 writes)
├── services/           # Aggregated module protocols crossing DAO boundaries 
├── sql/                # System structure schema initializations
├── tests/              # CLI integration and unit isolation tests
├── utils/              # Global application utilities (Auth, Uploader)
└── views/              # Front-end HTML presentation logic and layouts
```
