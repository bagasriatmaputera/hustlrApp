# Hustlr 🚀

> **A modern, reactive Side Hustle and Project Management Platform built with Laravel 13, Livewire 4, and Flux UI.**

[![Laravel](https://img.shields.io/badge/Laravel-13.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3%20|%208.5-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Livewire](https://img.shields.io/badge/Livewire-4.x-FB70A9?style=for-the-badge&logo=livewire&logoColor=white)](https://livewire.laravel.com)
[![Flux UI](https://img.shields.io/badge/Flux%20UI-2.x-6366F1?style=for-the-badge)](https://fluxui.dev)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind%20CSS-v4-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)](LICENSE)

---

## 📖 Table of Contents

- [About The Project](#-about-the-project)
- [Key Features](#-key-features)
- [Tech Stack](#-tech-stack)
- [Database & Architecture](#-database--architecture)
- [Getting Started](#-getting-started)
  - [Prerequisites](#prerequisites)
  - [Installation Steps](#installation-steps)
  - [Default Credentials](#default-credentials)
- [Available Scripts](#-available-scripts)
- [Directory Structure](#-directory-structure)
- [Roles & Permissions (RBAC)](#-roles--permissions-rbac)
- [Contributing](#-contributing)
- [License](#-license)

---

## 🌟 About The Project

**Hustlr** (Manage Side Hustle - MSH) is an all-in-one management platform designed for freelancers, indie hackers, and engineering teams to track concurrent side projects, client engagements, and internal tools.

It eliminates context-switching and scattered project documentation by providing a centralized hub for project deliverables, deployment links (staging & production), technology stacks, team assignments, client details, and activity logs—all packaged in a sleek, reactive, dark-mode ready interface.

---

## ⚡ Key Features

- **📊 Comprehensive Project Management**
  - Track projects across their full lifecycle: `Planning`, `In Progress`, `On Hold`, `Completed`, and `Cancelled`.
  - Classify deliverables by ownership model: `Client`, `Internal`, or `Freelance`.
  - Quick-access deployment links for staging/testing and production environments.
  - Timeline management with start and estimated completion dates.

- **🛠️ Technology Stack Tagging**
  - Catalog and associate projects with technologies categorized by domain (`Frontend`, `Backend`, `Database`, `DevOps`, `Mobile`).

- **🏢 Client Directory**
  - Maintain client contacts, organizations, emails, and address books directly tied to active projects.

- **👥 Team Collaboration & Assignment**
  - Assign team members to specific projects with distinct project-level roles and join dates.

- **🔐 Next-Gen Authentication & Security**
  - **Passkeys (WebAuthn)**: Passwordless biometric login supported out of the box via `@laravel/passkeys`.
  - **Two-Factor Authentication (2FA)**: TOTP authentication with recovery codes powered by Laravel Fortify.
  - **Password Confirmation**: Extra protection layer for sensitive security and profile actions.

- **🛡️ Role-Based Access Control (RBAC)**
  - Granular permissions powered by `spatie/laravel-permission` with an extensible `PermissionManager` pattern (`Owner`, `Management`, `Staff`).

- **🎨 Modern Reactive Interface**
  - Single-page application feel with server-side simplicity using **Livewire 4** and **Livewire Flux UI**.
  - Built with **Tailwind CSS v4** featuring native dark/light mode toggles and responsive layouts.

---

## 🛠️ Tech Stack

| Layer | Technology | Description |
| :--- | :--- | :--- |
| **Framework** | [Laravel 13.x](https://laravel.com/) | Robust backend framework with PHP 8.3+ support |
| **Reactivity** | [Livewire 4.x](https://livewire.laravel.com/) | Dynamic, reactive UI without leaving PHP |
| **UI Components** | [Flux UI 2.x](https://fluxui.dev/) | Official, accessible Livewire component library |
| **Styling** | [Tailwind CSS v4](https://tailwindcss.com/) | Next-generation utility-first CSS framework |
| **Bundler** | [Vite 8.x](https://vitejs.dev/) | Ultra-fast frontend development server & build tool |
| **Authentication** | [Laravel Fortify](https://laravel.com/docs/fortify) | Headless auth with 2FA & Passkey integration |
| **Authorization** | [Spatie Permission 8.x](https://spatie.be/docs/laravel-permission/) | Role and permission management |
| **Code Quality** | [Pint](https://laravel.com/docs/pint) & [Larastan](https://github.com/larastan/larastan) | Code styling and static analysis (PHPStan Level max) |
| **Testing** | [PHPUnit 12.x](https://phpunit.de/) | Automated feature and unit testing suite |

---

## 🗄️ Database & Architecture

Hustlr uses UUID primary keys for all domain models to ensure distributed scalability and obfuscate consecutive database IDs.

### Core Entity Relationships

```
┌──────────────┐         1:N         ┌───────────────────┐
│   Clients    │ ──────────────────< │     Projects      │
└──────────────┘                     └───────────────────┘
                                       │   │   │   │
                  ┌────────────────────┘   │   │   └────────────────────┐
                  │ 1:N                    │   │ N:M (with User)        │ 1:N
                  ▼                        │   ▼                        ▼
        ┌───────────────────┐              │ ┌───────────────────┐    ┌────────────────────┐
        │ ProjectDocuments  │              │ │  ProjectMembers   │    │ ProjectActivityLog │
        └───────────────────┘              │ └───────────────────┘    └────────────────────┘
                                           │
                                           │ N:M (via ProjectTechnology)
                                           ▼
                                 ┌───────────────────┐
                                 │   Technologies    │
                                 └───────────────────┘
```

- **`projects`**: Core entity containing metadata, ownership type, status, and URL links.
- **`clients`**: Organizations or individual clients funding or receiving deliverables.
- **`project_statuses`**: Workflow statuses (`Planning`, `In Progress`, `On Hold`, `Completed`, `Cancelled`).
- **`technologies`**: System-wide technology catalog grouped by category.
- **`project_members`**: Pivot tracking assigned users, roles, and join dates.
- **`project_documents`**: File attachments and documentation references per project.
- **`project_activity_logs`**: Audit trail for project events and changes.

---

## 🚀 Getting Started

Follow the instructions below to set up Hustlr locally on your development machine.

### Prerequisites

Ensure you have the following installed:

- **PHP**: `^8.3` or `^8.5` (with `bcmath`, `curl`, `mbstring`, `openssl`, `pdo_mysql` or `pdo_sqlite`, `xml`, `zip`)
- **Composer**: `^2.x`
- **Node.js**: `^20.x` or `^22.x` & **npm**
- **Database**: MySQL `^8.0` or SQLite `^3.35`

---

### Installation Steps

1. **Clone the repository**
   ```bash
   git clone <repository-url> hustlr
   cd hustlr
   ```

2. **Automated Setup (Recommended)**
   The project includes a streamlined setup script:
   ```bash
   composer run setup
   ```
   *This command installs Composer packages, copies `.env.example` to `.env`, generates the application key, runs migrations, installs npm packages, and compiles frontend assets.*

   ---

   **Or Manual Setup:**
   ```bash
   # 1. Install PHP dependencies
   composer install

   # 2. Configure environment file
   cp .env.example .env

   # 3. Generate application encryption key
   php artisan key:generate

   # 4. Configure database in .env (MySQL or SQLite)
   # e.g., DB_CONNECTION=mysql, DB_DATABASE=hustlr, etc.

   # 5. Run database migrations and seeders
   php artisan migrate --seed

   # 6. Install frontend dependencies and build
   npm install
   npm run build
   ```

3. **Start the Development Server**
   Run the all-in-one development process:
   ```bash
   composer run dev
   ```
   *Or execute via Artisan directly:*
   ```bash
   php artisan dev
   ```
   The application will be accessible at: **`http://localhost:8000`**

---

### Default Credentials

After executing `php artisan migrate --seed`, use the default seeded account:

| Attribute | Value |
| :--- | :--- |
| **Role** | Owner (`Integrated Personal System`) |
| **Email** | `owner@gmail.com` |
| **Password** | `password` |

> [!TIP]
> Make sure to update your credentials and configure Two-Factor Authentication (2FA) in **Settings > Security** after logging in.

---

## 💻 Available Scripts

This project includes pre-configured Composer scripts for daily development workflows:

| Command | Action |
| :--- | :--- |
| `composer run setup` | First-time project setup (install, key, migrate, npm) |
| `composer run dev` | Starts the concurrent development server |
| `composer run lint` | Fixes PHP code formatting with **Laravel Pint** |
| `composer run lint:check` | Checks PHP formatting without modifying files |
| `composer run types:check` | Runs static type analysis with **Larastan / PHPStan** |
| `composer run test` | Clears config, checks lint, checks types, and runs tests |

---

## 📁 Directory Structure

```text
hustlr/
├── app/
│   ├── Actions/Fortify/          # Fortify user & authentication actions
│   ├── Livewire/                 # Reactive Livewire components
│   │   ├── Projects/             # Project Index, Create, and Edit components
│   │   └── Settings/             # Profile, Appearance, and Security components
│   ├── Models/                   # Eloquent models (Project, Client, Tech, User, etc.)
│   └── PermissionManager/        # RBAC role & permission definitions
├── config/                       # Application configuration
├── database/
│   ├── factories/                # Eloquent model factories
│   ├── migrations/               # Database schema migrations
│   └── seeders/                  # Seeders for roles, statuses, and initial data
├── resources/
│   ├── css/                      # Tailwind CSS source files
│   ├── js/                       # Vite & JavaScript assets
│   └── views/                    # Blade templates & Flux components
│       ├── layouts/              # App & Auth master layouts
│       └── livewire/             # Livewire Blade component views
├── routes/
│   ├── web.php                   # Core application routes
│   ├── projects.php              # Project management routes
│   └── settings.php              # User profile & security routes
└── tests/
    ├── Feature/                  # Feature and integration tests
    └── Unit/                     # Unit tests
```

---

## 🛡️ Roles & Permissions (RBAC)

The application implements a structured Permission Manager architecture dividing responsibilities across three standard roles:

| Module / Scope | Owner | Management | Staff |
| :--- | :---: | :---: | :---: |
| **Dashboard Access** | ✅ | ✅ | ✅ |
| **View Projects** | ✅ | ✅ | ✅ |
| **Create / Edit Projects** | ✅ | ✅ | ❌ |
| **Delete Projects** | ✅ | ❌ | ❌ |
| **Task Management** | ✅ Full | ✅ Full | 👁️ View / Update assigned |
| **Team Management** | ✅ Invite & Remove | ✅ Invite | ❌ |
| **Global Settings** | ✅ | ❌ | ❌ |

---

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Format your code: `composer run lint`
4. Run static analysis: `composer run types:check`
5. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
6. Push to the branch (`git push origin feature/AmazingFeature`)
7. Open a Pull Request

---

## 📄 License

Distributed under the **MIT License**. See `LICENSE` for more information.

---

<p align="center">
  Built with ❤️ by <a href="https://github.com/bagasriatmaputera">Bagas Riatma Putera</a>
</p>
