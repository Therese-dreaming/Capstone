<!DOCTYPE html>
<html lang="en">
<head>
    @include('partials.head')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex h-screen flex-col md:flex-row">

    <!-- Left Side -->
    <div class="w-full md:w-1/2 bg-red-800 flex flex-col items-center justify-center relative p-8 md:p-0">
        <!-- Content container -->
        <div class="relative z-10 text-center">
            <h1 class="text-white text-4xl font-bold mb-6">Laboratory Management</h1>
            <p class="text-white/80 text-lg mb-8 max-w-md mx-auto">
                Access the laboratory login system quickly and easily
            </p>
            <a href="{{ route('lab.logging') }}" 
               class="inline-flex items-center bg-white text-red-800 px-8 py-4 rounded-lg hover:bg-gray-100 transition-all duration-200 font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-1 group">
                <svg class="w-6 h-6 mr-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Lab Login
            </a>
        </div>
        <!-- Background gradient -->
        <div class="absolute inset-0 opacity-10 bg-gradient-to-br from-white/10 to-transparent"></div>
    </div>

    <!-- Right Side -->
    <div class="w-full md:w-1/2 bg-white flex flex-col items-center justify-center p-8">
        <div class="w-full max-w-md">
            <div class="flex justify-center mb-6">
                <img src="{{ asset('images/logo-small.png') }}" alt="Right Side Image" width="227" height="230" class="drop-shadow-2xl">
            </div>
            <h2 class="text-2xl font-bold mb-6" style="font-family: 'Inter', sans-serif;">Welcome!</h2>

            <form method="POST" action="{{ route('auth.login') }}">
                @csrf

                @if ($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div class="mb-4">
                    <label for="ID Number" class="block text-gray-700"></label>
                    <input type="text" id="username" name="username" required class="w-full px-3 py-3 bg-gray-100 font-medium" 
                        placeholder="ID Number or RFID Number" style="font-family: 'Inter', sans-serif;">
                </div>

                <div class="mb-4">
                    <label for="password" class="block text-gray-700"></label>
                    <input type="password" id="password" name="password" required class="w-full px-3 py-3 bg-gray-100 font-medium" 
                        placeholder="Password" style="font-family: 'Inter', sans-serif;">
                </div>

                <button type="submit" class="w-full bg-red-800 text-white py-2 rounded-lg hover:bg-red-600 font-semibold" style="font-family: 'Inter', sans-serif;">Login</button>
            </form>
        </div>
    </div>

</body>
</html>
