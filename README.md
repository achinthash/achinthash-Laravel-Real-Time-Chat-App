

## Laravel WebSocket Chat App with Vue.js

A real-time chat application built with **Laravel WebSockets** and **Vue.js**. This project demonstrates real-time messaging using Laravel WebSockets, Echo, and Vue.js, allowing users to send and receive messages instantly.

---

### Features

-  Real-time messaging using **Laravel WebSockets**
-  Vue.js for the frontend with a clean, responsive UI
-  **Private & Group Chats** support
-  **Online/Offline status** updates
-  **Read receipts** to track message delivery
-  **File & Image Sharing**
-  **Authentication & User Management**


---

### Tech Stack
- **Backend:** Laravel 10, WebSockets
- **Frontend:** Vue.js, Tailwind CSS
- **Database:** MySQL
- **WebSocket Server:** Laravel WebSockets

---

##  Installation Guide

### 1. Clone the Repository
```sh
git clone https://github.com/achinthash/Laravel-Real-Time-Chat-App.git
cd laravel-websocket-chat
```

### 2. Install Dependencies
```sh
composer install
npm install
```

### 3. Set Up Environment Variables
Copy the `.env.example` file and rename it to `.env`:
```sh
cp .env.example .env
```

### 3. Generate Application Key
```sh
php artisan key:generate
```

Then update the following values in `.env`:
```ini

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password

# WebSockets
PUSHER_APP_ID=your_pusher_app_id
PUSHER_APP_KEY=your_pusher_app_key
PUSHER_APP_SECRET=your_pusher_app_secret
PUSHER_APP_CLUSTER=mt1
```

### 4. Start Laravel WebSockets Server

```sh
php artisan websockets:serve
```

### 5 Start Laravel Development Server
```sh
php artisan serve
```

### 6 Start Vue.js Development Server
```sh
npm run dev
```




---

##  Screenshots

<img src="https://github.com/achinthash/Laravel-Real-Time-Chat-App/blob/5821c68f1bed0a71ea5675fb58950e9e131522ea/storage/app/public/Chat-app-1%20.png" width="400" alt="Laravel Logo">

<img src="https://github.com/achinthash/Laravel-Real-Time-Chat-App/blob/5821c68f1bed0a71ea5675fb58950e9e131522ea/storage/app/public/Chat-app-2.png" width="400" alt="Laravel Logo">

<img src="https://github.com/achinthash/Laravel-Real-Time-Chat-App/blob/40ce033a1de813c356f753ca794eaeaf9d9d37f2/storage/app/public/Chat-app-3.png " width="400" alt="Laravel Logo">



---



