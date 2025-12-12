# Laravel Student Management System - Step-by-Step Guide

## 0. Prerequisites & Terminal Setup

### Prerequisites

Before starting, ensure you have the following installed and running:

1.  **XAMPP**: Start ONLY the **Apache** module from the XAMPP Control Panel (This is your PHP server).
2.  **MySQL Server & Workbench**: You have a separate MySQL installation. Open MySQL Workbench to manage your database. **Do NOT** start MySQL in XAMPP.
3.  **Node.js**: Required for installing frontend dependencies (npm).
4.  **Composer**: Required for managing PHP dependencies.

### Terminal Setup

You will need **3 separate terminal windows** inside **VS Code** for this project.
To open a new terminal in VS Code, go to **Terminal > New Terminal** or click the `+` icon in the terminal panel.

1.  **VS Code Terminal 1 (Server)**: Used ONLY to run the Laravel development server (`php artisan serve`). Keep this running constantly.
2.  **VS Code Terminal 2 (Assets)**: Used ONLY to compile frontend assets (`npm run dev`). Keep this running constantly.
3.  **VS Code Terminal 3 (Commands)**: Used for executing all other commands (creating controllers, migrations, models, running composer, etc.).

## 1. Project Setup

First, navigate to your XAMPP htdocs directory and create a new Laravel project:

```bash
cd C:\xampp\htdocs
composer create-project laravel/laravel student-management
cd student-management
```

**Note:** Make sure **XAMPP's Apache server** is running. We are using XAMPP for PHP/Apache, but your database is running on a **separate MySQL installation** (managed via Workbench).

## 2. Install Bootstrap via NPM

Install Node.js dependencies and Bootstrap:

```bash
npm install
npm install bootstrap
```

Configure Vite to include Bootstrap. Open `resources/js/app.js` and add:

```javascript
import "./bootstrap";
import "bootstrap/dist/css/bootstrap.min.css";
import "bootstrap/dist/js/bootstrap.bundle.min.js";
```

Open `resources/css/app.css` and add:

```css
@import "bootstrap/dist/css/bootstrap.min.css";
```

Build the assets:

```bash
# Run this in VS Code Terminal 2 (Assets)
npm run dev
```

For production, use:

```bash
npm run build
```

**Important:** Keep `npm run dev` running in **VS Code Terminal 2** while developing to watch for changes.

## 3. Database Configuration

Open `.env` file in your project root and configure your database:

```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
# REPLACE 'database_name' with your actual database name (e.g., student_db)
DB_DATABASE=database_name
# REPLACE 'root' with your MySQL username (usually 'root')
DB_USERNAME=root
# REPLACE 'your_password' with your actual MySQL Workbench password
DB_PASSWORD=your_password
```

**Note:** The database `database_name` should already be created. If not, create it in MySQL Workbench:

```sql
CREATE DATABASE database_name;
```

## 4. Create Migrations

**Step 4.1: System Tables**
First, create the necessary system tables for sessions, cache, and queues (required if using `database` driver):

```bash
php artisan session:table
php artisan cache:table
php artisan queue:table
```

**Step 4.2: Student Table**
Generate the student migration file:

```bash
php artisan make:migration create_students_table
```

Open `database/migrations/xxxx_xx_xx_create_students_table.php` and add:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            // You can EDIT these fields or ADD new ones:
            $table->string('name');
            $table->string('email')->unique();
            $table->string('course'); // Example: 'BSIT', 'BSCS'
            $table->date('enrollment_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
```

**Step 4.3: Run Migrations**
Run all migrations:

```bash
php artisan migrate
```

## 5. Create Model

Generate the Student model:

```bash
php artisan make:model Student
```

Open `app/Models/Student.php` and add:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'course',
        'enrollment_date'
    ];

    protected $casts = [
        'enrollment_date' => 'date'
    ];
}
```

## 6. Create Controller

Generate the controller:

```bash
php artisan make:controller StudentController
```

Open `app/Http/Controllers/StudentController.php` and add:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    // Display all students
    public function index()
    {
        $students = Student::all();
        return view('students.index', compact('students'));
    }

    // Display a specific student
    public function show($id)
    {
        $student = Student::findOrFail($id);
        return view('students.show', compact('student'));
    }
}
```

## 7. Define Routes

Open `routes/web.php` and add:

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;

Route::get('/', function () {
    return view('welcome');
});

// Route to display all students
Route::get('/students', [StudentController::class, 'index']);

// Route to display a specific student
Route::get('/students/{id}', [StudentController::class, 'show']);
```

