<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800"><?= $pageTitle ?></h1>
</div>

<?php if (empty($deals)): ?
    <div class="bg-white rounded-lg shadow p-8 text-center">
        <p class="text-gray-500 text-lg">Ingen deals funnet</p>
        <p class="mt-2"><a href="<?= APP_URL ?>/deals/create" class="text-blue-600 hover:underline">Opprett første deal</a></p>
    </div>
<?php else: ?
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tittel</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kunde</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Verdi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Forventet avslutning</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Handlinger</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                <?php foreach ($deals as $deal): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <a href="<?= APP_URL ?>/deals/view/<?= $deal['deal_id'] ?>" class="text-blue-600 hover:underline font-medium">
                                <?= e($deal['title']) ?>
                            </a>
                        </td>
                        
                        <td class="px-6 py-4">
                            <span class="text-sm"><?= e($deal['company_name']) ?></span>
                        </td>
                        
                        <td class="px-6 py-4">
                            <span class="font-medium"><?= $deal['value'] ? formatCurrency($deal['value']) : '-' ?></span>
                        </td>
                        
                        <td class="px-6 py-4">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full <?= getDealStatusColor($deal['status']) ?>">
                                <?= getDealStatusLabel($deal['status']) ?>
                            </span>
                        </td>
                        
                        <td class="px-6 py-4">
                            <span class="text-sm"><?= $deal['expected_close_date'] ? formatDate($deal['expected_close_date']) : '-' ?></span>
                        </td>
                        
                        <td class="px-6 py-4 text-right">
                            <a href="<?= APP_URL ?>/deals/view/<?= $deal['deal_id'] ?>" class="text-blue-600 hover:underline mr-3">Vis</a>
                            
                            <a href="<?= APP_URL ?>/deals/edit/<?= $deal['deal_id'] ?>" class="text-gray-600 hover:underline">Rediger</a>
                        </td>
                    </tr>
                <?php endforeach; ?
003e            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/../partials/footer.php'; ?>
