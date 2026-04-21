# KTV POS System - Laravel Implementation

## Project Overview
This project is a Point of Sale (POS) system for a KTV (Karaoke) business, implemented using the Laravel framework. It follows the **Model-View-Controller (MVC)** architectural pattern and uses **Tailwind CSS** for styling, based on a high-fidelity Figma UI design.

### Core Technologies
- **Framework:** Laravel 12.x
- **Frontend:** Blade Templates, Tailwind CSS 3.x
- **Icons:** Lucide React (via CDN for Blade)
- **Database:** SQLite (default for development)
- **Build Tool:** Vite

### Architecture
The project structure follows standard Laravel conventions:
- **Controllers:** `app/Http/Controllers/`
  - `DashboardController.php`
  - `RoomController.php`
  - `OrderController.php`
  - `MenuController.php`
  - `ReportController.php`
  - `ProfileController.php`
- **Views:** `resources/views/`
  - `layouts/app.blade.php`: Base layout with Sidebar and Mobile Nav.
  - `partials/`: Reusable Blade components (sidebar, mobile-nav).
  - `dashboard.blade.php`: Main overview.
  - `rooms/`, `orders/`, `menu/`, `reports/`, `profile/`: Section-specific views.
- **Routes:** `routes/web.php`
- **Styling:** `resources/css/app.css` (Tailwind configuration)
- **Reference Design:** `KTV POS System UI Design/` (Original React/Figma export)

## Building and Running

### Prerequisites
- PHP 8.2+
- Composer
- Node.js & NPM

### Commands
- **Install PHP Dependencies:**
  ```bash
  composer install
  ```
- **Install JS Dependencies:**
  ```bash
  npm install
  ```
- **Run Migrations:**
  ```bash
  php artisan migrate
  ```
- **Start Development Server:**
  ```bash
  php artisan serve
  ```
- **Compile Assets (Tailwind):**
  ```bash
  npm run dev
  ```

## Development Conventions
- **MVC Pattern:** Strictly adhere to the MVC pattern. Keep controllers thin and logic within Models or Service classes where appropriate.
- **Styling:** Use Tailwind CSS utility classes. Custom themes and colors are defined in `tailwind.config.js` and `resources/css/app.css`.
- **Naming:** Follow standard Laravel naming conventions (CamelCase for Controllers/Models, kebab-case for views and routes).

## Reference Material
The original UI design files are located in the `KTV POS System UI Design/` directory. Refer to these files for layout, component structure, and CSS variables when implementing new features in Blade.
