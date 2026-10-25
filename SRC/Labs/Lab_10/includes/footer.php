<?php
// End of every public page: footer and scripts.
// The page sets $scripts: the scripts of Lab_4+ it needs (besides the menu and the scroll effects).
?>
    <footer>
        <div class="Footer">
            <div class="Footer_Top">
                <div>
                    <h1>سياحة</h1>
                    <p>اكتشف جمال الجزائر معنا</p>
                </div>
                <div>
                    <a href="https://www.facebook.com/" aria-label="Facebook"><i class="fa-brands fa-square-facebook"></i></a>
                    <a href="https://x.com/" aria-label="X"><i class="fa-brands fa-square-x-twitter"></i></a>
                    <a href="https://www.instagram.com/" aria-label="Instagram"><i class="fa-brands fa-square-instagram"></i></a>
                    <a href="https://www.youtube.com/" aria-label="YouTube"><i class="fa-brands fa-square-youtube"></i></a>
                </div>
            </div>

            <div class="Footer_Buttom">
                <div>
                    <h4>روابط سريعة</h4>
                    <a href="index.php">الرئيسية</a>
                    <a href="services.php">جميع الخدمات</a>
                    <a href="about_us.php">من نحن؟</a>
                    <a href="contact_us.php">إتصل بنا</a>
                </div>
                <div>
                    <h4>الوجهات</h4>
                    <?php foreach (get_destinations() as $destination): ?>
                    <a href="index.php#Destinations"><?= e($destination['title']) ?></a>
                    <?php endforeach; ?>
                    <a href="services.php#Trips">جميع الرحلات</a>
                </div>
                <div>
                    <h4>تواصل معنا</h4>
                    <p>شارع ديدوش مراد، الجزائر العاصمة</p>
                    <a href="tel:+213555000000"><span dir="ltr">+213 555 00 00 00</span></a>
                    <a href="mailto:contact@syaha.example">contact@syaha.example</a>
                </div>
                <div>
                    <h4>ساعات العمل</h4>
                    <p>الأحد - الخميس: 08:00 - 17:00</p>
                    <p>السبت: 09:00 - 13:00</p>
                    <p>الجمعة: مغلق</p>
                </div>
            </div>
            <p class="Footer_Copyright">© <?= date('Y') ?> سياحة. جميع الحقوق محفوظة.</p>
        </div>
    </footer>
    <!-- JavaScript (Lab 4) -->
    <?php foreach (['Drop_Down_Menu', ...$scripts, 'Scroll_Effects'] as $script): ?>
    <script src="../Lab_4+/<?= $script ?>.js"></script>
    <?php endforeach; ?>
</body>

</html>
