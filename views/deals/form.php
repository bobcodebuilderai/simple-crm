<?php 
require __DIR__ . '/../partials/header.php';

$isEdit = !empty($formData['deal_id']);
$deal = $isEdit ? $formData : null;
?>

<div class="max-w-3xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-800 mb-6"><?= $pageTitle ?></h1>
    
    <form method="POST" class="bg-white rounded-lg shadow p-6">
        <?= csrfField() ?>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Customer -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Kunde *</label>
                
                <?php if ($isEdit || !empty($_GET['customer_id'])): ?>
                    <input type="hidden" name="customer_id" value="<?= $formData['customer_id'] ?? $_GET['customer_id'] ?>">
                    
                    <p class="px-3 py-2 bg-gray-100 rounded"><?= e($customer['company_name'] ?? '') ?></p>
                <?php else: ?
003e                    
                    <select name="customer_id" required class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Velg kunde...</option>
                        
                        <?php foreach ($customers as $c): ?>
                            <option value="<?= $c['customer_id'] ?>" <?= ($formData['customer_id'] ?? '') == $c['customer_id'] ? 'selected' : '' ?>>
                                <?= e($c['company_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php endif; ?>
            </div>
            
            
            <!-- Title -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Tittel *</label>
                <input type="text" name="title" required
                       value="<?= e($formData['title'] ?? '') ?>"
                       class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="F.eks. Nytt prosjekt 2024">
            </div>
            
            
            <!-- Value -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Verdi (kr)</label>
                <input type="number" name="value" step="0.01" min="0"
                       value="<?= e($formData['value'] ?? '') ?>"
                       class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="0.00">
            </div>
            
            
            <!-- Status -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="new" <?= ($formData['status'] ?? 'new') === 'new' ? 'selected' : '' ?>>Ny</option>
                    <option value="ongoing" <?= ($formData['status'] ?? '') === 'ongoing' ? 'selected' : '' ?>>Pågående</option>
                    <option value="won" <?= ($formData['status'] ?? '') === 'won' ? 'selected' : '' ?>>Vunnet</option>
                    <option value="lost" <?= ($formData['status'] ?? '') === 'lost' ? 'selected' : '' ?>>Tapt</option>
                    <option value="on_hold" <?= ($formData['status'] ?? '') === 'on_hold' ? 'selected' : '' ?>>Satt på vent</option>
                </select>
            </div>
            
            
            <!-- Expected close date -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Forventet avslutningsdato</label>
                <input type="date" name="expected_close_date"
                       value="<?= e($formData['expected_close_date'] ?? '') ?>"
                       class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            
            <!-- Description -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Beskrivelse</label>
                <textarea name="description" rows="4" class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"><?= e($formData['description'] ?? '') ?></textarea>
            </div>
        </div>
        
        
        <div class="mt-6 flex justify-between">
            <a href="<?= APP_URL ?>/deals" class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600">Avbryt</a>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700"><?= $isEdit ? 'Oppdater' : 'Opprett' ?></button>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
