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
        <h1 class="mb-4 text-center">Student Details</h1>
        
        <div class="card">
            <div class="card-body">
                <p><strong>ID:</strong> {{ $student->id }}</p>
                <p><strong>Name:</strong> {{ $student->name }}</p>
                <p><strong>Email:</strong> {{ $student->email }}</p>
                <p><strong>Course:</strong> {{ $student->course }}</p>
                <p><strong>Enrollment Date:</strong> {{ $student->enrollment_date->format('F d, Y') }}</p>
                <!-- <p><strong>Created At:</strong> {{ $student->created_at->format('F d, Y H:i:s') }}</p> -->
            </div>
        </div>
        
        <a href="/students" class="btn btn-warning mt-3">Back to Student List</a>
    </div>
</body>
</html>