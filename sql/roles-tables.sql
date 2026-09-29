-- =====================================================================
-- Logisti | أكواد MySQL الخاصة بالنوعين الجديدين (اختيارية)
-- البيانات حاليًا ثابتة داخل naql/static-data.php
-- شغّل هذا الملف فقط لو حبيت تنقل البيانات لقاعدة البيانات لاحقًا.
-- =====================================================================

-- ---------------------------------------------------------------------
-- 1) Operation Card - بطاقة تشغيل
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `operation_cards` (
  `id`               BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `token`            VARCHAR(64)  NOT NULL,
  -- بيانات المنشأة/الفرد
  `entity_name`      VARCHAR(255) NOT NULL,
  -- معلومات الترخيص الرئيسي
  `license_number`   VARCHAR(50)  NOT NULL,
  `license_type`     TEXT         NOT NULL,
  `city`             VARCHAR(100) NOT NULL,
  -- بيانات بطاقة التشغيل
  `card_number`      VARCHAR(50)  NOT NULL,
  `card_type`        TEXT         NOT NULL,
  `issue_date`       VARCHAR(20)  NOT NULL,   -- هجري كنص: 1448/03/19
  `expiry_date`      VARCHAR(20)  NOT NULL,
  `renewal_date`     VARCHAR(20)      NULL,
  -- معلومات المركبة
  `vehicle_model`    VARCHAR(150) NOT NULL,
  `plate_number`     VARCHAR(50)  NOT NULL,
  `manufacture_year` VARCHAR(10)  NOT NULL,
  `vehicle_color`    VARCHAR(50)  NOT NULL,
  `serial_number`    VARCHAR(50)  NOT NULL,
  `created_at`       TIMESTAMP NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `operation_cards`
(`token`, `entity_name`, `license_number`, `license_type`, `city`,
 `card_number`, `card_type`, `issue_date`, `expiry_date`, `renewal_date`,
 `vehicle_model`, `plate_number`, `manufacture_year`, `vehicle_color`, `serial_number`)
VALUES
('eddc5956-9eb8-49b7-ac12-380247e30099',
 'مؤسسة غايتكم للخدمات اللوجستية',
 '81/00000424',
 'نقل البضائع عبر الدراجات الآلية لأغراض تجارية',
 'محافظة جدة',
 '81-00003239',
 'نقل البضائع عبر الدراجة الآلية لأغراض تجارية',
 '1448/03/19',
 '1449/03/10',
 NULL,
 'دراجة نارية سويد',
 'د ب 6285',
 '2025',
 'رصاصي',
 '127771120');

-- ---------------------------------------------------------------------
-- 2) License - ترخيص
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `licenses` (
  `id`             BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `token`          VARCHAR(64)  NOT NULL,
  `activity`       TEXT         NOT NULL,   -- نشاط الترخيص = العنوان الأخضر أعلى الصفحة
  -- معلومات الترخيص الرئيسي
  `license_kind`   VARCHAR(50)  NOT NULL,   -- رئيسي / فرعي
  `license_number` VARCHAR(50)  NOT NULL,
  `request_status` VARCHAR(50)  NOT NULL,   -- نشط / منتهي ...
  `created_date`   VARCHAR(20)  NOT NULL,
  `expiry_date`    VARCHAR(20)  NOT NULL,
  -- معلومات السجل التجاري
  `cr_name`        VARCHAR(255) NOT NULL,
  `cr_number`      VARCHAR(50)  NOT NULL,
  `cr_status`      VARCHAR(50)  NOT NULL,
  `cr_expiry_date` VARCHAR(20)      NULL,
  `cr_activity`    VARCHAR(255) NOT NULL,
  -- بيانات المنشأة/الفرد
  `entity_name`    VARCHAR(255) NOT NULL,
  `entity_id`      VARCHAR(30)  NOT NULL,
  `region`         VARCHAR(100) NOT NULL,
  `city`           VARCHAR(100) NOT NULL,
  `address`        VARCHAR(255) NOT NULL,
  -- معلومات التواصل
  `contact_name`   VARCHAR(150) NOT NULL,
  `contact_mobile` VARCHAR(30)  NOT NULL,
  `contact_email`  VARCHAR(150) NOT NULL,
  `created_at`     TIMESTAMP NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `licenses`
(`token`, `activity`, `license_kind`, `license_number`, `request_status`,
 `created_date`, `expiry_date`, `cr_name`, `cr_number`, `cr_status`,
 `cr_expiry_date`, `cr_activity`, `entity_name`, `entity_id`, `region`,
 `city`, `address`, `contact_name`, `contact_mobile`, `contact_email`)
VALUES
('dcd63444-e888-454a-8c47-bbdbfdde04cd',
 'نقل البضائع عبر الدراجات الآلية لأغراض تجارية',
 'رئيسي',
 '81/00000424',
 'نشط',
 '1448-02-29',
 '1449-03-10',
 'مؤسسة غايتكم للخدمات اللوجستية',
 '1010650025',
 'نشط',
 NULL,
 '492311 - النقل الخفيف',
 'مؤسسة غايتكم للخدمات اللوجستية',
 '7017775516',
 'مكّة المكرّمة',
 'محافظة جدة',
 '23466, عمرو ابن سنان, الاجواد',
 'محمد بن غرامه الاسمري',
 '966559879689',
 'ghaya.com21@gmail.com');

-- ---------------------------------------------------------------------
-- 3) أعمدة اختيارية على جدول بطاقة السائق `driver_cards`
--    عشان بيانات المنشأة والترخيص في صفحة بطاقة السائق تبقى ديناميك كمان.
--    لو مشغلتهاش: الصفحة هتشتغل عادي بالقيم الافتراضية القديمة.
-- ---------------------------------------------------------------------
ALTER TABLE `driver_cards`
  ADD COLUMN `entity_name`         VARCHAR(255) NULL AFTER `expiry_date`,
  ADD COLUMN `entity_id`           VARCHAR(30)  NULL AFTER `entity_name`,
  ADD COLUMN `license_number`      VARCHAR(50)  NULL AFTER `entity_id`,
  ADD COLUMN `license_type`        TEXT         NULL AFTER `license_number`,
  ADD COLUMN `city`                VARCHAR(100) NULL AFTER `license_type`,
  ADD COLUMN `license_issue_date`  VARCHAR(20)  NULL AFTER `city`,
  ADD COLUMN `license_expiry_date` VARCHAR(20)  NULL AFTER `license_issue_date`;

