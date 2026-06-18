<h1>Создать объявление</h1>

<form action="/admin/advertisements/store" method="POST" enctype="multipart/form-data">
    <div class="form-group">
        <label for="seller_id">Продавец</label>
        <select name="seller_id" id="seller_id" required>
            <option value="">Выберите продавца</option>
            <?php foreach ($users as $user): ?>
                <option value="<?= $user['user_id'] ?>"><?= $this->escape($user['email'] . ' (' . $user['first_name'] . ' ' . $user['last_name'] . ')') ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="title">Название</label>
        <input type="text" name="title" id="title" required maxlength="255">
    </div>

    <div class="form-group">
        <label for="description">Описание</label>
        <textarea name="description" id="description" rows="5" required></textarea>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="price">Цена (₽)</label>
            <input type="number" name="price" id="price" required step="0.01" min="0">
        </div>
        <div class="form-group">
            <label for="status_id">Статус</label>
            <select name="status_id" id="status_id" required>
                <?php foreach ($statuses as $status): ?>
                    <option value="<?= $status['ad_status_id'] ?>" <?= $status['ad_status_id'] === 3 ? 'selected' : '' ?>><?= $this->escape($status['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="category_id">Категория</label>
            <select name="category_id" id="category_id" required>
                <option value="">Выберите категорию</option>
                <?php foreach ($categories as $group): ?>
                    <optgroup label="<?= $this->escape($group['parent']->name) ?>">
                        <option value="<?= $group['parent']->category_id ?>">
                            — Все
                        </option>
                        <?php foreach ($group['children'] as $sub): ?>
                            <option value="<?= $sub->category_id ?>"><?= $this->escape($sub->name) ?></option>
                        <?php endforeach; ?>
                    </optgroup>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="item_condition_id">Состояние</label>
            <select name="item_condition_id" id="item_condition_id" required>
                <option value="">Выберите состояние</option>
                <?php foreach ($conditions as $condition): ?>
                    <option value="<?= $condition->item_condition_id ?>"><?= $this->escape($condition->name) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label for="city_id">Город</label>
        <select name="city_id" id="city_id" required>
            <option value="">Выберите город</option>
            <?php foreach ($cities as $city): ?>
                <option value="<?= $city->city_id ?>"><?= $this->escape($city->name) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="images">Фотографии</label>
        <input type="file" name="images[]" id="images" multiple accept="image/jpeg,image/png,image/gif,image/webp">
        <small class="form-hint">Допустимые форматы: JPG, PNG, GIF, WebP. Макс. 5 МБ.</small>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn--primary">Создать</button>
        <a href="/admin/advertisements" class="btn">Отмена</a>
    </div>
</form>