## 8. Create Blade Views

### Create the views directory structure:

```bash
mkdir -p resources/views/students
```

### Create `resources/views/students/index.blade.php`:

```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Students</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Student List</h1>

        @if($students->isEmpty())
            <p>No students found.</p>
        @else
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Course</th>
                        <th>Enrollment Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $student)
                        <tr>
                            <td>{{ $student->id }}</td>
                            <td>{{ $student->name }}</td>
                            <td>{{ $student->email }}</td>
                            <td>{{ $student->course }}</td>
                            <td>{{ $student->enrollment_date->format('Y-m-d') }}</td>
                            <td>
                                <a href="/students/{{ $student->id }}" class="btn btn-primary btn-sm">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</body>
</html>
```

### Create `resources/views/students/show.blade.php`:

```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Details</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Student Details</h1>

        <div class="card">
            <div class="card-body">
                <p><strong>ID:</strong> {{ $student->id }}</p>
                <p><strong>Name:</strong> {{ $student->name }}</p>
                <p><strong>Email:</strong> {{ $student->email }}</p>
                <p><strong>Course:</strong> {{ $student->course }}</p>
                <p><strong>Enrollment Date:</strong> {{ $student->enrollment_date->format('F d, Y') }}</p>
                <p><strong>Created At:</strong> {{ $student->created_at->format('F d, Y H:i:s') }}</p>
            </div>
        </div>

        <a href="/students" class="btn btn-primary mt-3">Back to Student List</a>
    </div>
</body>
</html>
```

## 9. Add Sample Data

To test your application, you can add sample data using Laravel Tinker:

```bash
php artisan tinker
```

Then run:

```php
App\Models\Student::create([
    // EDITABLE: You can change the name, email, course, and date below
    'name' => 'Kyle Baluyot',
    'email' => 'kyle@example.com',
    'course' => 'BSIT',
    'enrollment_date' => '2023-11-15'
]);

App\Models\Student::create([
    'name' => 'Kissareen Baluyot',
    'email' => 'kissareen@example.com',
    'course' => 'BSED',
    'enrollment_date' => '2024-11-15'
]);

App\Models\Student::create([
    'name' => 'Michael Rodriguez',
    'email' => 'michael.rodriguez@example.com',
    'course' => 'BSCS',
    'enrollment_date' => '2023-08-20'
]);

App\Models\Student::create([
    'name' => 'Sarah Martinez',
    'email' => 'sarah.martinez@example.com',
    'course' => 'BSBA',
    'enrollment_date' => '2024-01-10'
]);

App\Models\Student::create([
    'name' => 'David Hernandez',
    'email' => 'david.hernandez@example.com',
    'course' => 'BSCE',
    'enrollment_date' => '2023-09-05'
]);

App\Models\Student::create([
    'name' => 'Emily Garcia',
    'email' => 'emily.garcia@example.com',
    'course' => 'BSN',
    'enrollment_date' => '2024-02-28'
]);

App\Models\Student::create([
    'name' => 'James Lopez',
    'email' => 'james.lopez@example.com',
    'course' => 'BSEE',
    'enrollment_date' => '2023-10-12'
]);
```

## 10. Run the Application

Since you're using XAMPP, you have two options to run the application:

**Option 1: Using Laravel's built-in server (Recommended for development)**

```bash
# Run this in VS Code Terminal 1 (Server)
php artisan serve
```

Then visit: http://localhost:8000/students

**Option 2: Using XAMPP Apache server**

Make sure Apache is running in XAMPP Control Panel, then visit:

-   http://localhost/student-management/public/students

**Important:**

-   Make sure `npm run dev` is still running in another terminal to compile your assets.
-   If using XAMPP Apache, ensure the Apache module is started in XAMPP Control Panel.

Visit in your browser:

-   **All students**: http://localhost:8000/students (or http://localhost/student-management/public/students)
-   **Specific student**: http://localhost:8000/students/1 (or http://localhost/student-management/public/students/1)

## Summary

You've successfully created a Laravel Student Management feature with:

-   Clean project setup
-   Bootstrap 5 installed via NPM and configured with Vite
-   Database configuration
-   Migrations for system tables & students table
-   Student Model
-   StudentController with index and show methods
-   Routes for listing and viewing students
-   Responsive Bootstrap-styled Blade views with foreach loop and individual student details
