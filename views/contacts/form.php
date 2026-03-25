<?php
/**
 * Contact Form View
 */
?\>
<?php require __DIR__ . '/../partials/header.php'; ?\>

<div class="bg-white rounded-lg shadow-lg p-6 max-w-2xl mx-auto"\>
    <h1 class="text-2xl font-bold text-gray-800 mb-6"\><?= e($pageTitle) ?\></h1\>
    
    <form method="POST" class="space-y-4"\>
        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?\>"\>
        <input type="hidden" name="customer_id" value="<?= e($customer['customer_id']) ?\>"\>
        
        <div class="grid grid-cols-2 gap-4"\>
            <div\>
                <label class="block text-sm font-medium text-gray-700 mb-1"\>Fornavn *</label\>
                <input type="text" name="first_name" required
                       value="<?= e($formData['first_name'] ?? '') ?\>"
                       class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"\>
            </div\>
            
            <div\>
                <label class="block text-sm font-medium text-gray-700 mb-1"\>Etternavn *</label\>
                <input type="text" name="last_name" required
                       value="<?= e($formData['last_name'] ?? '') ?\>"
                       class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"\>
            </div\>
        </div\>
        
        <div\>
            <label class="block text-sm font-medium text-gray-700 mb-1"\>Stilling/tittel</label\>
            <input type="text" name="title"
                   value="<?= e($formData['title'] ?? '') ?\>"
                   class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"\>
        </div\>
        
        <div class="grid grid-cols-2 gap-4"\>
            <div\>
                <label class="block text-sm font-medium text-gray-700 mb-1"\>E-post</label\>
                <input type="email" name="email"
                       value="<?= e($formData['email'] ?? '') ?\>"
                       class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"\>
            </div\>
            
            <div\>
                <label class="block text-sm font-medium text-gray-700 mb-1"\>Telefon</label\>
                <input type="text" name="phone"
                       value="<?= e($formData['phone'] ?? '') ?\>"
                       class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"\>
            </div\>
        </div\>
        
        <div\>
            <label class="block text-sm font-medium text-gray-700 mb-1"\>Mobil</label\>
            <input type="text" name="mobile"
                   value="<?= e($formData['mobile'] ?? '') ?\>"
                   class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"\>
        </div\>
        
        <div\>
            <label class="flex items-center"\>
                <input type="checkbox" name="is_primary" value="1"
                       <?= !empty($formData['is_primary']) ? 'checked' : '' ?\>
                       class="mr-2"\>
                <span class="text-sm text-gray-700"\>Primær kontaktperson</span\>
            </label\>
        </div\>
        
        <div\>
            <label class="block text-sm font-medium text-gray-700 mb-1"\>Notater</label\>
            <textarea name="notes" rows="3"
                      class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"><?= e($formData['notes'] ?? '') ?\></textarea\>
        </div\>
        
        <div class="flex justify-between pt-4"\>
            <a href="<?= APP_URL ?\>/customers/view/<?= e($customer['customer_id']) ?\>" 
               class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400"\>
                Avbryt
            </a\>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"\>
                Lagre
            </button\>
        </div\>
    </form\>
</div\>

<?php require __DIR__ . '/../partials/footer.php'; ?\>