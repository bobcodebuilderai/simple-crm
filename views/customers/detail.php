<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="mb-6">
    <div class="flex items-center gap-4 mb-2">
        <a href="<?= APP_URL ?>/customers" class="text-blue-600 hover:underline">← Tilbake til kunder</a>
    </div>
    
    <div class="flex justify-between items-start">
        <div>
            <h1 class="text-3xl font-bold text-gray-800"><?= e($customer['company_name']) ?></h1>
            <p class="text-gray-500">Kundenummer: <?= e($customer['customer_number']) ?></p>
        </div>
        
        <div class="flex gap-2">
            <span class="px-3 py-1 rounded-full text-sm <?= $customer['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' ?>">
                <?= $customer['status'] === 'active' ? 'Aktiv' : 'Inaktiv' ?>
            </span>
            <a href="<?= APP_URL ?>/customers/edit/<?= $customer['customer_id'] ?>" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">Rediger</a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left column: Info -->
    <div class="lg:col-span-1">
        <!-- Contact Info -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4">Kontaktinformasjon</h2>
            
            <div class="space-y-3">
                <?php if ($customer['org_number']): ?>
                    <div>
                        <span class="text-gray-500 text-sm">Org.nr:</span>
                        <p><?= e($customer['org_number']) ?></p>
                    </div>
                <?php endif; ?>
                
                <?php if ($customer['address']): ?>
                    <div>
                        <span class="text-gray-500 text-sm">Adresse:</span>
                        <p><?= e($customer['address']) ?></p>
                        <p><?= e($customer['postal_code']) ?> <?= e($customer['city']) ?></p>
                    </div>
                <?php endif; ?>
                
                
                <?php if ($customer['phone']): ?
                    <div>
                        <span class="text-gray-500 text-sm">Telefon:</span>
                        <p><?= e($customer['phone']) ?></p>
                    </div>
                <?php endif; ?>
                
                
                <?php if ($customer['email']): ?
                    <div>
                        <span class="text-gray-500 text-sm">E-post:</span>
                        <p><span class="text-gray-300"><?= e($customer['email']) ?></p>
                    </div>
                <?php endif; ?>
                
                
                <?php if ($customer['website']): ?
                    <div>
                        <span class="text-gray-500 text-sm">Nettside:</span>
                        <p><a href="<?= e($customer['website']) ?>" target="_blank" class="text-blue-600 hover:underline"><?= e($customer['website']) ?></a></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        
        <!-- Stats -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">Oversikt</h2>
            
            <div class="grid grid-cols-2 gap-4">
                <div class="text-center p-4 bg-blue-50 rounded">
                    <p class="text-2xl font-bold text-blue-600"><?= count($contacts) ?></p>
                    <p class="text-sm text-gray-600">Kontaktpersoner</p>
                </div>
                
                
                <div class="text-center p-4 bg-green-50 rounded">
                    <p class="text-2xl font-bold text-green-600"><?= count($deals) ?></p>
                    <p class="text-sm text-gray-600">Deals</p>
                </div>
                
                
                <div class="text-center p-4 bg-yellow-50 rounded">
                    <p class="text-2xl font-bold text-yellow-600"><?= count($tasks) ?></p>
                    <p class="text-sm text-gray-600">Åpne oppgaver</p>
                </div>
                
                
                <div class="text-center p-4 bg-purple-50 rounded">
                    <p class="text-2xl font-bold text-purple-600"><?= count($activities) ?></p>
                    <p class="text-sm text-gray-600">Aktiviteter</p>
                </div>
            </div>
        </div>
    </div>
    
    
    
    <!-- Right column: Details -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Contacts -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b flex justify-between items-center">
                <h2 class="text-lg font-semibold">Kontaktpersoner</h2>
                <a href="<?= APP_URL ?>/contacts/create?customer_id=<?= $customer['customer_id'] ?>" 
                   class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">+ Ny</a>
            </div>
            
            
            <?php if (empty($contacts)): ?
                <div class="p-6 text-center text-gray-500">
                    <p>Ingen kontaktpersoner</p>
                </div>
            <?php else: ?
                <div class="divide-y">
                    <?php foreach ($contacts as $contact): ?
                        <div class="px-6 py-4 flex justify-between items-center">
                            <div>
                                <p class="font-medium">
                                    <?= e($contact['first_name'] . ' ' . $contact['last_name']) ?>
                                    <?php if ($contact['is_primary']): ?
                                        <span class="ml-2 px-2 py-0.5 bg-blue-100 text-blue-800 text-xs rounded">Primær</span>
                                    <?php endif; ?>
                                </p>
                                <?php if ($contact['title']): ?
                                    <p class="text-sm text-gray-500"><?= e($contact['title']) ?></p>
                                <?php endif; ?
                                
                                <?php if ($contact['email']): ?
                                    <p class="text-sm"><?= e($contact['email']) ?></p>
                                <?php endif; ?
                                
                                
                                <?php if ($contact['phone'] || $contact['mobile']): ?
                                    <p class="text-sm text-gray-500"><?= e($contact['phone'] ?? $contact['mobile']) ?></p>
                                <?php endif; ?>
                            </div>
                            
                            <div>
                                <a href="<?= APP_URL ?>/contacts/edit/<?= $contact['contact_id'] ?>" class="text-blue-600 hover:underline text-sm">Rediger</a>
                            </div>
                        </div>
                    <?php endforeach; ?
