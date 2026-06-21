<h1>Редактировать объявление</h1>

<?php if (in_array($ad->status_id, [3, 4])): ?>
    <div class="alert alert--info">
        После редактирования объявление будет отправлено на повторную модерацию.
    </div>
<?php elseif ($ad->status_id === 1): ?>
    <div class="alert alert--info">
        После сохранения объявление будет отправлено на модерацию.
    </div>
<?php endif; ?>

<form action="/ad/<?= $ad->ad_id ?>/edit" method="POST" enctype="multipart/form-data">
    <div class="form-group">
        <label for="category_id">Категория</label>
        <select name="category_id" id="category_id" required>
            <option value="">Выберите категорию</option>
            <?php foreach ($categories as $cat): ?>
                <optgroup label="<?= $this->escape($cat->name) ?>">
                    <option value="<?= $cat->category_id ?>" <?= (isset($_SESSION['old_input']['category_id']) && (int)$_SESSION['old_input']['category_id'] === $cat->category_id) ? 'selected' : '' ?>>
                        - Все
                    </option>
                    <?php
                    $subcategories = $categorySubcategories[$cat->category_id] ?? [];
                    foreach ($subcategories as $sub):
                    ?>
                        <option value="<?= $sub->category_id ?>" <?= $ad->category_id === $sub->category_id ? 'selected' : '' ?>>
                            <?= $this->escape($sub->name) ?>
                        </option>
                    <?php endforeach; ?>
                </optgroup>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="title">Название</label>
        <input type="text" name="title" id="title" value="<?= $this->escape($ad->title) ?>" required maxlength="255">
    </div>

    <div class="form-group">
        <label for="description">Описание</label>
        <textarea name="description" id="description" rows="6" required><?= $this->escape($ad->description) ?></textarea>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="price">Цена (₽)</label>
            <input type="number" name="price" id="price" value="<?= $this->escape($ad->price) ?>" required step="0.01" min="0">
        </div>

        <div class="form-group">
            <label for="item_condition_id">Состояние</label>
            <select name="item_condition_id" id="item_condition_id" required>
                <option value="">Выберите состояние</option>
                <?php foreach ($conditions as $condition): ?>
                    <option value="<?= $condition->item_condition_id ?>" <?= $ad->item_condition_id === $condition->item_condition_id ? 'selected' : '' ?>>
                        <?= $this->escape($condition->name) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label for="city_id">Город</label>
        <select name="city_id" id="city_id" required>
            <option value="">Выберите город</option>
            <?php foreach ($cities as $city): ?>
                <option value="<?= $city->city_id ?>" <?= $ad->city_id === $city->city_id ? 'selected' : '' ?>>
                    <?= $this->escape($city->name) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <?php if (!empty($images)): ?>
        <div class="form-group">
            <label>Текущие фотографии</label>
            <div class="image-list">
                <?php foreach ($images as $image): ?>
                    <div class="image-list__item">
                        <img src="/<?= $this->escape($image->image_path) ?>" alt="" class="image-list__thumb">
                        <label>
                            <input type="checkbox" name="delete_images[]" value="<?= $image->image_id ?>">
                            Удалить
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="form-group">
        <label for="images">Добавить фотографии</label>
        <input type="file" name="images[]" id="images" multiple accept="image/jpeg,image/png,image/gif,image/webp">
        <small class="form-hint">Допустимые форматы: JPG, PNG, GIF, WebP. Максимальный размер: 5 МБ.</small>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn--primary">Сохранить</button>
        <a href="/ad/<?= $ad->ad_id ?>" class="btn">Отмена</a>
    </div>
</form>