# CodeIgniter 3 Assignment - Dealer/Employee Management System

This is a CodeIgniter 3 based application for managing dealers and employees.

## Features
- **Authentication**: Registration and Login with AJAX.
- **User Roles**: Separate dashboards for Employees and Dealers.
- **Dealer Profile**: Automatic first-login redirect to complete profile (City, State, Zip).
- **Employee Dashboard**: View list of dealers with server-side pagination and Zip code filtering.
- **Editing**: Employees can edit any dealer's location; Dealers can edit their own profile.
- **Validation**: Strict JS and PHP validation (Numeric/Length rules for Zip, Domain restrictions for emails).
- **AJAX Driven**: All forms and email checks are performed via AJAX using `.html` views.

## Requirements
- XAMPP / WAMP / MAMP (PHP 7.4 or 8.x)
- MySQL Database

## Setup Instructions
1. Clone the repository to your `htdocs` folder.
2. Import the `database.sql` file into your MySQL database named `ci_assignment`.
3. Configure `application/config/database.php` if your credentials differ (default is `root` with no password).
4. Access the project at `http://localhost/codigniter%203/index.php/`.

## Credentials
- **Employee**: `sushil@yopmail.com` / `password123`
