# Hackathon Project

A comprehensive health tracking application that helps users log and monitor various health metrics such as food intake, exercise, sleep, water consumption, body metrics, blood metrics, mindfulness activities, and even menstrual cycles. The platform includes interactive dashboards, visualizations, and email reports to ensure users stay on top of their health.

---

## Installation Instructions

Follow these steps to install and run the project locally:

1. **Clone the Repository**:
   ```bash
   git clone https://github.com/Sreeju7733/hackathon.git
   cd hackathon
   ```

2. **Install Dependencies**:
   - Install PHP and MySQL on your machine.
   - Install Composer for PHP package management.
   - Run the following command to install required PHP libraries (like PHPMailer):
     ```bash
     composer install
     ```

3. **Set Up the Database**:
   - Import the `db/schema.sql` file into your MySQL database to create the necessary tables.
   - Update `db/conn.php` with your database credentials.

4. **Set Up Email Configuration**:
   - Update `send_mail.php` and `send_report.php` with your SMTP server settings and credentials.

5. **Run the Project**:
   - Start a local server (e.g., using XAMPP or PHP’s built-in server):
     ```bash
     php -S localhost:8000
     ```
   - Open your browser and navigate to `http://localhost:8000`.

---

## Usage Guide

### Logging Health Metrics
- **Food**: Log food items and their calories using `log_food.php`.
- **Exercise**: Track your workouts and calories burned in `log_exercise.php`.
- **Sleep**: Record sleep duration and quality in `log_sleep.php`.
- **Water**: Log daily water intake in `log_water.php`.
- **Mindfulness**: Log mindfulness activities like meditation in `log_mindfulness.php`.

### Viewing Logs
- Navigate to the respective `view_*_log.php` files to view detailed logs and visualizations for metrics like blood pressure, body metrics, and food consumption.

### Email Reports
- Use the `send_report.php` script to receive a detailed health report via email, including charts and insights.

### Dashboard
- The `index.php` file serves as the main dashboard, summarizing daily and weekly health metrics and trends.

---

## Screenshots

### Dashboard
![Dashboard Screenshot](https://github.com/user-attachments/assets/90f0590d-1e81-4ca7-a4ff-b7fa605d9f1e)

---

## Contributing Guidelines

We welcome contributions! Please follow these steps:

1. **Fork the Repository**: Click the "Fork" button on GitHub.
2. **Create a New Branch**:
   ```bash
   git checkout -b feature/your-feature-name
   ```
3. **Make Changes**: Add your feature or fix a bug.
4. **Run Tests**: Ensure your changes do not break existing functionality.
5. **Submit a Pull Request**: Open a pull request with a clear description of your changes.

### Code Style
- Follow PSR-12 coding standards for PHP.
- Use meaningful commit messages.

---

## Tech Stack

- **Programming Language**: PHP, JavaScript
- **Frontend**: Bootstrap, Chart.js, JQuery
- **Database**: MySQL
- **Email Service**: PHPMailer
- **Server**: Apache or PHP Built-in Server

---

## Features

1. **Comprehensive Health Tracking**:
   - Log and track food, exercise, sleep, water, mindfulness, body metrics, and blood metrics.
2. **Visualizations**:
   - Beautiful charts using Chart.js for trends and insights.
3. **Email Reports**:
   - Receive daily/weekly health summaries via email.
4. **User-Friendly Interface**:
   - Simple and intuitive design powered by Bootstrap.
5. **Secure Authentication**:
   - Passwords are hashed for secure storage.
6. **Notification System**:
   - Daily notification system.

---

## License
Copyright 2025 Sreeju S

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

[http://www.apache.org/licenses/LICENSE-2.0](http://www.apache.org/licenses/LICENSE-2.0)

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
