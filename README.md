# Business Loyalty Management Portal

## 📌 Project Description
The **Business Loyalty Management Portal** is a simplified loyalty points management system built using **FilamentPHP** and the **TALL stack** (**Tailwind CSS, Alpine.js, Laravel, Livewire**). This system demonstrates the ability to work with our core technologies and efficiently manage customers, track loyalty points, and handle transactions.

---

## 🚀 Features

- **Customer Management** (CRUD operations)
- **Loyalty Points System** (Issuing and deducting points with full transaction tracking)
- **Filament Admin Panel** (For managing customers and transactions)
- **API Endpoints** (For mobile or third-party integrations)
- **Database Management** (MySQL with migrations and seeders)

---

## 🛠 Setup Instructions

### 1. Clone the Repository
```sh
git clone https://github.com/your-repository.git
cd your-repository
```

### 2. Install Dependencies
```sh
composer install
npm install && npm run build
```

### 3. Set Up Environment Variables
Copy the `.env.example` file and update your environment settings.
```sh
cp .env.example .env
```
Update the `.env` file with your **MySQL** database credentials:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 4. Generate Application Key
```sh
php artisan key:generate
```

### 5. Run Migrations and Seed Database
```sh
php artisan migrate --seed
```

### 6. Start the Development Server
```sh
php artisan serve
php artisan queue:work
```

### 7. Create API Documentation
```sh
php artisan scribe:generate
```

### 8. Create Filament Admin User
```sh
php artisan make:filament-user
```
Then log in at `/admin` using the created credentials.

---

## 🔑 Demo User

You can log in using the following demo credentials:

- **Admin Panel:** `/admin`
- **Email:** `admin@admin.com`
- **Password:** `password`

---

## 📝 Excel Import Format
To import customers, use an Excel file with the following format:

| name       | email             | phone      | total_points |
|------------|------------------|------------|--------------|
| John Doe   | john@example.com  | 1234567890 | 100          |
| Jane Smith | jane@example.com  | 0987654321 | 200          |

The file should be saved as `.xlsx` or `.csv`.

---

## 💡 Implementation Choices

- **Laravel 11**: Used for its simplicity and robust ecosystem.
- **FilamentPHP**: Provides an intuitive admin panel.
- **TALL Stack**: Enables fast, dynamic UI updates with Livewire and Alpine.js.
- **MySQL**: Preferred for its performance and scalability.

---

## 📌 Assumptions Made

- Each business manages **its own customers**.
- Only **authenticated users** can modify or view customer data.
- A customer’s **total points** cannot be negative.
- Transactions are **immutable**—modifications require new entries.

---

## ✅ Completed Features

- [x] **Customer CRUD (Create, Read, Update, Delete)**
- [x] **Point Transactions (Issue & Deduct)**
- [x] **Filament Admin Panel**
- [x] **API Authentication (Sanctum)**
- [x] **MySQL Database Setup**

---

## 🚀 Future Improvements

- **📊 Analytics & Reports**: Provide insights on customer spending and loyalty.
- **📢 Notifications**: Notify customers about transactions via email/SMS.
- **📱 Mobile App**: Implement a mobile-friendly PWA or native app using Flutter.
- **💳 Payment Integration**: Allow points to be used for discounts or purchases.
- **👥 Multi-Tenancy**: Support multiple businesses under one platform.

---

## 📂 Repository Structure

- **📝 `app/`** – Application logic and models
- **📝 `database/migrations/`** – Database migration files
- **📝 `database/seeders/`** – Seed files for testing data
- **📝 `routes/`** – API and web route definitions
- **📝 `resources/views/`** – Blade templates for the UI
- **📝 `public/`** – Public assets
- **📝 `.env.example`** – Environment variables template

---

## 🤝 Contributing
Pull requests are welcome! For major changes, please open an issue first to discuss.

---

## 📝 License
This project is **open-source** under the MIT License.

