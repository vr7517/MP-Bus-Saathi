<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'लॉगिन - मध्य प्रदेश बस साथी' }}</title>
    <!-- Tailwind CSS (CDN - Zero Node/NPM dependencies) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Devanagari:wght@400;600;700&family=Inter:wght@400;500;600;700&display=swap');
        body { font-family: 'Noto Sans Devanagari', 'Inter', sans-serif; }
    </style>
    @livewireStyles
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        {{ $slot }}
    </div>
    @livewireScripts
</body>
</html>

