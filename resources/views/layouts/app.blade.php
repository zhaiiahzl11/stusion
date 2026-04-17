<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-select">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Stusion - Session System</title>

        <!-- Fonts & Icons -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <style>
            @import url('https://api.fontshare.com/v2/css?f[]=switzer@400,500,600,700,800,900&display=swap');
        </style>
        <script src="https://unpkg.com/lucide@latest"></script>

        <!-- Tailwind Vite (If exists) or CDN -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://cdn.tailwindcss.com"></script>
        
        <style>
            :root {
                --background: 0 0% 100%;
                --foreground: 222.2 84% 4.9%;
                --card: 0 0% 100%;
                --card-foreground: 222.2 84% 4.9%;
                --popover: 0 0% 100%;
                --popover-foreground: 222.2 84% 4.9%;
                --primary: 221.2 83.2% 53.3%;
                --primary-foreground: 210 40% 98%;
                --secondary: 210 40% 96.1%;
                --secondary-foreground: 222.2 47.4% 11.2%;
                --muted: 210 40% 96.1%;
                --muted-foreground: 215.4 16.3% 46.9%;
                --accent: 210 40% 96.1%;
                --accent-foreground: 222.2 47.4% 11.2%;
                --destructive: 0 84.2% 60.2%;
                --destructive-foreground: 210 40% 98%;
                --border: 214.3 31.8% 91.4%;
                --input: 214.3 31.8% 91.4%;
                --ring: 221.2 83.2% 53.3%;
                --radius: 0.5rem;
            }
            body { background: white; font-family: 'Switzer', sans-serif !important; }
        </style>
    </head>
    <body class="antialiased text-gray-900 bg-gray-50 flex h-screen overflow-hidden">
        
        @yield('content')

        <script>
            lucide.createIcons();
        </script>
    </body>
</html>
