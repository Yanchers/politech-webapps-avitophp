<h1>Создать объявление</h1>

<form action="/ad/create" method="POST" enctype="multipart/form-data">
    <div class="form-group">
        <label for="category_id">Категория</label>
        <select name="category_id" id="category_id" required>
            <option value="">Выберите категорию</option>
            <?php foreach ($categories as $cat): ?>
                <optgroup label="<?= $this->escape($cat->name) ?>">
                    <?php
                    $subcategories = (new \App\Repositories\CategoryRepository())->findByParentId($cat->category_id);
                    foreach ($subcategories as $sub):
                    ?>
                        <option value="<?= $sub->category_id ?>" <?= (isset($_SESSION['old_input']['category_id']) && (int)$_SESSION['old_input']['category_id'] === $sub->category_id) ? 'selected' : '' ?>>
                            <?= $this->escape($sub->name) ?>
                        </option>
                    <?php endforeach; ?>
                </optgroup>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="title">Название</label>
        <input type="text" name="title" id="title" value="<?= $this->escape($_SESSION['old_input']['title'] ?? '') ?>" required maxlength="255">
    </div>

    <div class="form-group">
        <label for="description">Описание</label>
        <textarea name="description" id="description" rows="6" required><?= $this->escape($_SESSION['old_input']['description'] ?? '') ?></textarea>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="price">Цена (₽)</label>
            <input type="number" name="price" id="price" value="<?= $this->escape($_SESSION['old_input']['price'] ?? '') ?>" required step="0.01" min="0">
        </div>

        <div class="form-group">
            <label for="item_condition_id">Состояние</label>
            <select name="item_condition_id" id="item_condition_id" required>
                <option value="">Выберите состояние</option>
                <?php foreach ($conditions as $condition): ?>
                    <option value="<?= $condition->item_condition_id ?>" <?= (isset($_SESSION['old_input']['item_condition_id']) && (int)$_SESSION['old_input']['item_condition_id'] === $condition->item_condition_id) ? 'selected' : '' ?>>
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
                <option value="<?= $city->city_id ?>" <?= (isset($_SESSION['old_input']['city_id']) && (int)$_SESSION['old_input']['city_id'] === $city->city_id) ? 'selected' : '' ?>>
                    <?= $this->escape($city->name) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="images">Фотографии (можно выбрать несколько)</label>
        <input type="file" name="images[]" id="images" multiple accept="image/jpeg,image/png,image/gif,image/webp">
        <small class="form-hint">Допустимые форматы: JPG, PNG, GIF, WebP. Максимальный размер: 5 МБ.</small>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn--primary">Опубликовать</button>
        <a href="/" class="btn">Отмена</a>
    </div>
</form>
