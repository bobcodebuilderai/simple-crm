<?php
/**
 * Contacts List View
 */
?\>
<?php require __DIR__ . '/../partials/header.php'; ?\>

<div class="bg-white rounded-lg shadow-lg p-6"\>
    <div class="flex justify-between items-center mb-6"\>
        <div\>
            <h1 class="text-2xl font-bold text-gray-800"\><?= e($pageTitle) ?\></h1\>
            <p class="text-gray-600 mt-1"\>Organisasjonsnummer: <?= e($customer['org_number']) ?\></p\>
        </div\>
        
        <div class="flex space-x-3"\>
            <a href="<?= APP_URL ?\>/contacts/create?customer_id=<?= e($customer['customer_id']) ?\>" 
               class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"\>
                + Ny kontaktperson
            </a\>
        </div\>
    </div\>
    
    <?php if (empty($contacts)): ?\>
        <div class="text-center py-12 text-gray-500"\>
            <p class="text-lg"\>Ingen kontaktpersoner registrert</p\>
            <p class="mt-2"\>Klikk på "Ny kontaktperson" for å legge til den første.</p\>
        </div\>
    <?php else: ?\>
        <div class="overflow-x-auto"\>
            <table class="w-full"\>
                <thead class="bg-gray-50"\>
                    <tr\>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700"\>Navn</th\>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700"\>Stilling</th\>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700"\>E-post</th\>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700"\>Telefon</th\>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700"\>Primær</th\>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700"\>Handlinger</th\>
                    </tr\>
                </thead\>
                <tbody class="divide-y divide-gray-200"\>
                    <?php foreach ($contacts as $contact): ?\>
                        <tr class="hover:bg-gray-50"\>
                            <td class="px-4 py-3"\>
                                <div class="font-medium text-gray-900"\>
                                    <?= e($contact['first_name'] . ' ' . $contact['last_name']) ?\>
                                </div\>
                            </td\>
                            <td class="px-4 py-3 text-sm text-gray-600"\><?= e($contact['title'] ?? '-') ?\></td\>
                            <td class="px-4 py-3 text-sm"\>
                                <?php if ($contact['email']): ?\>
                                    <a href="mailto:<?= e($contact['email']) ?\>" class="text-blue-600 hover:underline"\>
                                        <?= e($contact['email']) ?\>
                                    </a\>
                                <?php else: ?\>-<?php endif; ?\>
                            </td\>
                            <td class="px-4 py-3 text-sm text-gray-600"\>
                                <?= e($contact['phone'] ?? ($contact['mobile'] ?? '-')) ?\>
                            </td\>
                            <td class="px-4 py-3 text-sm"\>
                                <?php if ($contact['is_primary']): ?\>
                                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs"\>Ja</span\>
                                <?php else: ?\>
                                    <span class="text-gray-400"\u003e-</span\>
                                <?php endif; ?\>
                            </td\>
                            <td class="px-4 py-3 text-sm"\u003e
                                <div class="flex space-x-2"\u003e
                                    <a href="<?= APP_URL ?\u003e/contacts/edit/<?= e($contact['id']) ?\u003e" 
                                       class="text-blue-600 hover:text-blue-800"\u003eRediger</a\>
                                    
                                    <form method="POST" action="<?= APP_URL ?\u003e/contacts/delete/<?= e($contact['id']) ?\u003e" 
                                          class="inline" onsubmit="return confirm('Er du sikker på at du vil slette denne kontaktpersonen?');"\>
                                        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?\>"\>
                                        <button type="submit" class="text-red-600 hover:text-red-800"\u003eSlett</button\>
                                    </form\>
                                </div\>
                            </td\>
                        </tr\>
                    <?php endforeach; ?\>
                </tbody\>
            </table\>
        </div\>
    <?php endif; ?\>
    
    <div class="mt-6"\>
        <a href="<?= APP_URL ?\>/customers/view/<?= e($customer['customer_id']) ?\>" 
           class="text-blue-600 hover:underline"\>
            ← Tilbake til kunde
        </a\>
    </div\>
</div\>

<?php require __DIR__ . '/../partials/footer.php'; ?\>