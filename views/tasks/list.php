<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="mb-6 flex justify-between items-center">
    <h1 class="text-3xl font-bold text-gray-800"><?= $pageTitle ?></h1>
</div>

<?php if (!empty($overdueTasks)): ?
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
        <h2 class="text-red-800 font-semibold mb-2">⚠️ Forfalte oppgaver (<?= count($overdueTasks) ?>)</h2>
        
        <div class="space-y-2">
            <?php foreach (array_slice($overdueTasks, 0, 3) as $task): ?
                <div class="flex justify-between items-center bg-white p-3 rounded">
                    <div>
                        <a href="<?= APP_URL ?>/tasks/view/<?= $task['task_id'] ?>" class="font-medium text-blue-600 hover:underline"><?= e($task['title']) ?></a>
                        <span class="text-sm text-gray-500 ml-2">• <?= e($task['company_name']) ?></span>
                    </div>
                    
                    <span class="text-red-600 font-bold"><?= formatDate($task['due_date']) ?></span>
                </div>
            <?php endforeach; ?
003e        </div>
    </div>
<?php endif; ?>

<!-- Filter -->
<div class="bg-white rounded-lg shadow p-4 mb-6">
    <div class="flex gap-4">
        <a href="<?= APP_URL ?>/tasks?status=open" 
           class="px-4 py-2 rounded <?= $status === 'open' ? 'bg-blue-600 text-white' : 'bg-gray-200' ?>">Åpne</a>
        
        
        <a href="<?= APP_URL ?>/tasks?status=all" 
           class="px-4 py-2 rounded <?= $status === 'all' ? 'bg-blue-600 text-white' : 'bg-gray-200' ?>">Alle</a>
    </div>
</div>

<?php if (empty($tasks)): ?
    <div class="bg-white rounded-lg shadow p-8 text-center">
        <p class="text-gray-500 text-lg">Ingen oppgaver funnet</p>
    </div>
<?php else: ?
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Oppgave</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kunde/Deal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prioritet</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Forfallsdato</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Handlinger</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                <?php foreach ($tasks as $task): ?>
                    <tr class="hover:bg-gray-50 <?= isOverdue($task['due_date']) && $task['status'] === 'open' ? 'bg-red-50' : '' ?>">
                        <td class="px-6 py-4">
                            <a href="<?= APP_URL ?>/tasks/view/<?= $task['task_id'] ?>" class="text-blue-600 hover:underline font-medium">
                                <?= e($task['title']) ?>
                            </a>
                            
                            <?php if ($task['status'] === 'completed'): ?
                                <span class="ml-2 text-green-600">✓</span>
                            <?php endif; ?>
                        </td>
                        
                        <td class="px-6 py-4">
                            <span class="text-sm"><?= e($task['company_name']) ?></span>
                            
                            <?php if ($task['deal_title']): ?
                                <span class="text-xs text-gray-500 block"><?= e($task['deal_title']) ?></span>
                            <?php endif; ?>
                        </td>
                        
                        <td class="px-6 py-4">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full <?= getTaskPriorityColor($task['priority']) ?>">
                                <?= getTaskPriorityLabel($task['priority']) ?>
                            </span>
                        </td>
                        
                        <td class="px-6 py-4">
                            <span class="text-sm <?= isOverdue($task['due_date']) && $task['status'] === 'open' ? 'text-red-600 font-bold' : '' ?>">
                                <?= formatDate($task['due_date']) ?>
                            </span>
                        </td>
                        
                        <td class="px-6 py-4 text-right">
                            <?php if ($task['status'] !== 'completed'): ?
                                <a href="<?= APP_URL ?>/tasks/complete/<?= $task['task_id'] ?>" 
                                   class="text-green-600 hover:underline mr-3"
                                   onclick="return confirm('Marker som fullført?')">✓ Fullfør</a>
                            <?php endif; ?
003e                            
                            
                            <a href="<?= APP_URL ?>/tasks/view/<?= $task['task_id'] ?>" class="text-blue-600 hover:underline">Vis</a>
                        </td>
                    </tr>
                <?php endforeach; ?
003e            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/../partials/footer.php'; ?>
