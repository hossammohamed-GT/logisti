<?php
/**
 * بطاقة السائق - Driver Card
 * البيانات من جدول driver_cards ($card / $data).
 * الحقول الخاصة بالمنشأة والترخيص اختيارية: لو الأعمدة مش موجودة أو فاضية
 * بيتم استخدام القيم الافتراضية (نفس القيم القديمة) بدل ما الصفحة تفضى.
 */
$e = static fn($v) => htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8');

$val = static function (string $key, string $default = '') use ($card) {
    $v = trim((string)($card[$key] ?? ''));
    return $v !== '' ? $v : $default;
};

$entityName        = $val('entity_name', 'مؤسسة الإنجاز المتميزة للخدمات اللوجستية');
$entityId          = $val('entity_id', '7027992556');
$licenseNumber     = $val('license_number', '38/00014540');
$licenseType       = $val('license_type', 'نشاط النقل الخفيف للبضائع لأغراض تجارية (للغير - منشآت)');
$licenseCity       = $val('city', 'محافظة جدة');
$licenseIssueDate  = $val('license_issue_date', '1446/09/08');
$licenseExpiryDate = $val('license_expiry_date', '1449/10/12');
?>
        <!-- بيانات المنشأة/الفرد -->
        <section class="section">
          <h2 class="section-title">بيانات المنشأة/الفرد</h2>
          <div class="field-grid">
            <div class="field">
              <div class="label">الاسم</div>
              <div class="value"><?= $e($entityName) ?></div>
            </div>
            <div class="field">
              <div class="label">رقم هوية المنشأة</div>
              <div class="value"><?= $e($entityId) ?></div>
            </div>
          </div>
        </section>

        <!-- معلومات الترخيص الرئيسي -->
        <section class="section">
          <h2 class="section-title">معلومات الترخيص الرئيسي</h2>
          <div class="field-grid cols-3">
            <div class="field">
              <div class="label">رقم الترخيص</div>
              <div class="value"><?= $e($licenseNumber) ?></div>
            </div>
            <div class="field">
              <div class="label">نوع الترخيص/النشاط</div>
              <div class="value"><?= $e($licenseType) ?></div>
            </div>
            <div class="field">
              <div class="label">المدينة</div>
              <div class="value"><?= $e($licenseCity) ?></div>
            </div>
            <div class="field">
              <div class="label">تاريخ الإصدار</div>
              <div class="value"><?= $e($licenseIssueDate) ?></div>
            </div>
            <div class="field">
              <div class="label">تاريخ الإنتهاء</div>
              <div class="value"><?= $e($licenseExpiryDate) ?></div>
            </div>
          </div>
        </section>

<!-- بطاقة السائق -->
<section class="section">
  <h2 class="section-title">بطاقة السائق</h2>

  <div class="field-grid cols-3">

    <div class="field">
      <div class="label">رقم البطاقة</div>
      <div class="value">
        <?= $e($card['card_number']) ?>
      </div>
    </div>

    <div class="field">
      <div class="label">هوية السائق</div>
      <div class="value">
        <?= $e($card['driver_id_number']) ?>
      </div>
    </div>

    <div class="field">
      <div class="label">الاسم الأول</div>
      <div class="value">
        <?= $e($card['first_name_ar']) ?>
      </div>
    </div>

    <div class="field">
      <div class="label">اسم العائلة</div>
      <div class="value">
        <?= $e($card['family_name_ar']) ?>
      </div>
    </div>

    <div class="field">
      <div class="label">تاريخ إصدار البطاقة</div>
      <div class="value">
        <?= $e($card['issue_date']) ?>
      </div>
    </div>

    <div class="field">
      <div class="label">تاريخ إنتهاء البطاقة</div>
      <div class="value">
        <?= $e($card['expiry_date']) ?>
      </div>
    </div>

    <div class="field span-2">
      <div class="label">نوع البطاقة</div>
      <div class="value">
        <?= $e($card['card_type_ar']) ?>
      </div>
    </div>

  </div>
</section>

