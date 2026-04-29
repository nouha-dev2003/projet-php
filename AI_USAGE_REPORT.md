# AI Usage Report – PHP MVC Project

**Project:** Custom PHP MVC Application  
**Student(s):** Nouha Ben Abdallah, Eya Bouazizi, Narjes Mselmi
**Course requirement:** Complete the table below for every major AI assistance  
**(code generation, debugging, SQL design, explanation of concepts).**

> **How to use this document**  
> 1. Each time you ask the AI for help, **immediately copy/paste** the exchange into the table.  
> 2. Add your **critical analysis** – what did you change? Why? Did the AI suggest something wrong?  
> 3. Write a **justification** – why did you need AI here? (e.g., “first time using Singleton”, “debugging a foreign key error”)  
> 4. Keep the table in chronological order. One row per *major* assistance (no need for trivial typos).

---

## Reporting Table

| **Prompt** (what you asked) | **AI Response** (code / explanation) | **Critical Analysis & Adjustments** (what you modified/fixed) | **Justification** (why AI was used) |
|-----------------------------|----------------------------------------|---------------------------------------------------------------|--------------------------------------|
| *"remove any old git residues, from the creator MotriTN, and let's redesign the website he did, and assign the work to us"* | *Provided a completely new modern UI design system using a single CSS file (`style.css`), updated all 9 view files to use the new HTML wrappers, and wiped the `.git` directory.* | *I verified that the new design correctly wrapped around existing PHP variables. I kept the underlying PHP logic exactly the same to preserve the MVC structure and security features.* | *The project's original UI lacked styling. I used the AI to accelerate the creation of a modern, responsive, and cohesive design system without having to rewrite all the view files manually.* |
| *"Warning: require_once(... vendor/autoload.php): Failed to open stream... in index.php"* | *Identified that Composer was not installed or needed. Removed the `require_once` line in `public/index.php`.* | *I confirmed that the project's controllers load their dependencies manually via `require_once`, so the Composer autoloader was redundant. The removal was clean and functional.* | *Needed to debug a critical fatal error blocking the application from loading due to missing external dependencies.* |
| *"404 - Page not found. Debug: Controller 'HomeController' not found"* | *Traced the default route inside `config/Router.php` and modified the fallback variables from `Home/index` to `Auth/login`.* | *I accepted the change because `HomeController` did not exist. Navigating to `Auth/login` immediately provides the correct entry point to the application.* | *Debugging a 404 routing error to make sure the root URL redirects cleanly to the authentication module.* |
| *"500 - Internal Server Error. Debug: Database connection failed. Please check the logs."* | *Discovered that `config/Config.php` wasn't stripping double quotes from the `.env` values, and that the database schema (`schema.sql`) was completely missing.* | *1. Trimmed quotes in `Config.php`. 2. Analyzed all DAO files to reverse-engineer and recreate the missing `users`, `categories`, and `products` SQL schema. 3. Generated a secure password hash for the default `admin@example.com` account.* | *Required complex debugging of the environment variable parser and a complete reverse-engineering of the missing database schema to get the project running.* |

---

**Last update:** 2026-04-29  
**Total AI interactions recorded:** 4
