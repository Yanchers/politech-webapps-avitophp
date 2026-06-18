<h1>Состояния товара</h1>

<div class="admin-toolbar">
    <a href="/admin/item_conditions/create" class="btn">+ Создать</a>
</div>

<?php if (empty($items)): ?>
    <p>Нет состояний.</p>
<?php else: ?>
    <form action="/admin/item_conditions/batch-delete" method="POST" class="admin-batch-form">
        <table class="admin-table">
            <thead>
                <tr>
                    <th><input type="checkbox" class="admin-check-all"></th>
                    <th>ID</th>
                    <th>Название</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><input type="checkbox" name="ids[]" value="<?= $item->item_condition_id ?>" class="admin-check-item"></td>
                        <td><?= $item->item_condition_id ?></td>
                        <td><?= $this->escape($item->name) ?></td>
                        <td class="admin-actions">
                            <a href="/admin/item_conditions/<?= $item->item_condition_id ?>/edit" class="btn btn--small">Ред.</a>
                            <a href="/admin/item_conditions/<?= $item->item_condition_id ?>/delete" class="btn btn--small btn--danger" onclick="return confirm('Удалить?')">Удалить</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <button type="submit" class="btn btn--danger btn--small" onclick="return confirm('Удалить выбранные?')">Удалить выбранные</button>
    </form>
<?php endif; ?>
