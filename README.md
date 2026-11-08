# 🌸 BookMyBeauty — Salon & Beautician Booking Platform

[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Google Gemini](https://img.shields.io/badge/Gemini%20AI-Integrated-4285F4?style=for-the-badge&logo=google&logoColor=white)](https://aistudio.google.com)
[![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)

**BookMyBeauty** is a modern, full-featured web platform that connects clients with verified beauty professionals and salon services. Featuring **Bella**, an integrated AI Salon Assistant powered by Google Gemini, BookMyBeauty makes discovering beauticians, scheduling appointments, and managing beauty routines seamless and effortless.

---

## ✨ Key Features

### 🤖 1. Bella — Smart AI Salon Assistant (Google Gemini)
* **24/7 Intelligent Consultation:** Answers beauty questions, suggests treatments, and provides pricing details.
* **Multimodal Support:** Supports text inquiries, voice/audio notes, and image analysis (haircut, skincare, nail art advice).
* **Multi-Language Support:** Responds fluently in English, Urdu, and Roman Urdu.

### 💄 2. Customer Experience
* **Search & Filter:** Find top-rated beauticians by city, location, service category, and price range.
* **Online Booking System:** Real-time appointment scheduling with date and time slot selection.
* **Ratings & Reviews:** Leave verified feedback and photo reviews after appointments.
* **Favorites & History:** Save favorite salons and track upcoming and past bookings.

### ✂️ 3. Beautician Portal
* **Custom Profile & Portfolio:** Showcase work with high-resolution image galleries and bio.
* **Service & Pricing Catalog:** Add, update, and manage beauty packages and pricing.
* **Availability Management:** Set weekly working hours, blackout dates, and slot timings.
* **Booking Dashboard:** Accept, reschedule, or cancel bookings in real-time.

### 🛡️ 4. Administrator Control Panel
* **Comprehensive Analytics:** Total bookings, revenue insights, active users, and platform trends.
* **User Management:** Oversee client and beautician accounts (activate/deactivate profiles).
* **Quality & Content Moderation:** Ensure platform integrity and service quality.

---

## 🛠️ Tech Stack

* **Backend:** PHP 8.2+, Laravel 11
* **Frontend:** Blade Templating, Responsive CSS3, JavaScript (AJAX / Fetch API)
* **AI Engine:** Google Gemini API (`gemini-flash-lite`, `gemini-flash`)
* **Database:** MySQL
* **Authentication:** Role-Based Access Control (Admin, Beautician, Customer)

---

## 🚀 Getting Started

Follow these steps to run BookMyBeauty locally on your machine:

### 1. Clone the Repository
```bash
git clone https://github.com/Saniya378/book-my-beauty.git
cd book-my-beauty
```

### 2. Install Dependencies
```bash
composer install
npm install && npm run build
```

### 3. Environment Configuration
Create your `.env` file from the example:
```bash
cp .env.example .env
```
Open `.env` and set your database and API credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=book_my_beauty
DB_USERNAME=root
DB_PASSWORD=

# Google Gemini API Key for Bella AI Chatbot
GEMINI_API_KEY=your_gemini_api_key_here
```

### 4. Generate Application Key & Migrate Database
```bash
php artisan key:generate
php artisan migrate --seed
```

### 5. Start the Development Server
```bash
php artisan serve
```
Visit `http://127.0.0.1:8000` in your web browser.

---

## 🔒 Security & Privacy
* Sensitive credentials (passwords, SMTP details, API keys) are strictly managed via environment variables (`.env`) and are ignored from version control.
* Role-based middleware ensures secure route protection across customer, beautician, and admin panels.

---

## 👩‍💻 Author
* **Saniya Zahra** — [GitHub Profile](https://github.com/Saniya378)

---

## 📄 License
This project was developed as a Final Year Project (FYP). Distributed under the MIT License.
