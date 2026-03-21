<?php
/**
 * Dashboard View
 */

require __DIR__ . '/../partials/header.php';

// Get statistics
require_once __DIR__ . '/../../models/Customer.php';
require_once __DIR__ . '/../../models/Deal.php';
require_once __DIR__ . '/../../models/Task.php';
require_once __DIR__ . '/../../models/Activity.php';

$customerModel = new Customer();
$dealModel = new Deal();
$taskModel = new Task();
$activityModel = new Activity();

$customerStats = $customerModel->getStats();
$dealStats = $dealModel->getStats();
$taskStats = $taskModel->getStats();
$recentActivities = $activityModel->getRecent(5);
$overdueTasks = $taskModel->getOverdueTasks();
$upcomingTasks = $taskModel->getOpenTasks(5);
?>

<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-2">Dashboard</h1>
    <p class="text-gray-600">Oversikt over CRM-systemet</p>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Active Customers -->
    <a href="<?= APP_URL ?>/customers" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-all">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm">Aktive kunder</p>
                <p class="text-3xl font-bold text-blue-600"><?= $customerStats['active'] ?></p>
            </div>
            <div class="text-4xl">🏢</div>
        </div>
    </a>
    
    <!-- Open Deals -->
    <a href="<?= APP_URL ?>/deals" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-all">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm">Åpne deals</p>
                <p class="text-3xl font-bold text-green-600"><?= $dealStats['open_count'] ?></p>
            </div>
            <div class="text-4xl">💼</div>
        </div>
        <?php if ($dealStats['open_value'] > 0): ?>
            <p class="text-sm text-gray-500 mt-2">Verdi: <?= formatCurrency($dealStats['open_value']) ?></p>
        <?php endif; ?>
    </a>
    
    <!-- Open Tasks -->
    <a href="<?= APP_URL ?>/tasks" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-all">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm">Åpne oppgaver</p>
                <p class="text-3xl font-bold text-yellow-600"><?= $taskStats['open'] ?></p>
            </div>
            <div class="text-4xl">✅</div>
        </div>
        
        <?php if ($taskStats['overdue'] > 0): ?>
            <p class="text-sm text-red-500 mt-2"><?= $taskStats['overdue'] ?> forfalte</p>
        <?php endif; ?>
    </a>
    
    <!-- Recent Activities -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm">Nye kunder (30 dager)</p>
                <p class="text-3xl font-bold text-purple-600"><?= $customerStats['recent'] ?></p>
            </div>
            <div class="text-4xl">📈</div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Recent Activities -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b">
            <h2 class="text-lg font-semibold">Siste aktiviteter</h2>
        </div>
        
        <div class="divide-y">
            <?php if (empty($recentActivities)): ?>
                <p class="p-6 text-gray-500">Ingen aktiviteter ennå</p>
            <?php else: ?
                <?php foreach ($recentActivities as $activity): ?>
                    <div class="px-6 py-4 hover:bg-gray-50">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="font-medium"><?= e($activity['title']) ?></p>
                                <p class="text-sm text-gray-500">
                                    <?= e($activity['company_name']) ?>
                                    <?php if ($activity['first_name']): ?
003e                                        • <?= e($activity['first_name'] . ' ' . $activity['last_name']) ?>
                                    <?php endif; ?>
                                </p>
                                <p class="text-xs text-gray-400 mt-1">
                                    <?= getActivityTypeLabel($activity['activity_type']) ?> • <?= formatDateTime($activity['created_at']) ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Upcoming Tasks -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b flex justify-between items-center">
            <h2 class="text-lg font-semibold">Neste oppgaver</h2>
            <a href="<?= APP_URL ?>/tasks" class="text-blue-600 text-sm hover:underline">Se alle →</a>
        </div>
        
        <div class="divide-y">
            <?php if (empty($upcomingTasks)): ?
                <p class="p-6 text-gray-500">Ingen kommende oppgaver</p>
            <?php else: ?
                <?php foreach ($upcomingTasks as $task): ?
                    <div class="px-6 py-4 hover:bg-gray-50 <?= isOverdue($task['due_date']) ? 'bg-red-50' : '' ?>">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="font-medium"><?= e($task['title']) ?></p>
                                <p class="text-sm text-gray-500"><?= e($task['company_name']) ?></p>
                                <p class="text-xs mt-1">
                                    <span class="inline-block px-2 py-1 rounded text-xs <?= getTaskPriorityColor($task['priority']) ?>">
                                        <?= getTaskPriorityLabel($task['priority']) ?>
                                    </span>
                                    <span class="<?= isOverdue($task['due_date']) ? 'text-red-600 font-bold' : 'text-gray-400' ?> ml-2">
                                        <?= formatDate($task['due_date']) ?>
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
