# RentFlow - Rental Management System

RentFlow is a web-based application designed to streamline the management of rental properties. It provides a centralized platform for property owners, tenants, and administrators to manage and track rental-related activities.

## Features

*   **Centralized Dashboard:** Manage all properties, tenants, and financials from a single, powerful interface.
*   **Automated Billing:** Generate invoices automatically and accept online payments with ease.
*   **Maintenance Hub:** Track payments, assign notices, set rules, and keep everything running smoothly.

## Login Portals

The system has three distinct user portals:

*   **Admin Portal:** For overall management of the system, including managing owners, service plans, and buildings.
*   **Owner Portal:** For property owners to manage their properties, tenants, and payments.
*   **Tenant Portal:** For tenants to view their payment history, pay rent, and view notices.

## Getting Started

To get started with RentFlow, follow these steps:

1.  **Database Setup:**
    *   Import the `rentalmanagement.sql` file into your MySQL database.

2.  **Configuration:**
    *   Open the `config.php` file.
    *   Update the following database connection details:
        ```php
        define('DB_HOST', 'your_database_host');
        define('DB_USER', 'your_database_user');
        define('DB_PASS', 'your_database_password');
        define('DB_NAME', 'rentalmanagement');
        ```

3.  **Web Server:**
    *   Host the project files on a web server with PHP support (e.g., XAMPP, WAMP).

4.  **Access the Application:**
    *   Open your web browser and navigate to the project's URL.

## Technologies Used

*   **Backend:** PHP
*   **Frontend:** HTML, CSS, JavaScript, Bootstrap
*   **Database:** MySQL

## Database Schema

The database schema consists of the following tables:

*   `admin`: Stores administrator login credentials.
*   `buildings`: Stores information about the buildings.
*   `building_owners`: Stores information about the building owners.
*   `floors`: Stores information about the floors in each building.
*   `notices`: Stores notices for the tenants.
*   `owner_payments`: Stores payment information for the building owners.
*   `payments`: Stores payment information for the tenants.
*   `rules`: Stores the rules for each building.
*   `service_plans`: Stores the available service plans for the building owners.
*   `tenants`: Stores information about the tenants.

## Author

*   **MD ANIK BISWAS** - [LinkedIn](https://www.linkedin.com/in/mdanikbiswas/)

&copy; 2025 RentFlow. All Rights Reserved.
