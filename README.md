# 🚀 MiniStack Cloud Platform

MiniStack is a custom cloud platform simulation built with Laravel 11. It provides a modern, intuitive dashboard for managing Object Storage, Compute Instances (VMs), and Managed Databases, complete with dynamic billing and subscription limits.

## 🌟 Key Features

### 1. 🗄️ Object Storage (MiniStack Storage Mock Integration)

- Create, manage, and delete storage buckets.
- Upload and delete files up to 50MB.
- Seamlessly integrates with MiniStack Storage Service via API to simulate real AWS S3 functionality.
- Tracks exact storage usage (GB) and object counts.

### 2. ⚙️ Compute Instances (VM Simulation)

- Launch virtual machines with various instance types (Nano, Micro, Small, Medium, Large, XLarge).
- Dynamic resource validation: prevents launching instances that exceed your active subscription limits (vCPU and RAM).
- Simulates IP allocation and instance status tracking.

### 3. 🐬 Managed Databases (DBaaS Simulation)

- Provision managed databases (MySQL, PostgreSQL, MariaDB, Redis).
- Storage limit validation: instance sizes scale appropriately to respect your overall subscription storage limit.
- Automatic resource reservation out of your total compute limits.

### 4. 💳 Dynamic Billing & Subscriptions

- 3-Tier Bundle Subscription System (Free, Pro, Business).
- Live upgrades: Upgrading your storage plan simultaneously scales up your compute limits (Bundled scaling).
- Zero hardcoded limits: The UI, dashboard cards, and validation logic dynamically read from a single source of truth in the Models.

## 🛠️ Tech Stack & Architecture

- **Framework**: Laravel 11 (PHP 8.x)
- **Frontend**: Blade Templates, Tailwind CSS
- **Database**: MySQL (running via Laragon)
- **Storage Backend**: MiniStack Storage Service (HTTP Mock Server on port 4566)
- **Environment**: Laragon (Windows) / Localhost

## 📋 System Requirements

To run this project locally, you will need:

- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL
- Laragon (Recommended for Windows)

## 🚀 Installation Guide

Follow these steps to set up MiniStack on your local machine:

1. **Clone the repository**

    ```bash
    git clone https://github.com/MadeByBintang/AWS-CLOUD
    cd AWS-CLOUD
    ```

2. **Install PHP Dependencies**

    ```bash
    composer install
    ```

3. **Install NPM Dependencies & Compile Assets**

    ```bash
    npm install
    npm run build
    ```

4. **Environment Setup**

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

    _Edit the `.env` file to configure your MySQL database credentials._

5. **Run Database Migrations & Seeders**

    ```bash
    php artisan migrate --seed
    ```

6. **Start MiniStack Storage Service (Storage Backend)**
   Install and run the MiniStack Storage Service via python/pip or Docker:
   ```bash
   pip install ministack
   docker run -p 4566:4566 ministackorg/ministack
   ```

7. **Serve the Application**
   Using Laragon, the site will be available at `http://ministack.test`. Alternatively, run:
    ```bash
    php artisan serve
    npm run dev
    ```

## 🧪 Testing Methods

This application supports extensive manual testing (Black-box testing) for quality assurance:

- **Resource Validation**: Try creating a `Large` database on a `Free` plan to verify the backend successfully rejects the request based on Storage/vCPU constraints.
- **Subscription Sync**: Upgrade your plan from the Billing menu and observe how the Dashboard quotas immediately reflect the new tiers across all services.
- **Security Validation**: Attempt to bypass HTML disabled states; the backend controllers will still perform strict Path Testing (White-box) to enforce plan limits.

## 📝 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
