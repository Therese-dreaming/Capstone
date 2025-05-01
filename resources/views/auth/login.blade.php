<!DOCTYPE html>
<html lang="en">
<head>
    @include('partials.head')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex h-screen">

    <!-- Left Side -->
    <div class="w-1/2 bg-red-800 flex items-center justify-center">
        <img src="{{ asset('images/logo-big.png') }}" alt="Left Side Image" width="746" height="763">
    </div>

    <!-- Right Side -->
    <div class="w-1/2 bg-white flex flex-col items-center justify-center p-8">
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
