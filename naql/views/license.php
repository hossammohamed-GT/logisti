<?php
/** الترخيص - License | $data = صف من جدول licenses */

$e = static function ($v) {
    return htmlspecialchars((string)($v === null ? '' : $v), ENT_QUOTES, 'UTF-8');
};

/** قيمة الحقل (مع بديل لو العمود قديم/فاضي) */
$f = static function ($key, $fallbackKey = null) use ($data) {
    $v = isset($data[$key]) ? trim((string)$data[$key]) : '';
    if ($v === '' && $fallbackKey !== null) {
        $v = isset($data[$fallbackKey]) ? trim((string)$data[$fallbackKey]) : '';
    }
    return $v;
};

/**
 * عرض الترجمة الإنجليزية تحت القيمة العربية.
 * خليها true لو عايز الإنجليزي يبان في الصفحة.
 */
$SHOW_ENGLISH = false;

/** سطر إنجليزي صغير تحت القيمة */
$en = static function ($key) use ($data, $e, $SHOW_ENGLISH) {
    if (!$SHOW_ENGLISH) {
        return '';
    }
    $v = isset($data[$key]) ? trim((string)$data[$key]) : '';
    if ($v === '') {
        return '';
    }
    return '<div style="font-size:12px;color:var(--text-grey);direction:ltr;text-align:right;margin-top:2px;">'
        . $e($v) . '</div>';
};
?>
        <!-- معلومات الترخيص الرئيسي -->
        <section class="section">
          <h2 class="section-title">معلومات الترخيص الرئيسي</h2>
          <div class="field-grid cols-3">
            <div class="field">
              <div class="label">النوع</div>
              <div class="value"><?= $e($f('license_kind')) ?><?= $en('license_kind_en') ?></div>
            </div>
            <div class="field">
              <div class="label">رقم الترخيص</div>
              <div class="value"><?= $e($f('license_number')) ?></div>
            </div>
            <div class="field">
              <div class="label">حالة الطلب</div>
              <div class="value"><?= $e($f('request_status')) ?></div>
            </div>
            <div class="field">
              <div class="label">تاريخ الإصدار</div>
              <div class="value"><?= $e($f('issue_date', 'created_date')) ?></div>
            </div>
            <div class="field">
              <div class="label">تاريخ الإنتهاء</div>
              <div class="value"><?= $e($f('expiry_date')) ?></div>
            </div>
          </div>
        </section>

        <!-- معلومات السجل التجاري -->
        <section class="section">
          <h2 class="section-title">معلومات السجل التجاري</h2>
          <div class="field-grid cols-3">
            <div class="field">
              <div class="label">اسم السجل التجاري</div>
              <div class="value"><?= $e($f('cr_name', 'entity_name')) ?><?= $en('entity_name_en') ?></div>
            </div>
            <div class="field">
              <div class="label">رقم السجل التجاري</div>
              <div class="value"><?= $e($f('cr_number')) ?></div>
            </div>
            <div class="field">
              <div class="label">حالة السجل التجاري</div>
              <div class="value"><?= $e($f('cr_status')) ?></div>
            </div>
            <div class="field">
              <div class="label">تاريخ إنتهاء السجل</div>
              <div class="value"><?= $e($f('cr_expiry_date')) ?></div>
            </div>
            <div class="field">
              <div class="label">نشاط السجل التجاري</div>
              <div class="value"><?= $e($f('cr_activity')) ?></div>
            </div>
          </div>
        </section>

        <!-- بيانات المنشأة/الفرد -->
        <section class="section">
          <h2 class="section-title">بيانات المنشأة/الفرد</h2>
          <div class="field-grid cols-3">
            <div class="field">
              <div class="label">اسم المنشأة</div>
              <div class="value"><?= $e($f('entity_name')) ?><?= $en('entity_name_en') ?></div>
            </div>
            <div class="field">
              <div class="label">رقم هوية المنشأة</div>
              <div class="value"><?= $e($f('entity_id')) ?></div>
            </div>
            <div class="field">
              <div class="label">المنطقة</div>
              <div class="value"><?= $e($f('region')) ?><?= $en('region_en') ?></div>
            </div>
            <div class="field">
              <div class="label">المدينة</div>
              <div class="value"><?= $e($f('city')) ?><?= $en('city_en') ?></div>
            </div>
            <div class="field">
              <div class="label">مقر مزاولة النشاط</div>
              <div class="value"><?= $e($f('address')) ?><?= $en('address_en') ?></div>
            </div>
          </div>
        </section>

        <!-- معلومات التواصل -->
        <?php if ($f('contact_name') !== '' || $f('contact_mobile') !== '' || $f('contact_email') !== ''): ?>
          <section class="section">
            <h2 class="section-title">معلومات التواصل</h2>
            <div class="field-grid cols-3">
              <div class="field">
                <div class="label">مسؤول الاتصال</div>
                <div class="value"><?= $e($f('contact_name')) ?></div>
              </div>
              <div class="field">
                <div class="label">رقم الجوال</div>
                <div class="value"><?= $e($f('contact_mobile')) ?></div>
              </div>
              <div class="field">
                <div class="label">البريد الالكتروني</div>
                <div class="value">
                  <?php if ($f('contact_email') !== ''): ?>
                    <a href="mailto:<?= $e($f('contact_email')) ?>"><?= $e($f('contact_email')) ?></a>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </section>
        <?php endif; ?>
