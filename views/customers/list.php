<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="mb-6 flex justify-between items-center">
    <h1 class="text-3xl font-bold text-gray-800"><?= $pageTitle ?></h1>
    <a href="<?= APP_URL ?>/customers/create" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
        + Ny kunde
    </a>
</div>

<!-- Search and filter -->
<div class="bg-white rounded-lg shadow p-4 mb-6">
    <form method="GET" class="flex gap-4">
        <input type="text" name="search" placeholder="Søk etter firma, kundenummer..." 
               value="<?= e($_GET['search'] ?? '') ?>"
               class="flex-1 px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
        
        <select name="status" class="px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Alle statuser</option>
            <option value="active" <?= ($_GET['status'] ?? '') === 'active' ? 'selected' : '' ?>>Aktiv</option>
            <option value="inactive" <?= ($_GET['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inaktiv</option>
        </select>
        
        <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">Søk</button>
    </form>
</div>

<!-- Customers table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <?php if (empty($customers)): ?>
        <div class="p-8 text-center text-gray-500">
            <p class="text-lg">Ingen kunder funnet</p>
            <p class="mt-2"><a href="<?= APP_URL ?>/customers/create" class="text-blue-600 hover:underline">Opprett første kunde</a></p>
        </div>
    <?php else: ?>
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kundenummer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Firmanavn</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kontakt</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sted</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Handlinger</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                <?php foreach ($customers as $customer): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-medium text-gray-900"><?= e($customer['customer_number']) ?></span>
                        </td>
                        <td class="px-6 py-4">
                            <a href="<?= APP_URL ?>/customers/view/<?= $customer['customer_id'] ?>" 
                               class="text-blue-600 hover:text-blue-800 font-medium"><?= e($customer['company_name']) ?></a>
                            <?php if ($customer['org_number']): ?
                                <span class="text-xs text-gray-400 block">Org.nr: <?= e($customer['org_number']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm"><?= e($customer['phone'] ?? '-') ?></span>
                            <?php if ($customer['email']): ?
                                <span class="text-xs text-gray-400 block"><?= e($customer['email']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm"><?= e($customer['city'] ?? '-') ?></span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full <?= $customer['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' ?>">
                                <?= $customer['status'] === 'active' ? 'Aktiv' : 'Inaktiv' ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="<?= APP_URL ?>/customers/view/<?= $customer['customer_id'] ?>" class="text-blue-600 hover:text-blue-800 mr-3">Vis</a>
                            <a href="<?= APP_URL ?>/customers/edit/<?= $customer['customer_id'] ?>" class="text-gray-600 hover:text-gray-800">Rediger</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
