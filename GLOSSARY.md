# Project Glossary

## Database Tables

### users
- id (int, PK, auto-increment)
- email (varchar(100), unique)
- password_hash (varchar(255))
- role (enum: 'admin', 'user')
- created_at (timestamp)

### categories
- id (int, PK, auto-increment)
- name (varchar(50))
- slug (varchar(50), unique)

### products
- id (int, PK, auto-increment)
- name (varchar(100))
- description (text)
- price (decimal(10,2))
- category_id (int, FK → categories.id, ON DELETE SET NULL)
- image_path (varchar(255))
- created_at (timestamp)

### files
- id (int, PK, auto-increment)
- original_name (varchar(255))
- stored_name (varchar(255))
- mime_type (varchar(100))
- size (int)
- user_id (int, FK → users.id, ON DELETE CASCADE)
- created_at (timestamp)

## URL Convention

- Route format: `index.php?route=controller/action/id`
- Example: `index.php?route=products/show/5`