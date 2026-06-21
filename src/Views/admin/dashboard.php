<h1>Админ-панель</h1>

<div class="admin-stats">
    <a href="/admin/users" class="admin-stat-card">
        <div class="admin-stat-card__number"><?= $this->escape((string)$stats['users']) ?></div>
        <div class="admin-stat-card__label">Пользователи</div>
    </a>
    <a href="/admin/advertisements" class="admin-stat-card">
        <div class="admin-stat-card__number"><?= $this->escape((string)$stats['ads']) ?></div>
        <div class="admin-stat-card__label">Объявления</div>
    </a>
    <a href="/admin/cities" class="admin-stat-card">
        <div class="admin-stat-card__number"><?= $this->escape((string)$stats['cities']) ?></div>
        <div class="admin-stat-card__label">Города</div>
    </a>
    <a href="/admin/categories" class="admin-stat-card">
        <div class="admin-stat-card__number"><?= $this->escape((string)$stats['categories']) ?></div>
        <div class="admin-stat-card__label">Категории</div>
    </a>
    <a href="/admin/item_conditions" class="admin-stat-card">
        <div class="admin-stat-card__number"><?= $this->escape((string)$stats['conditions']) ?></div>
        <div class="admin-stat-card__label">Состояния</div>
    </a>
    <a href="/admin/ad_chat_messages" class="admin-stat-card">
        <div class="admin-stat-card__number"><?= $this->escape((string)$stats['messages']) ?></div>
        <div class="admin-stat-card__label">Сообщения</div>
    </a>
</div>

<div class="admin-charts">
    <div class="admin-chart-card">
        <h2 class="admin-chart-card__title">Объявления по статусам</h2>
        <canvas id="chartByStatus" width="400" height="300"></canvas>
    </div>
    <div class="admin-chart-card">
        <h2 class="admin-chart-card__title">Объявления по категориям</h2>
        <canvas id="chartByCategory" width="400" height="300"></canvas>
    </div>
    <div class="admin-chart-card">
        <h2 class="admin-chart-card__title">Объявления по городам</h2>
        <canvas id="chartByCity" width="400" height="300"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.8/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // мы получаем данные в формате: 
    // byStatus: {name: string; count: number;}
    // byCategory: {name: string; count: number;}
    // byCity: {name: string; count: number;}
    // и потом превращаем его в данные которые принимает функция графиков.
    var chartData = <?= json_encode($chartData, JSON_UNESCAPED_UNICODE) ?>;

    function makeLabelsAndCounts(items) {
        return {
            labels: items.map(function (i) { return i.name; }),
            counts: items.map(function (i) { return parseInt(i.cnt, 10); }),
        };
    }

    // график по статусам
    var statusData = makeLabelsAndCounts(chartData.byStatus);
    new Chart(document.getElementById('chartByStatus'), {
        type: 'doughnut',
        data: {
            labels: statusData.labels,
            datasets: [{
                data: statusData.counts,
                borderWidth: 2,
            }],
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'right' },
            },
        },
    });

    // ── By Category (bar) ──
    var catData = makeLabelsAndCounts(chartData.byCategory);
    new Chart(document.getElementById('chartByCategory'), {
        type: 'bar',
        data: {
            labels: catData.labels,
            datasets: [{
                label: 'Количество',
                data: catData.counts,
                borderWidth: 1,
            }],
        },
        options: {
            responsive: true,
            indexAxis: 'y',
            plugins: {
                legend: { display: false },
            },
            scales: {
                x: {
                    ticks: { stepSize: 1 },
                },
            },
        },
    });

    // ── By City (bar) ──
    var cityData = makeLabelsAndCounts(chartData.byCity);
    new Chart(document.getElementById('chartByCity'), {
        type: 'bar',
        data: {
            labels: cityData.labels,
            datasets: [{
                label: 'Количество',
                data: cityData.counts,
                borderWidth: 1,
            }],
        },
        options: {
            responsive: true,
            indexAxis: 'y',
            plugins: {
                legend: { display: false },
            },
            scales: {
                x: {
                    ticks: { stepSize: 1 },
                },
            },
        },
    });
});
</script>
