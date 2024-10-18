<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vous êtes banni!</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 dark:bg-gray-900 h-screen flex items-center justify-center">
    <div class="bg-white dark:bg-gray-800 p-10 rounded-lg shadow-lg text-center">
        <h1 class="text-4xl font-bold text-red-600 dark:text-red-500 mb-4">Accès refusé</h1>
        <p class="text-lg text-gray-700 dark:text-white mb-6">Vous avez été banni et ne pouvez pas accéder à ce site.</p>
        <livewire:banned.banned-time-and-reason />
    </div>
</body>
</html>