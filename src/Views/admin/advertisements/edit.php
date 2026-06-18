<h1>Редактировать объявление</h1>

<form action="/admin/advertisements/<?= $ad->ad_id ?>/update" method="POST" enctype="multipart/form-data">
    <div class="form-group">
        <label for="seller_id">Продавец</label>
        <select name="seller_id" id="seller_id" required>
            <option value="">Выберите продавца</option>
            <?php foreach ($users as $user): ?>
                <option value="<?= $user['user_id'] ?>" <?= (int)$user['user_id'] === $ad->seller_id ? 'selected' : '' ?>>
                    <?= $this->escape($user['email'] . ' (' . $user['first_name'] . ' ' . $user['last_name'] . ')') ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="title">Название</label>
        <input type="text" name="title" id="title" value="<?= $this->escape($ad->title) ?>" required maxlength="255">
    </div>

    <div class="form-group">
        <label for="description">Описание</label>
        <textarea name="description" id="description" rows="5" required><?= $this->escape($ad->description) ?></textarea>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="price">Цена (₽)</label>
            <input type="number" name="price" id="price" value="<?= $this->escape((string)$ad->price) ?>" required step="0.01" min="0">
        </div>
        <div class="form-group">
            <label for="status_id">Статус</label>
            <select name="status_id" id="status_id" required>
                <?php foreach ($statuses as $status): ?>
                    <option value="<?= $status['ad_status_id'] ?>" <?= (int)$status['ad_status_id'] == $ad->status_id ? 'selected' : '' ?>><?= $this->escape($status['name']) ?></option>
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
                        <option value="<?= $group['parent']->category_id ?>" <?= (int)$ad->category_id === null ? '' : ((int)$ad->category_id == $group['parent']->category_id ? 'selected' : '') ?>>
                            — Все
                        </option>
                        <?php foreach ($group['children'] as $sub): ?>
                            <option value="<?= $sub->category_id ?>" <?= (int)$sub->category_id == (int)$ad->category_id ? 'selected' : '' ?>><?= $this->escape($sub->name) ?></option>
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
                    <option value="<?= $condition->item_condition_id ?>" <?= $condition->item_condition_id == $ad->item_condition_id ? 'selected' : '' ?>><?= $this->escape($condition->name) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label for="city_id">Город</label>
        <select name="city_id" id="city_id" required>
            <option value="">Выберите город</option>
            <?php foreach ($cities as $city): ?>
                <option value="<?= $city->city_id ?>" <?= $city->city_id === $ad->city_id ? 'selected' : '' ?>><?= $this->escape($city->name) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <?php if (!empty($images)): ?>
        <div class="form-group">
            <label>Текущие изображения</label>
            <div class="image-list">
                <?php foreach ($images as $img): ?>
                    <div class="image-list__item">
                        <img src="/<?= $this->escape($img->image_path) ?>" alt="" class="image-list__thumb">
                        <label>
                            <input type="checkbox" name="delete_images[]" value="<?= $img->image_id ?>">
                            Удалить
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="form-group">
        <label for="images">Добавить изображения</label>
        <input type="file" name="images[]" id="images" multiple accept="image/jpeg,image/png,image/gif,image/webp">
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn--primary">Сохранить</button>
        <a href="/admin/advertisements" class="btn">Отмена</a>
    </div>
</form>