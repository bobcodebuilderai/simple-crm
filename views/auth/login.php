<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logg inn - <?= APP_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    
    <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-800"><?= APP_NAME ?></h1>
            <p class="text-gray-600 mt-2">Logg inn for å fortsette</p>
        </div>
        
        
        <?php if (isset($error)): ?>
            <div class="bg-red-100 border border-red-200 text-red-800 px-4 py-3 rounded mb-4">
                <?= e($error) ?>
            </div>
        <?php endif; ?>
        
        
        <?php displayFlashMessage(); ?>
        
        
        <form method="POST">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Brukernavn</label>
                <input type="text" name="username" required autofocus
                       class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Passord</label>
                <input type="password" name="password" required
                       class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
                Logg inn
            </button>
        </form>
        
        
        <div class="mt-6 text-center text-sm text-gray-500">
            <p>Standard bruker: <strong>admin / admin123</strong></p>
            <p class="mt-1">Husk å endre passord etter første innlogging!</p>
        </div>
    </div>
    
</body>
</html>
