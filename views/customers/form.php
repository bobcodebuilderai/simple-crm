<?php 
require __DIR__ . '/../partials/header.php';

// Determine if creating or editing
$isEdit = !empty($formData['customer_id']);
$customer = $isEdit ? $formData : null;
?>

<div class="max-w-3xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-800 mb-6"><?= $pageTitle ?></h1>
    
    <form method="POST" class="bg-white rounded-lg shadow p-6">
        <?= csrfField() ?>
        
        <!-- BRReg lookup for new customers -->
        <?php if (!$isEdit): ?>
            <div class="mb-6 p-4 bg-blue-50 rounded">
                <label class="block text-sm font-medium text-gray-700 mb-2">Hent fra Brønnøysundregistrene (valgfritt)</label>
                <div class="flex gap-2">
                    <input type="text" id="org_number_lookup" placeholder="Skriv org.nummer (9 siffer)" 
                           class="flex-1 px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                           maxlength="20">
                    <button type="button" onclick="lookupBrreg()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        Hent data
                    </button>
                </div>
                <p id="brreg_status" class="text-sm mt-2"></p>
            </div>
        <?php endif; ?>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Company name -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Firmanavn *</label>
                <input type="text" name="company_name" required
                       value="<?= e($formData['company_name'] ?? '') ?>"
                       class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <!-- Org number -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Organisasjonsnummer</label>
                <input type="text" name="org_number" id="org_number"
                       value="<?= e($formData['org_number'] ?? '') ?>"
                       class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                       maxlength="20">
            </div>
            
            <!-- Phone -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Telefon</label>
                <input type="tel" name="phone"
                       value="<?= e($formData['phone'] ?? '') ?>"
                       class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <!-- Email -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">E-post</label>
                <input type="email" name="email"
                       value="<?= e($formData['email'] ?? '') ?>"
                       class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            
            <!-- Website -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nettside</label>
                <input type="url" name="website"
                       value="<?= e($formData['website'] ?? '') ?>"
                       placeholder="https://"
                       class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            
            <!-- Status -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="active" <?= ($formData['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Aktiv</option>
                    <option value="inactive" <?= ($formData['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inaktiv</option>
                </select>
            </div>
            
            
            <!-- Address -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Adresse</label>
                <input type="text" name="address" id="address"
                       value="<?= e($formData['address'] ?? '') ?>"
                       class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            
            <!-- Postal code -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Postnummer</label>
                <input type="text" name="postal_code" id="postal_code"
                       value="<?= e($formData['postal_code'] ?? '') ?>"
                       class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                       maxlength="10">
            </div>
            
            
            <!-- City -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Poststed</label>
                <input type="text" name="city" id="city"
                       value="<?= e($formData['city'] ?? '') ?>"
                       class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            
            <!-- Country -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Land</label>
                <input type="text" name="country" id="country"
                       value="<?= e($formData['country'] ?? 'Norge') ?>"
                       class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            
            <!-- Notes -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Notater</label>
                <textarea name="notes" rows="4" class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"><?= e($formData['notes'] ?? '') ?></textarea>
            </div>
        </div>
        
        
        <div class="mt-6 flex justify-between">
            <a href="<?= APP_URL ?>/customers" class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600">Avbryt</a>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700"><?= $isEdit ? 'Oppdater' : 'Opprett' ?></button>
        </div>
    </form>
</div>

<script>
function lookupBrreg() {
    const orgNumber = document.getElementById('org_number_lookup').value.replace(/\s/g, '');
    const statusEl = document.getElementById('brreg_status');
    
    if (orgNumber.length !== 9 || !/^\d{9}$/.test(orgNumber)) {
        statusEl.textContent = 'Ugyldig organisasjonsnummer (må være 9 siffer)';
        statusEl.className = 'text-sm mt-2 text-red-600';
        return;
    }
    
    statusEl.textContent = 'Henter data...';
    statusEl.className = 'text-sm mt-2 text-blue-600';
    
    fetch(`<?= APP_URL ?>/customers/brreg?org_number=${orgNumber}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                statusEl.textContent = data.error;
                statusEl.className = 'text-sm mt-2 text-red-600';
            } else {
                // Fill in the form
                document.getElementById('org_number').value = data.org_number || '';
                document.querySelector('[name="company_name"]').value = data.company_name || '';
                document.getElementById('address').value = data.address || '';
                document.getElementById('postal_code').value = data.postal_code || '';
                document.getElementById('city').value = data.city || '';
                
                statusEl.textContent = 'Data hentet!';
                statusEl.className = 'text-sm mt-2 text-green-600';
            }
        })
        .catch(error => {
            statusEl.textContent = 'Feil ved henting av data';
            statusEl.className = 'text-sm mt-2 text-red-600';
        });
}
</script>

<?php require __DIR__ . '/../partials/footer.php'; ?>
