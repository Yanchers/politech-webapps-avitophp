<h1>Пользователи</h1>

<div class="admin-toolbar">
    <a href="/admin/users/create" class="btn">+ Создать</a>
</div>

<?php if (empty($items)): ?>
    <p>Нет пользователей.</p>
<?php else: ?>
    <form action="/admin/users/batch-delete" method="POST" class="admin-batch-form">
        <table class="admin-table">
            <thead>
                <tr>
                    <th><input type="checkbox" class="admin-check-all"></th>
                    <th>ID</th>
                    <th>Email</th>
                    <th>Телефон</th>
                    <th>Имя</th>
                    <th>Фамилия</th>
                    <th>Роль</th>
                    <th>Город</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><input type="checkbox" name="ids[]" value="<?= $item['user_id'] ?>" class="admin-check-item"></td>
                        <td><?= $item['user_id'] ?></td>
                        <td><?= $this->escape($item['email']) ?></td>
                        <td><?= $this->escape($item['phone']) ?></td>
                        <td><?= $this->escape($item['first_name']) ?></td>
                        <td><?= $this->escape($item['last_name']) ?></td>
                        <td><?= $this->escape($item['role_name']) ?></td>
                        <td><?= $this->escape($item['city_name']) ?></td>
                        <td class="admin-actions">
                            <a href="/admin/users/<?= $item['user_id'] ?>/edit" class="btn btn--small">Ред.</a>
                            <a href="/admin/users/<?= $item['user_id'] ?>/delete" class="btn btn--small btn--danger" onclick="return confirm('Удалить?')">Удалить</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <button type="submit" class="btn btn--danger btn--small" onclick="return confirm('Удалить выбранные?')">Удалить выбранные</button>
    </form>
<?php endif; ?>
