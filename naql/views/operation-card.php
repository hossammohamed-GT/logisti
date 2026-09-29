<?php
/** بطاقة التشغيل - Operation Card | $data = صف من جدول operation_cards */
$e = static function ($v) {
    return htmlspecialchars((string)($v === null ? '' : $v), ENT_QUOTES, 'UTF-8');
};
?>
        <!-- بيانات المنشأة/الفرد -->
        <section class="section">
          <h2 class="section-title">بيانات المنشأة/الفرد</h2>
          <div class="field-grid">
            <div class="field">
              <div class="label">الاسم</div>
              <div class="value"><?= $e($data['entity_name']) ?></div>
            </div>
          </div>
        </section>

        <!-- معلومات الترخيص الرئيسي -->
        <section class="section">
          <h2 class="section-title">معلومات الترخيص الرئيسي</h2>
          <div class="field-grid cols-3">
            <div class="field">
              <div class="label">رقم الترخيص</div>
              <div class="value"><?= $e($data['license_number']) ?></div>
            </div>
            <div class="field">
              <div class="label">نوع الترخيص/النشاط</div>
              <div class="value"><?= $e($data['license_type']) ?></div>
            </div>
            <div class="field">
              <div class="label">المدينة</div>
              <div class="value"><?= $e($data['city']) ?></div>
            </div>
          </div>
        </section>

        <!-- بيانات بطاقة التشغيل -->
        <section class="section">
          <h2 class="section-title">بيانات بطاقة التشغيل</h2>
          <div class="field-grid cols-3">
            <div class="field">
              <div class="label">رقم البطاقة</div>
              <div class="value"><?= $e($data['card_number']) ?></div>
            </div>
            <div class="field">
              <div class="label">نوع بطاقة التشغيل</div>
              <div class="value"><?= $e($data['card_type']) ?></div>
            </div>
            <div class="field">
              <div class="label">تاريخ الإصدار</div>
              <div class="value"><?= $e($data['issue_date']) ?></div>
            </div>
            <div class="field">
              <div class="label">تاريخ الإنتهاء</div>
              <div class="value"><?= $e($data['expiry_date']) ?></div>
            </div>
            <div class="field">
              <div class="label">تاريخ التجديد</div>
              <div class="value"><?= $e($data['renewal_date']) ?></div>
            </div>
          </div>
        </section>

        <!-- معلومات المركبة -->
        <section class="section">
          <h2 class="section-title">معلومات المركبة</h2>
          <div class="field-grid cols-3">
            <div class="field">
              <div class="label">نوع السيارة - الماركة و الطراز</div>
              <div class="value"><?= $e($data['vehicle_model']) ?></div>
            </div>
            <div class="field">
              <div class="label">رقم اللوحة</div>
              <div class="value"><?= $e($data['plate_number']) ?></div>
            </div>
            <div class="field">
              <div class="label">سنة الصنع</div>
              <div class="value"><?= $e($data['manufacture_year']) ?></div>
            </div>
            <div class="field">
              <div class="label">لون المركبة</div>
              <div class="value"><?= $e($data['vehicle_color']) ?></div>
            </div>
            <div class="field">
              <div class="label">الرقم التسلسلي</div>
              <div class="value"><?= $e($data['serial_number']) ?></div>
            </div>
          </div>
        </section>
