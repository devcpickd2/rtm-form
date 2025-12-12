<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ready Meal Login</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="{{ asset('assets/fontawesome/css/all.min.css') }}">
    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }
        body {
            background: linear-gradient(rgba(180, 30, 30, 0.6), rgba(180, 30, 30, 0.6)), url('images/ready_meal2.png');
            background-size: cover;
            background-position: center;
        }
    </style>
</head>
<body class="flex justify-center items-center h-screen">
    <!-- Container for the entire content, centered -->
    <div class="relative z-10 text-center text-white space-y-8">
        <!-- Ready Meal Title -->
        <h1 class="text-6xl font-extrabold tracking-widest uppercase">
            READY MEAL
        </h1>

        <!-- Login Card -->
        <div class="w-[420px] p-10 bg-[rgba(180,30,30,0.6)] backdrop-blur-sm border border-white border-opacity-30 rounded-2xl shadow-[0_10px_25px_rgba(0,0,0,0.25)] flex flex-col items-center space-y-5">
            <!-- Form untuk login -->
            <form method="POST" action="{{ url('/login') }}" class="w-full space-y-5">
               @csrf
               <!-- Username Input -->
               <div class="relative">
                <i class="fas fa-user absolute left-4 top-1/2 -translate-y-1/2 text-white text-lg"></i>
                <input type="text" name="username" placeholder="Username" required class="w-full pl-12 pr-4 py-3 bg-white bg-opacity-30 text-white placeholder-white placeholder-opacity-70 rounded-lg focus:outline-none focus:bg-opacity-40 transition-colors duration-300" required>
            </div>
            
            <!-- Password Input -->
            <div class="relative">
                <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-white text-lg"></i>
                <input type="password" name="password" placeholder="Password" required class="w-full pl-12 pr-4 py-3 bg-white bg-opacity-30 text-white placeholder-white placeholder-opacity-70 rounded-lg focus:outline-none focus:bg-opacity-40 transition-colors duration-300" required>
            </div>

            <!-- Login Button -->
            <button type="submit" class="w-full py-3 mt-4 font-semibold text-white bg-red-800 rounded-lg hover:bg-red-900 transition-colors duration-300">
                LOGIN
            </button>
        </form>

        <!-- Forgot Password Link -->
        <!-- <a href="#" class="text-sm text-white hover:underline">
            Forgot password?
        </a> -->
    </div>
</div>
</body>
</html>
