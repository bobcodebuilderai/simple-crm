<?php 
require __DIR__ . '/../partials/header.php';

$isEdit = !empty($formData['task_id']);
$task = $isEdit ? $formData : null;
?>

<div class="max-w-3xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-800 mb-6"><?= $pageTitle ?></h1>
    
    <form method="POST" class="bg-white rounded-lg shadow p-6">
        <?= csrfField() ?>
        
        <input type="hidden" name="customer_id" value="<?= $formData['customer_id'] ?? $_GET['customer_id'] ?? '' ?>">
        
        <?php if (!empty($dealId) || !empty($formData['deal_id'])): ?
003e            
            <input type="hidden" name="deal_id" value="<?= $dealId ?? $formData['deal_id'] ?? '' ?>">
        
        <?php endif; ?>
        
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Title -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Tittel *</label>
                <input type="text" name="title" required
                       value="<?= e($formData['title'] ?? '') ?>"
                       class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Hva skal gjøres?">
            </div>
            
            
            <!-- Due date -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Forfallsdato *</label>
                <input type="date" name="due_date" required
                       value="<?= e($formData['due_date'] ?? '') ?>"
                       class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            
            <!-- Priority -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Prioritet</label>
                <select name="priority" class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="low" <?= ($formData['priority'] ?? 'medium') === 'low' ? 'selected' : '' ?>>Lav</option>
                    <option value="medium" <?= ($formData['priority'] ?? 'medium') === 'medium' ? 'selected' : '' ?>>Middels</option>
                    <option value="high" <?= ($formData['priority'] ?? '') === 'high' ? 'selected' : '' ?>>Høy</option>
                </select>
            </div>
            
            
            <?php if ($isEdit): ?
003e                
                <!-- Status (only for edit) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    
                    <select name="status" class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="open" <?= ($formData['status'] ?? 'open') === 'open' ? 'selected' : '' ?>>Åpen</option>
                        <option value="completed" <?= ($formData['status'] ?? '') === 'completed' ? 'selected' : '' ?>>Fullført</option>
                        <option value="postponed" <?= ($formData['status'] ?? '') === 'postponed' ? 'selected' : '' ?>>Utsatt</option>
                    </select>
                </div>
            
            
            
                
                <!-- Reminder (only for edit) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Påminnelse</label>
                    
                    <input type="datetime-local" name="reminder_date"
                           value="<?= e($formData['reminder_date'] ? str_replace(' ', 'T', $formData['reminder_date']) : '') ?>"
                           class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                    
                    
                    <p class="text-xs text-gray-500 mt-1">Valgfritt. Format: ÅÅÅÅ-MM-DD TT:MM</p>
                </div>
            
            
            <?php endif; ?>
            
            
            <!-- Description -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Beskrivelse</label>
                <textarea name="description" rows="4" class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"><?= e($formData['description'] ?? '') ?></textarea>
            </div>
        </div>
        
        
        <div class="mt-6 flex justify-between">
            <a href="<?= APP_URL ?>/tasks" class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600">Avbryt</a>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700"><?= $isEdit ? 'Oppdater' : 'Opprett' ?></button>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
