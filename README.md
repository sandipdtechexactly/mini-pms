# Mini Project Management System (PMS)

[![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/)

A lightweight and efficient Project Management System built with Laravel, designed to help teams manage projects, tasks, and team collaboration effectively.

## Features

- **Project Management**: Create and manage multiple projects
- **Task Tracking**: Assign and track tasks with different statuses
- **User Roles**: Role-based access control (Admin, Manager, Team Member)
- **Responsive Design**: Works on desktop and mobile devices
- **Task Comments**: Collaborate with team members through task comments
- **File Attachments**: Share files related to tasks and projects

## Prerequisites

- PHP >= 8.1
- Composer
- MySQL >= 5.7
- Node.js & NPM

## Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/sandipdtechexactly/mini-pms.git
   cd mini-pms
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install JavaScript dependencies**
   ```bash
   npm install
   ```

4. **Create and configure .env file**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure database**
   Update your `.env` file with your database credentials:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=mini_pms
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

6. **Run migrations and seeders**
   ```bash
   php artisan migrate --seed
   ```

7. **Generate application key**
   ```bash
   php artisan key:generate
   ```

8. **Start the development server**
   ```bash
   php artisan serve
   ```

9. **Compile assets**
   ```bash
   npm run dev
   ```

10. **Access the application**
    Open your browser and visit: `http://localhost:8000`

## Default Login Credentials

- **Admin**
  - Email: admin@example.com
  - Password: password

- **Manager**
  - Email: manager1@example.com
  - Password: password

- **Team Member**
  - Email: developer1@example.com
  - Password: password


## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This project is open-source and available under the [MIT License](LICENSE).

## Contact

For any inquiries, please reach out to [sandipd.techexactly@gmail.com](mailto:sandipd.techexactly@gmail.com)
