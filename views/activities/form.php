<?php 
require __DIR__ . '/../partials/header.php';

$isEdit = !empty($formData['activity_id']);
$activity = $isEdit ? $formData : null;
?>

<div class="max-w-3xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-800 mb-6"><?= $pageTitle ?></h1>
    
    <form method="POST" <?= !$isEdit ? 'enctype="multipart/form-data"' : '' ?> class="bg-white rounded-lg shadow p-6">
        <?= csrfField() ?>
        
        <input type="hidden" name="customer_id" value="<?= $customer['customer_id'] ?>">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Title -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Tittel *</label>
                <input type="text" name="title" required
                       value="<?= e($formData['title'] ?? '') ?>"
                       class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="F.eks. Møte med kunden">
            </div>
            
            
            <!-- Activity Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Aktivitetstype</label>
                <select name="activity_type" class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="customer_service" <?= ($formData['activity_type'] ?? '') === 'customer_service' ? 'selected' : '' ?>>Kundeservice</option>
                    <option value="meeting" <?= ($formData['activity_type'] ?? '') === 'meeting' ? 'selected' : '' ?>>Møte</option>
                    <option value="phone_call" <?= ($formData['activity_type'] ?? '') === 'phone_call' ? 'selected' : '' ?>>Telefonsamtale</option>
                    <option value="email" <?= ($formData['activity_type'] ?? '') === 'email' ? 'selected' : '' ?>>E-post</option>
                    <option value="contract" <?= ($formData['activity_type'] ?? '') === 'contract' ? 'selected' : '' ?>>Kontrakt</option>
                    <option value="follow_up" <?= ($formData['activity_type'] ?? '') === 'follow_up' ? 'selected' : '' ?>>Oppfølging</option>
                    <option value="note" <?= ($formData['activity_type'] ?? 'note') === 'note' ? 'selected' : '' ?>>Notat</option>
                    <option value="other" <?= ($formData['activity_type'] ?? '') === 'other' ? 'selected' : '' ?>>Annet</option>
                </select>
            </div>
            
            
            <!-- Contact -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kontaktperson</label>
                <select name="contact_id" class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Ingen spesifikk --</option>
                    
                    <?php foreach ($contacts as $contact): ?>
                        <option value="<?= $contact['contact_id'] ?>" <?= ($formData['contact_id'] ?? '') == $contact['contact_id'] ? 'selected' : '' ?>>
                            <?= e($contact['first_name'] . ' ' . $contact['last_name']) ?> <?= $contact['is_primary'] ? '(Primær)' : '' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            
            <!-- Activity Date -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Dato/tid</label>
                <input type="datetime-local" name="activity_date"
                       value="<?= e($formData['activity_date'] ? str_replace(' ', 'T', $formData['activity_date']) : date('Y-m-d\TH:i')) ?>"
                       class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            
            <?php if (!$isEdit): ?
003e                
                <!-- File Attachments (only for new activities) -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Vedlegg</label>
                    
                    <div id="file-inputs">
                        <input type="file" name="attachments[]" 
                               class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500 mb-2"
                               accept=".pdf,.docx,.xlsx,.png,.jpg,.jpeg,.txt">
                    </div>
                    
                    
                    <button type="button" onclick="addFileInput()" class="text-blue-600 text-sm hover:underline mt-1">+ Legg til flere filer</button>
                    
                    
                    <p class="text-xs text-gray-500 mt-1">Tillatte typer: PDF, Word, Excel, bilder. Maks <?= formatFileSize(MAX_FILE_SIZE) ?> per fil.</p>
                </div>
            
            
            <?php endif; ?>
            
            
            <!-- Description -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Beskrivelse</label>
                <textarea name="description" rows="6" class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"><?= e($formData['description'] ?? '') ?></textarea>
            </div>
        </div>
        
        
        <div class="mt-6 flex justify-between">
            <a href="<?= APP_URL ?>/customers/view/<?= $customer['customer_id'] ?>" class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600">Avbryt</a>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700"><?= $isEdit ? 'Oppdater' : 'Lagre' ?></button>
        </div>
    </form>
</div>

<script>
function addFileInput() {
    const container = document.getElementById('file-inputs');
    const input = document.createElement('input');
    input.type = 'file';
    input.name = 'attachments[]';
    input.className = 'w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500 mb-2';
    input.accept = '.pdf,.docx,.xlsx,.png,.jpg,.jpeg,.txt';
    container.appendChild(input);
}
</script>

<?php require __DIR__ . '/../partials/footer.php'; ?>