-- ---------------------------------------------------------------------
-- 4) تحديث جدول `licenses` — ترتيب بيانات الترخيص الجديد + الحقول الإنجليزية
--    شغّل الجزء ده مرة واحدة بعد ما الجدول يكون اتعمل.
-- ---------------------------------------------------------------------
ALTER TABLE `licenses`
  ADD COLUMN `license_kind_en` VARCHAR(50)   NULL AFTER `license_kind`,
  ADD COLUMN `issue_date`      VARCHAR(20)   NULL AFTER `license_kind_en`,
  ADD COLUMN `entity_name_en`  VARCHAR(255)  NULL AFTER `entity_name`,
  ADD COLUMN `region_en`       VARCHAR(100)  NULL AFTER `region`,
  ADD COLUMN `city_en`         VARCHAR(100)  NULL AFTER `city`,
  ADD COLUMN `address_en`      VARCHAR(255)  NULL AFTER `address`,
  ADD COLUMN `activity_en`     VARCHAR(255)  NULL AFTER `activity`;

-- الأعمدة القديمة تبقى اختيارية عشان الفورم الجديد ما يبعتش كل حاجة
ALTER TABLE `licenses`
  MODIFY `created_date`   VARCHAR(20)  NULL,
  MODIFY `request_status` VARCHAR(50)  NULL,
  MODIFY `cr_name`        VARCHAR(255) NULL,
  MODIFY `cr_number`      VARCHAR(50)  NULL,
  MODIFY `cr_status`      VARCHAR(50)  NULL,
  MODIFY `cr_activity`    VARCHAR(255) NULL,
  MODIFY `contact_name`   VARCHAR(150) NULL,
  MODIFY `contact_mobile` VARCHAR(30)  NULL,
  MODIFY `contact_email`  VARCHAR(150) NULL;

-- مثال: تحديث بيانات الترخيص الحالي بالبيانات الجديدة
UPDATE `licenses` SET
  `license_number`  = '81/00000424',
  `license_kind`    = 'رئيسي',
  `license_kind_en` = 'Main',
  `issue_date`      = '2026-08-12',
  `expiry_date`     = '2027-08-12',
  `request_status`  = 'نشط',
  `entity_name`     = 'مؤسسة الإنجاز المتميزة للخدمات اللوجستية',
  `entity_name_en`  = 'Al Enjaz Distinguished Logistics Services Establishment',
  `entity_id`       = '7027992556',
  `cr_number`       = '4030454952',
  `region`          = 'مكة المكرمة',
  `region_en`       = 'Makkah',
  `city`            = 'محافظة جدة',
  `city_en`         = 'Jeddah',
  `address`         = 'مدائن الفهد، عبدالرحمن بن محمد القاسم',
  `address_en`      = 'Madan Alfad, Abd Alhamn Mohamed',
  `activity`        = 'نقل البضائع عبر الدراجات الآلية لأغراض تجارية',
  `activity_en`     = 'Motorcycle Road Freight for Commercial Purpose'
WHERE `token` = 'dcd63444-e888-454a-8c47-bbdbfdde04cd';
