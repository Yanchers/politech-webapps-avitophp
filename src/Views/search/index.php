<div class="search-page">
    <div class="search-page__sidebar">
        <form action="/search" method="GET" class="filter-form">
            <div class="filter-form__section">
                <h3>Поиск</h3>
                <input type="text" name="q" class="filter-form__input" placeholder="Что ищете?" value="<?= $this->escape($search) ?>">
            </div>

            <div class="filter-form__section">
                <h3>Категория</h3>
                <select name="category_id" class="filter-form__select">
                    <option value="">Все категории</option>
                    <?php foreach ($categories as $cat):
                        $subcategories = $categorySubcategories[$cat->category_id] ?? [];
                    ?>
                        <optgroup label="<?= $this->escape($cat->name) ?>">
                            <?php if (!empty($subcategories)): ?>
                                <option value="<?= $cat->category_id ?>" <?= $selectedCategory === null ? '' : ($selectedCategory === $cat->category_id ? 'selected' : '') ?>>
                                    — Все
                                </option>
                            <?php endif; ?>
                            <?php foreach ($subcategories as $sub): ?>
                                <option value="<?= $sub->category_id ?>" <?= $selectedCategory === $sub->category_id ? 'selected' : '' ?>>
                                    <?= $this->escape($sub->name) ?>
                                </option>
                            <?php endforeach; ?>
                        </optgroup>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-form__section">
                <h3>Город</h3>
                <select name="city_id" class="filter-form__select">
                    <option value="">Все города</option>
                    <?php foreach ($cities as $city): ?>
                        <option value="<?= $city->city_id ?>" <?= $selectedCity === $city->city_id ? 'selected' : '' ?>>
                            <?= $this->escape($city->name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-form__section">
                <h3>Состояние</h3>
                <select name="item_condition_id" class="filter-form__select">
                    <option value="">Любое</option>
                    <?php foreach ($conditions as $condition): ?>
                        <option value="<?= $condition->item_condition_id ?>" <?= $selectedCondition === $condition->item_condition_id ? 'selected' : '' ?>>
                            <?= $this->escape($condition->name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-form__section">
                <h3>Цена</h3>
                <div class="filter-form__price">
                    <input type="number" name="price_min" class="filter-form__input" placeholder="от" value="<?= $priceMin !== null ? $this->escape((string) $priceMin) : '' ?>" min="0">
                    <span>—</span>
                    <input type="number" name="price_max" class="filter-form__input" placeholder="до" value="<?= $priceMax !== null ? $this->escape((string) $priceMax) : '' ?>" min="0">
                </div>
            </div>

            <div class="filter-form__section">
                <h3>Сортировка</h3>
                <select name="sort" class="filter-form__select">
                    <option value="date_desc" <?= $sort === 'date_desc' ? 'selected' : '' ?>>Сначала новые</option>
                    <option value="date_asc" <?= $sort === 'date_asc' ? 'selected' : '' ?>>Сначала старые</option>
                    <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Сначала дешёвые</option>
                    <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Сначала дорогие</option>
                </select>
            </div>

            <button type="submit" class="btn btn--primary filter-form__submit">Применить</button>
            <a href="/search" class="btn filter-form__reset">Сбросить</a>
        </form>
    </div>

    <div class="search-page__content">
        <div class="search-page__header">
            <h1><?= $search ? "Результаты по запросу «{$this->escape($search)}»" : 'Все объявления' ?></h1>
            <span class="search-page__count">Найдено: <?= $total ?></span>
        </div>

        <?php if (empty($ads)): ?>
            <div class="search-page__empty">
                <p>По вашему запросу ничего не найдено.</p>
                <a href="/search" class="btn">Сбросить фильтры</a>
            </div>
        <?php else: ?>
            <div class="ads-grid">
                <?php foreach ($ads as $ad): ?>
                    <div class="ad-card">
                        <a href="/ad/<?= $ad->ad_id ?>" class="ad-card__link">
                            <div class="ad-card__image">
                                <?php if (!empty($ad->first_image_path)): ?>
                                    <img src="/<?= $this->escape($ad->first_image_path) ?>" alt="<?= $this->escape($ad->title) ?>">
                                <?php else: ?>
                                    <div class="ad-card__no-image">Нет фото</div>
                                <?php endif; ?>
                            </div>
                            <div class="ad-card__body">
                                <h3 class="ad-card__title"><?= $this->escape($ad->title) ?></h3>
                                <div class="ad-card__price"><?= number_format($ad->price, 0, ',', ' ') ?> ₽</div>
                                <div class="ad-card__meta">
                                    <span><?= $this->escape($ad->city_name) ?></span>
                                    <span><?= $this->escape($ad->condition_name) ?></span>
                                    <span><?= date('d.m.Y', strtotime($ad->created_at)) ?></span>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="<?= $this->buildSearchUrl(['page' => $page - 1]) ?>" class="pagination__link">&laquo; Назад</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <?php if ($i === $page): ?>
                            <span class="pagination__link pagination__link--active"><?= $i ?></span>
                        <?php elseif ($i === 1 || $i === $totalPages || abs($i - $page) <= 2): ?>
                            <a href="<?= $this->buildSearchUrl(['page' => $i]) ?>" class="pagination__link"><?= $i ?></a>
                        <?php elseif (abs($i - $page) === 3): ?>
                            <span class="pagination__dots">...</span>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                        <a href="<?= $this->buildSearchUrl(['page' => $page + 1]) ?>" class="pagination__link">Вперёд &raquo;</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