003e                </div>
            <?php endif; ?>
        </div>
        
        
        
        <!-- Deals -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b flex justify-between items-center">
                <h2 class="text-lg font-semibold">Deals</h2>
                <a href="<?= APP_URL ?>/deals/create?customer_id=<?= $customer['customer_id'] ?>" 
                   class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700">+ Ny</a>
            </div>
            
            
            <?php if (empty($deals)): ?
                <div class="p-6 text-center text-gray-500">
                    <p>Ingen deals</p>
                </div>
            <?php else: ?
                <div class="divide-y">
                    <?php foreach ($deals as $deal): ?
                        <div class="px-6 py-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <a href="<?= APP_URL ?>/deals/view/<?= $deal['deal_id'] ?>" class="font-medium text-blue-600 hover:underline">
                                        <?= e($deal['title']) ?>
                                    </a>
                                    
                                    <span class="ml-2 px-2 py-0.5 rounded text-xs <?= getDealStatusColor($deal['status']) ?>">
                                        <?= getDealStatusLabel($deal['status']) ?>
                                    </span>
                                </div>
                                
                                <?php if ($deal['value']): ?
                                    <span class="font-semibold"><?= formatCurrency($deal['value']) ?></span>
                                <?php endif; ?>
                            </div>
                            
                            
                            <?php if ($deal['expected_close_date']): ?
                                <p class="text-sm text-gray-500 mt-1">
                                    Forventet avslutning: <?= formatDate($deal['expected_close_date']) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?
003e                </div>
            <?php endif; ?>
        </div>
        
        
        
        <!-- Tasks -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b flex justify-between items-center">
                <h2 class="text-lg font-semibold">Oppgaver</h2>
                <a href="<?= APP_URL ?>/tasks/create?customer_id=<?= $customer['customer_id'] ?>" 
                   class="bg-yellow-600 text-white px-3 py-1 rounded text-sm hover:bg-yellow-700">+ Ny</a>
            </div>
            
            
            <?php if (empty($tasks)): ?
                <div class="p-6 text-center text-gray-500">
                    <p>Ingen åpne oppgaver</p>
                </div>
            <?php else: ?
                <div class="divide-y">
                    <?php foreach ($tasks as $task): ?
                        <div class="px-6 py-4 <?= isOverdue($task['due_date']) ? 'bg-red-50' : '' ?>">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-medium"><?= e($task['title']) ?></p>
                                    
                                    <span class="inline-block px-2 py-0.5 rounded text-xs <?= getTaskPriorityColor($task['priority']) ?> mt-1">
                                        <?= getTaskPriorityLabel($task['priority']) ?>
                                    </span>
                                </div>
                                
                                
                                <span class="text-sm <?= isOverdue($task['due_date']) ? 'text-red-600 font-bold' : 'text-gray-500' ?>">
                                    <?= formatDate($task['due_date']) ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?
003e                </div>
            <?php endif; ?>
        </div>
        
        
        
        <!-- Recent Activities -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b flex justify-between items-center">
                <h2 class="text-lg font-semibold">Siste aktiviteter</h2>
                <a href="<?= APP_URL ?>/activities/create?customer_id=<?= $customer['customer_id'] ?>" 
                   class="bg-purple-600 text-white px-3 py-1 rounded text-sm hover:bg-purple-700">+ Ny</a>
            </div>
            
            
            <?php if (empty($activities)): ?
                <div class="p-6 text-center text-gray-500">
                    <p>Ingen aktiviteter</p>
                </div>
            <?php else: ?
                <div class="divide-y">
                    <?php foreach ($activities as $activity): ?
                        <div class="px-6 py-4">
                            <p class="font-medium"><?= e($activity['title']) ?></p>
                            
                            <p class="text-sm text-gray-500">
                                <?= getActivityTypeLabel($activity['activity_type']) ?> • <?= formatDateTime($activity['activity_date']) ?>
                                <?php if ($activity['first_name']): ?
                                    • <?= e($activity['first_name'] . ' ' . $activity['last_name']) ?>
                                <?php endif; ?>
                            </p>
                        </div>
                    <?php endforeach; ?
003e                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
