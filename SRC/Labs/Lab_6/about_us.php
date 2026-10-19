<?php
require __DIR__ . '/includes/functions.php';
require __DIR__ . '/includes/queries.php';

$team = get_team();

$page_title = 'سياحة | من نحن؟';
$active = 'about';
$styles = ['Hero', 'About_Us'];
$responsive = ['Hero', 'About_Us'];
$scripts = [];

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
        <div class="About_Us_Container">
            <h1>من نحن؟</h1>
            <p>
                سياحة وكالة أسفار جزائرية تأسست سنة 2015 بهدف التعريف بثراء الجزائر الطبيعي والثقافي. ننظم كل سنة
                عشرات الرحلات إلى المدن الساحلية والهضاب العليا والصحراء، لفائدة العائلات والطلبة والسياح الأجانب.
            </p>
            <h1>رسالتنا</h1>
            <p>
                أن نجعل اكتشاف الجزائر سهلاً وممتعاً وفي متناول الجميع، مع احترام البيئة ودعم الحرفيين والمرشدين
                المحليين في كل منطقة نزورها.
            </p>
            <h1>قيمنا</h1>
            <ul>
                <li>الأمانة: أسعار واضحة ومعلومات صادقة.</li>
                <li>الجودة: شركاء مختارون بعناية في النقل والإقامة.</li>
                <li>الاحترام: احترام السكان المحليين وتقاليدهم وطبيعتهم.</li>
                <li>الإصغاء: نصمم كل رحلة حسب رغبات زبائننا.</li>
            </ul>
        </div>
        <section class="Team">
            <h1>فريقنا</h1>
            <div class="Team_Grid">
                <?php foreach ($team as $member): ?>
                <div class="Team_Card">
                    <img src="<?= e(image_url($member['photo'])) ?>" alt="<?= e($member['full_name']) ?>" />
                    <h3><?= e($member['full_name']) ?></h3>
                    <p><?= e($member['role']) ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
    </main>
<?php require __DIR__ . '/includes/footer.php'; ?>
