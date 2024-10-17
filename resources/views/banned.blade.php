<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Banni</title>
    @vite('resources/css/app.css') <!-- Assurez-vous que Tailwind est bien chargé -->
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center">
    <div class="bg-white p-10 rounded-lg shadow-lg text-center">
        <h1 class="text-4xl font-bold text-red-600 mb-4">Accès refusé</h1>
        <p class="text-lg text-gray-700 mb-6">Vous avez été banni et ne pouvez pas accéder à ce site.</p>
        <livewire:banned.banned-time-and-reason />
    </div>
</body>
</html>