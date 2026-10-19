<?php
require __DIR__ . '/includes/functions.php';
require __DIR__ . '/includes/queries.php';
require __DIR__ . '/includes/auth.php';

$services = get_services();

// search form (GET): the choices are in the URL (services.php?max_price=20000&sort=price_asc#Trips),
// so a search can be bookmarked or shared. Each value is checked against the list of allowed choices.
$price_choices = ['' => 'كل الأسعار', '10000' => 'حتى 10 000 دج', '20000' => 'حتى 20 000 دج', '40000' => 'حتى 40 000 دج'];
$days_choices = ['' => 'كل المدد', '1' => 'يوم واحد', '3' => '3 أيام على الأكثر', '7' => 'أسبوع على الأكثر'];
$sort_choices = ['' => 'الترتيب الافتراضي', 'price_asc' => 'الأرخص أولاً', 'price_desc' => 'الأغلى أولاً', 'duration' => 'الأقصر أولاً'];

$search = [];
foreach (['max_price' => $price_choices, 'max_days' => $days_choices, 'sort' => $sort_choices] as $name => $choices) {
    $value = input($name, 'get');
    $search[$name] = array_key_exists($value, $choices) ? $value : '';   // an unknown value is ignored
}

$trips = search_trips(
    $search['max_price'] === '' ? null : (int) $search['max_price'],
    $search['max_days'] === '' ? null : (int) $search['max_days'],
    $search['sort']
);
$booking_trips = get_trips();   // the dialog offers every trip, not only the ones found

$page_title = 'سياحة | جميع الخدمات';
$active = 'services';
$styles = ['Hero', 'Services', 'Trip'];
$responsive = ['Hero', 'Services', 'Trip'];
$scripts = ['Read_More', 'Favourites', 'Booking'];
$return = 'services.php';

require __DIR__ . '/includes/header.php';
?>
    <main>
        <section id="Hero_ID">
            <div class="Hero">
                <img src="../../IMAGES/Oran.jpg" alt="وهران" />
            </div>
            <div class="Hero_Text">
                <h1>مرحبا بكم في موقعنا</h1>
                <p>نحن نقدم لكم أفضل الخدمات</p>
                <a href="contact_us.php" class="Show" id="Contact_Us_Btn_ID">تواصل معنا</a>
            </div>
        </section>
        <!--Services section starts here-->
        <section class="Services">
            <h1>جميع الخدمات</h1>
            <p class="Services_Intro">نرافقكم في كل مراحل رحلتكم، من الحجز إلى العودة.</p>
            <div class="Services_Grid">
                <?php foreach ($services as $service): ?>
                <div class="Service_Card">
                    <i class="<?= e($service['icon']) ?>"></i>
                    <h3><?= e($service['title']) ?></h3>
                    <p><?= e($service['description']) ?></p>
                    <span class="Service_Price"><?= e($service['price_label']) ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <!--Services section ends here-->
        <!--Search form starts here: GET, because it only reads data-->
        <form class="Search_Form" action="services.php#Trips" method="get">
            <h2><i class="fa-solid fa-magnifying-glass"></i> ابحث عن رحلة</h2>
            <?php foreach (['max_price' => ['السعر', $price_choices], 'max_days' => ['المدة', $days_choices], 'sort' => ['الترتيب', $sort_choices]] as $name => [$label, $choices]): ?>
            <label>
                <?= $label ?>
                <select name="<?= $name ?>">
                    <?php foreach ($choices as $value => $text): ?>
                    <option value="<?= e((string) $value) ?>" <?= (string) $value === $search[$name] ? 'selected' : '' ?>><?= $text ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <?php endforeach; ?>
            <button type="submit">بحث</button>
            <a href="services.php#Trips">إعادة تعيين</a>
        </form>
        <!--Search form ends here-->
<?php require __DIR__ . '/includes/trips_section.php'; ?>
<?php require __DIR__ . '/includes/booking_dialog.php'; ?>
    </main>
<?php require __DIR__ . '/includes/footer.php'; ?>
