<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? APP_NAME) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        
        /* Smooth transitions */
        .transition-all {
            transition: all 0.2s ease-in-out;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-blue-600 text-white shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <a href="<?= APP_URL ?>" class="text-xl font-bold">
                    <?= APP_NAME ?>
                </a>
                
                <!-- Search -->
                <form action="<?= APP_URL ?>/search" method="GET" class="flex-1 max-w-md mx-8">
                    <div class="relative">
                        <input type="text" name="q" placeholder="Søk..." 
                            class="w-full px-4 py-2 rounded-lg text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-300"
                            value="<?= e($_GET['q'] ?? '') ?>">
                        <button type="submit" class="absolute right-3 top-2.5 text-gray-500">
                            🔍
                        </button>
                    </div>
                </form>
                
                <!-- Navigation Links -->
                <div class="flex items-center space-x-6">
                    <a href="<?= APP_URL ?>/customers" 
                       class="hover:text-blue-200 <?= isCurrentPath('/customers') ? 'font-bold' : '' ?>">
                        Kunder
                    </a>
                    <a href="<?= APP_URL ?>/deals" 
                       class="hover:text-blue-200 <?= isCurrentPath('/deals') ? 'font-bold' : '' ?>">
                        Deals
                    </a>
                    <a href="<?= APP_URL ?>/tasks" 
                       class="hover:text-blue-200 <?= isCurrentPath('/tasks') ? 'font-bold' : '' ?>">
                        Oppgaver
                    </a>
                    
                    <!-- User Menu -->
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="flex items-center space-x-4 ml-4 border-l border-blue-500 pl-4">
                            <span class="text-sm"><?= e($_SESSION['full_name'] ?? $_SESSION['username']) ?></span>
                            <a href="<?= APP_URL ?>/auth/logout" class="text-sm hover:text-blue-200">Logg ut</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <!-- Flash Messages -->
        <?php displayFlashMessage(); ?>
