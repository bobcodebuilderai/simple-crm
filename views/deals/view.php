<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="mb-6">
    <div class="flex items-center gap-4 mb-2">
        <a href="<?= APP_URL ?>/deals" class="text-blue-600 hover:underline">← Tilbake til deals</a>
    </div>
    
    <div class="flex justify-between items-start">
        <div>
            <h1 class="text-3xl font-bold text-gray-800"><?= e($deal['title']) ?></h1>
            <p class="text-gray-500"><?= e($deal['company_name']) ?></p>
        </div>
        
        <div class="flex gap-2">
            <span class="px-3 py-1 rounded-full text-sm <?= getDealStatusColor($deal['status']) ?>">
                <?= getDealStatusLabel($deal['status']) ?>
            </span>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left column -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4">Detaljer</h2>
            
            <div class="space-y-3">
                <?php if ($deal['value']): ?
003e                    
                    <div>
                        <span class="text-gray-500 text-sm">Verdi:</span>
                        <p class="text-2xl font-bold text-green-600"><?= formatCurrency($deal['value']) ?></p>
                    </div>
                <?php endif; ?>
                
                
                <?php if ($deal['expected_close_date']): ?
003e                    
                    <div>
                        <span class="text-gray-500 text-sm">Forventet avslutning:</span>
                        <p><?= formatDate($deal['expected_close_date']) ?></p>
                    </div>
                <?php endif; ?>
                
                
                <div>
                    <span class="text-gray-500 text-sm">Opprettet:</span>
                    <p><?= formatDateTime($deal['created_at']) ?></p>
                </div>
            </div>
            
            
            <div class="mt-6">
                <a href="<?= APP_URL ?>/deals/edit/<?= $deal['deal_id'] ?>" class="block w-full text-center bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Rediger deal</a>
            </div>
        </div>
    </div>
    
    
    
    <!-- Right column -->
    <div class="lg:col-span-2">
        <!-- Description -->
        <?php if ($deal['description']): ?
003e            
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-lg font-semibold mb-4">Beskrivelse</h2>
                
                
                <p class="text-gray-700 whitespace-pre-wrap"><?= nl2br(e($deal['description'])) ?></p>
            </div>
        <?php endif; ?>
        
        
        
        <!-- Tasks -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b flex justify-between items-center">
                <h2 class="text-lg font-semibold">Oppgaver knyttet til denne dealen</h2>
                
                <a href="<?= APP_URL ?>/tasks/create?deal_id=<?= $deal['deal_id'] ?>&customer_id=<?= $deal['customer_id'] ?>" 
                   class="bg-yellow-600 text-white px-3 py-1 rounded text-sm hover:bg-yellow-700">+ Ny oppgave</a>
            </div>
            
            
            <?php if (empty($tasks)): ?
                
                <div class="p-6 text-center text-gray-500">
                    <p>Ingen oppgaver</p>
                </div>
            <?php else: ?
                
                <div class="divide-y">
                    <?php foreach ($tasks as $task): ?
                        
                        <div class="px-6 py-4 <?= isOverdue($task['due_date']) && $task['status'] === 'open' ? 'bg-red-50' : '' ?>">
                            
                            <div class="flex justify-between items-start">
                                
                                <div>
                                    <p class="font-medium"><?= e($task['title']) ?></p>
                                    
                                    
                                    <span class="inline-block px-2 py-0.5 rounded text-xs <?= getTaskPriorityColor($task['priority']) ?> mt-1">
                                        <?= getTaskPriorityLabel($task['priority']) ?>
                                    </span>
                                    
                                    
                                    <?php if ($task['status'] === 'completed'): ?
                                        
                                        <span class="ml-2 text-green-600">✓ Fullført</span>
                                    <?php endif; ?>
                                </div>
                                
                                
                                
                                <span class="text-sm <?= isOverdue($task['due_date']) && $task['status'] === 'open' ? 'text-red-600 font-bold' : 'text-gray-500' ?>">
                                    <?= formatDate($task['due_date']) ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?
003e                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
