<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="mb-6">
    <div class="flex items-center gap-4 mb-2">
        <a href="<?= APP_URL ?>/customers/view/<?= $activity['customer_id'] ?>" class="text-blue-600 hover:underline">← Tilbake til kunde</a>
    </div>
    
    <div class="flex justify-between items-start">
        <div>
            <h1 class="text-3xl font-bold text-gray-800"><?= e($activity['title']) ?></h1>
            <p class="text-gray-500"><?= e($activity['company_name']) ?></p>
        </div>
        
        <span class="px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-800">
            <?= getActivityTypeLabel($activity['activity_type']) ?>
        </span>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left column: Info -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">Detaljer</h2>
            
            <div class="space-y-3">
                <div>
                    <span class="text-gray-500 text-sm">Dato/tid:</span>
                    <p><?= formatDateTime($activity['activity_date']) ?></p>
                </div>
                
                
                <?php if ($activity['first_name']): ?
003e                    
                    <div>
                        <span class="text-gray-500 text-sm">Kontaktperson:</span>
                        <p><?= e($activity['first_name'] . ' ' . $activity['last_name']) ?></p>
                    </div>
                <?php endif; ?>
                
                
                <div>
                    <span class="text-gray-500 text-sm">Opprettet:</span>
                    <p><?= formatDateTime($activity['created_at']) ?></p>
                </div>
            </div>
            
            
            <div class="mt-6 flex gap-2">
                <a href="<?= APP_URL ?>/activities/edit/<?= $activity['activity_id'] ?>" class="flex-1 text-center bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Rediger</a>
            </div>
        </div>
    </div>
    
    
    
    <!-- Right column: Description and Attachments -->
    <div class="lg:col-span-2">
        <!-- Description -->
        <?php if ($activity['description']): ?
003e            
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-lg font-semibold mb-4">Beskrivelse</h2>
                
                
                <p class="text-gray-700 whitespace-pre-wrap"><?= nl2br(e($activity['description'])) ?></p>
            </div>
        <?php endif; ?>
        
        
        
        <!-- Attachments -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">Vedlegg (<?= count($attachments) ?>)</h2>
            
            
            <?php if (empty($attachments)): ?
                
                <p class="text-gray-500">Ingen vedlegg</p>
            <?php else: ?
                
                <div class="space-y-2">
                    <?php foreach ($attachments as $attachment): ?
                        
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                            
                            <div class="flex items-center">
                                <span class="text-2xl mr-3"><?= getFileIcon($attachment['file_type']) ?></span>
                                
                                <div>
                                    <p class="font-medium"><?= e($attachment['original_name']) ?></p>
                                    
                                    <p class="text-sm text-gray-500"><?= formatFileSize($attachment['file_size']) ?> • <?= formatDate($attachment['uploaded_at']) ?></p>
                                </div>
                            </div>
                            
                            
                            <div class="flex gap-2">
                                <a href="<?= APP_URL ?>/activities/downloadAttachment/<?= $attachment['attachment_id'] ?>" 
                                   class="text-blue-600 hover:underline text-sm">Last ned</a>
                                
                                
                                <form method="POST" action="<?= APP_URL ?>/activities/deleteAttachment/<?= $attachment['attachment_id'] ?>" class="inline" onsubmit="return confirm('Slette dette vedlegget?')">
                                    <?= csrfField() ?>
                                    
                                    <button type="submit" class="text-red-600 hover:underline text-sm">Slett</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?
003e                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
