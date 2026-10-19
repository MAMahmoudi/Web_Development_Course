-- ------------------------------------------------------------------
-- Syaha: database of the demo website (Lab 5, users added in Lab 7)
-- Import it in phpMyAdmin (Import tab) or with:
--   mysql -u root < database/syaha.sql
-- Importing again deletes the tables and their data and recreates them.
-- ------------------------------------------------------------------

CREATE DATABASE IF NOT EXISTS syaha CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE syaha;

-- children first: a table cannot be dropped while another table references it
DROP TABLE IF EXISTS bookings;
DROP TABLE IF EXISTS contact_messages;
DROP TABLE IF EXISTS trips;
DROP TABLE IF EXISTS destinations;
DROP TABLE IF EXISTS services;
DROP TABLE IF EXISTS team_members;
DROP TABLE IF EXISTS users;

-- the three destinations of the home page (mosques, cities, Sahara)
CREATE TABLE destinations (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(100) NOT NULL,
  description TEXT NOT NULL,
  image_1 VARCHAR(255) NOT NULL,          -- path inside SRC/IMAGES
  image_2 VARCHAR(255) NOT NULL,
  sort_order INT NOT NULL DEFAULT 0
) ENGINE=InnoDB;

-- the trips that can be booked
CREATE TABLE trips (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(100) NOT NULL,
  description TEXT NOT NULL,
  image VARCHAR(255) NOT NULL,
  price INT UNSIGNED NOT NULL,            -- price per person, in dinars
  duration_days TINYINT UNSIGNED NOT NULL DEFAULT 1,
  is_published TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- the service cards of the services page
CREATE TABLE services (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  icon VARCHAR(60) NOT NULL,              -- Font Awesome classes
  title VARCHAR(100) NOT NULL,
  description TEXT NOT NULL,
  price_label VARCHAR(100) NOT NULL,
  sort_order INT NOT NULL DEFAULT 0
) ENGINE=InnoDB;

-- the team of the about page
CREATE TABLE team_members (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(100) NOT NULL,
  role VARCHAR(100) NOT NULL,
  photo VARCHAR(255) NOT NULL,
  sort_order INT NOT NULL DEFAULT 0
) ENGINE=InnoDB;

-- the accounts (Lab 7): the password is never stored, only its hash (password_hash)
CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(100) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,     -- UNIQUE: two accounts cannot share an e-mail
  password_hash VARCHAR(255) NOT NULL,    -- 60 characters today, 255 leaves room for future algorithms
  role ENUM('customer', 'admin') NOT NULL DEFAULT 'customer',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- one booking = one trip (many bookings can concern the same trip: one-to-many relationship)
CREATE TABLE bookings (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  trip_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NULL,              -- the account that booked (NULL: booked without an account)
  full_name VARCHAR(100) NOT NULL,
  email VARCHAR(255) NOT NULL,
  departure_date DATE NOT NULL,
  people TINYINT UNSIGNED NOT NULL,
  total_price INT UNSIGNED NOT NULL,      -- computed by the server, never taken from the browser
  status ENUM('pending', 'confirmed', 'cancelled') NOT NULL DEFAULT 'pending',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_bookings_trip FOREIGN KEY (trip_id) REFERENCES trips (id)
    ON DELETE RESTRICT ON UPDATE CASCADE,  -- a trip that has bookings cannot be deleted
  CONSTRAINT fk_bookings_user FOREIGN KEY (user_id) REFERENCES users (id)
    ON DELETE SET NULL                     -- an account deleted: its bookings stay, without account
) ENGINE=InnoDB;

-- the messages sent with the contact form
CREATE TABLE contact_messages (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(100) NOT NULL,
  email VARCHAR(255) NOT NULL,
  phone VARCHAR(20) NULL,                 -- optional
  subject VARCHAR(20) NOT NULL,
  message TEXT NOT NULL,
  is_read TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------------
-- Data
-- ------------------------------------------------------------------
INSERT INTO destinations (title, description, image_1, image_2, sort_order) VALUES
  ('المساجد', 'تحتوي الجزائر على العديد من المساجد والجوامع الإسلامية التي تتميز بالزخارف وفن العمارة المميزة ومن أهمها ما يلي: جامع كتشاوة: مسجد تاريخي موجود في عاصمة الجزائر، بُني المسجد في فترة الحكم العثماني في 1612 ميلادي وتمت توسعته لاحقاً على يد الباي حسن في العام 1792 ميلادي، أثناء الحكم الفرنسي للجزائر تحوّل هذا المسجد إلى كاتدرائية القديس فيليب، التي ظلت كذلك حتى عام 1962 ميلادي، وعندها تمت إعادة المسجد إلى وضعه الأصلي، وعلى الرغم من هذه التحولات فقد احتفظ المسجد بعظمته الأصلية وعراقته التاريخية، كما تم تسجيل المسجد في قائمة اليونسكو للتراث العالمي في عام 1992، كجزء من آثار مدينة الجزائر. المسجد الكبير :مسجد حسن باشا أو مسجد الباشا كما اشتهر باسم المسجد الكبير، بُني المسجد في عام 1796 م في مدينة وهران الجزائرية، في عهد حكم الباي محمد بن عثمان الكبير عندما أمره بابا حسن باشا بتشيده وذلك خلال الفترة العثمانية من القرن الثامن عشر. مسجد المنصورة :هو مسجد تاريخي مهدم يقع في مدينة المنصورة شمال غرب الجزائر في ولاية تلمسان، بُني المسجد في عهد بنو مرين في عام 1303 ميلادي وذلك على يد السلطان أبي يعقوب يوسف الناصر، وتمت زخرفة البوابة الرئيسية لاحقاً في عهد السلطان أبي الحسن المريني في عام 1336. الجامع الكبير في تلمسان : يُعتبر أحد أقدم وأهم المساجد التاريخية الموجودة في مدينة تلمسان في الجزائر، بُني في عهد السلطان يوسف بن تاشفين في عام 1082 ميلادي ومن ثم أُعيد بناؤه في عهد ابنه علي بن يوسف في عام 1136 ميلادي، ويعتبر أحد أفضل الأمثلة عن العمارة المرابطية.', 'Lab_2/Masjid.jpg', 'Lab_2/Masjid2.jpg', 1),
  ('المدن', '"تُعد الجزائر من الدول القليلة التي تمتلك مقومات هائلة في قطاع السياحة تجعلها إحدى الوجهات المفضلة للسياح حول العالم، فهناك العديد من الأماكن والمدن التي يمكن أن تقضي فيها عطلتك السنوية الصيفية في الجزائر، تمزج هذه المدن بين سحر الطبيعة الخلابة ورمال الشواطئ الذهبية وزرقة البحر والمعالم التاريخية الأثرية السياحية، التي تعكس تزاوجاً بين الطبيعة والتاريخ والبر والبحر،', 'Lab_2/City.jpg', 'Lab_2/City2.jpg', 2),
  ('الصحراء', 'تعد الصحراء الجزائرية ثاني أكبر صحراء في العالم، وذلك لشاسعتها، حيث تقارب مساحتها 2 مليون متر مربع وتحتوي على تراث طبيعي وثقافي وتاريخي غني ومتنوع. تعد السياحة الصحراوية جوهر المنتوج السياحي الجزائري. تحتوي الصحراء على 05 حظائر ثقافية مصنفة، وهي حظيرة الطاسيلي في ولاية ايليزي، الهقار في ولاية تمنراست، وادي ميزاب في ولاية غرداية والحظيرة الثقافية للأطلس الصحراوي في حظيرة توات، قورارة، تيديكلت.', 'Lab_2/Sahara.jpg', 'Lab_2/Sahara2.jpg', 3);

INSERT INTO trips (title, description, image, price, duration_days) VALUES
  ('المساجد', 'تحتوي الجزائر على العديد من المساجد والجوامع الإسلامية التي تتميز بالزخارف وفن العمارة المميزة ومن أهمها ما يلي: جامع كتشاوة: مسجد تاريخي موجود في عاصمة الجزائر، بُني المسجد في فترة الحكم العثماني في 1612 ميلادي وتمت توسعته لاحقاً على يد الباي حسن في العام 1792 ميلادي، أثناء الحكم الفرنسي للجزائر تحوّل هذا المسجد إلى كاتدرائية القديس فيليب، التي ظلت كذلك حتى عام 1962 ميلادي، وعندها تمت إعادة المسجد إلى وضعه الأصلي، وعلى الرغم من هذه التحولات فقد احتفظ المسجد بعظمته الأصلية وعراقته التاريخية، كما تم تسجيل المسجد في قائمة اليونسكو للتراث العالمي في عام 1992، كجزء من آثار مدينة الجزائر.', 'Lab_2/Masjid3.jpg', 8000, 1),
  ('المدن', 'تُعد الجزائر من الدول القليلة التي تمتلك مقومات هائلة في قطاع السياحة تجعلها إحدى الوجهات المفضلة للسياح حول العالم، فهناك العديد من الأماكن والمدن التي يمكن أن تقضي فيها عطلتك السنوية الصيفية في الجزائر، تمزج هذه المدن بين سحر الطبيعة الخلابة ورمال الشواطئ الذهبية وزرقة البحر والمعالم التاريخية الأثرية السياحية، التي تعكس تزاوجاً بين الطبيعة والتاريخ والبر والبحر،', 'Lab_2/City3.jpg', 15000, 3),
  ('الصحراء', 'تعد الصحراء الجزائرية ثاني أكبر صحراء في العالم، وذلك لشاسعتها، حيث تقارب مساحتها 2 مليون متر مربع وتحتوي على تراث طبيعي وثقافي وتاريخي غني ومتنوع. تعد السياحة الصحراوية جوهر المنتوج السياحي الجزائري. تحتوي الصحراء على 05 حظائر ثقافية مصنفة، وهي حظيرة الطاسيلي في ولاية ايليزي، الهقار في ولاية تمنراست، وادي ميزاب في ولاية غرداية والحظيرة الثقافية للأطلس الصحراوي في حظيرة توات، قورارة، تيديكلت.', 'Lab_2/Sahara3.jpg', 30000, 5);

INSERT INTO services (icon, title, description, price_label, sort_order) VALUES
  ('fa-solid fa-route', 'رحلات منظمة', 'برامج جاهزة لاكتشاف المدن الساحلية والمواقع الأثرية مع مرشد محلي.', 'ابتداءً من 15 000 دج', 1),
  ('fa-solid fa-hotel', 'حجز الفنادق', 'مجموعة مختارة من الفنادق والإقامات العائلية في أهم الوجهات.', 'ابتداءً من 6 000 دج / الليلة', 2),
  ('fa-solid fa-van-shuttle', 'النقل', 'نقل مريح من المطار وإلى مختلف المدن، مع سائقين ذوي خبرة.', 'حسب المسافة', 3),
  ('fa-solid fa-sun', 'رحلات الصحراء', 'مخيمات في تاغيت وجانت، جولات على الجمال وسهرات تحت النجوم.', 'ابتداءً من 30 000 دج', 4),
  ('fa-solid fa-mosque', 'السياحة الثقافية', 'زيارات المساجد العتيقة والقصبات والمتاحف مع شروحات تاريخية.', 'ابتداءً من 8 000 دج', 5),
  ('fa-solid fa-pen-ruler', 'رحلات حسب الطلب', 'صمموا رحلتكم بأنفسكم: الوجهة والمدة والميزانية، ونحن نتكفل بالباقي.', 'عرض سعر مجاني', 6);

INSERT INTO team_members (full_name, role, photo, sort_order) VALUES
  ('أمين بن علي', 'المدير العام', 'User_A.png', 1),
  ('سارة بوزيد', 'مسؤولة الرحلات', 'User_F.png', 2),
  ('يوسف حمدي', 'مرشد سياحي', 'User_A.png', 3),
  ('مريم سعدي', 'خدمة الزبائن', 'User_F.png', 4);

-- demo accounts: admin@syaha.example / Syaha_Admin1 and amine@example.com / Syaha_2025
INSERT INTO users (full_name, email, password_hash, role) VALUES
  ('مدير سياحة', 'admin@syaha.example', '$2y$10$yO1qwluIpBNvbYVXQEo02.wgJfQrvxz80vf3qj/NeG9ppy.4mGT2e', 'admin'),
  ('أمين بن علي', 'amine@example.com', '$2y$10$fCbx4JzpwZd1NVO6sr.kCuALpXIS7xO1t2bkSKBqYXmwgmXAb0ejm', 'customer');

INSERT INTO bookings (trip_id, full_name, email, departure_date, people, total_price, status) VALUES
  (3, 'كريم بلقاسم', 'karim@example.com', '2025-03-15', 2, 60000, 'confirmed'),
  (1, 'ليلى منصوري', 'leila@example.com', '2025-04-02', 4, 32000, 'pending');

INSERT INTO contact_messages (full_name, email, phone, subject, message) VALUES
  ('نسرين عمار', 'nesrine@example.com', '0661234567', 'booking', 'هل توجد رحلات إلى تاغيت خلال عطلة الربيع؟ نحن عائلة من خمسة أشخاص.');

-- the demo customer has one booking
INSERT INTO bookings (trip_id, user_id, full_name, email, departure_date, people, total_price, status) VALUES
  (2, 2, 'أمين بن علي', 'amine@example.com', '2025-05-10', 2, 30000, 'confirmed');
