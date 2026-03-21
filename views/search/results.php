<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800"><?= $pageTitle ?></h1>
</div>

<?php if (empty($query)): ?
    <div class="bg-white rounded-lg shadow p-8 text-center">
        <p class="text-gray-500">Skriv inn søkeord i feltet øverst for å søke</p>
    </div>
<?php else: ?
    
    <p class="text-gray-600 mb-4">Søkeresultater for: "<strong><?= e($query) ?></strong>"</p>
    
    
    <!-- Customers -->
    <?php if (!empty($customers)): ?
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="px-6 py-4 border-b">
                <h2 class="text-lg font-semibold">Kunder (<?= count($customers) ?>)</h2>
            </div>
            
            
            <div class="divide-y">
                <?php foreach ($customers as $customer): ?
                    <div class="px-6 py-4 hover:bg-gray-50">
                        <a href="<?= APP_URL ?>/customers/view/<?= $customer['customer_id'] ?>" class="text-blue-600 hover:underline font-medium">
                            <?= e($customer['company_name']) ?>
                        </a>
                        
                        <p class="text-sm text-gray-500"><?= e($customer['customer_number']) ?> • <?= e($customer['city'] ?? 'Ingen by') ?></p>
                    </div>
                <?php endforeach; ?
003e            </div>
        </div>
    <?php endif; ?>
    
    
    
    <!-- Contacts -->
    <?php if (!empty($contacts)): ?
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="px-6 py-4 border-b">
                <h2 class="text-lg font-semibold">Kontaktpersoner (<?= count($contacts) ?>)</h2>
            </div>
            
            
            <div class="divide-y">
                <?php foreach ($contacts as $contact): ?
                    <div class="px-6 py-4 hover:bg-gray-50">
                        <p class="font-medium"><?= e($contact['first_name'] . ' ' . $contact['last_name']) ?></p>
                        
                        <p class="text-sm text-gray-500"><?= e($contact['company_name']) ?></p>
                        
                        <?php if ($contact['email']): ?
                            <p class="text-sm"><?= e($contact['email']) ?></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?
003e            </div>
        </div>
    <?php endif; ?>
    
    
    
    <?php if (empty($customers) && empty($contacts)): ?
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <p class="text-gray-500">Ingen resultater funnet</p>
        </div>
    <?php endif; ?>
    
<?php endif; ?>

<?php require __DIR__ . '/../partials/footer.php'; ?>
