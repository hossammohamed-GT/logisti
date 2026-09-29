<?php
/** الترخيص - License | $data من ملف البيانات الثابتة */
$e = static fn($v) => htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8');
?>
        <!-- معلومات الترخيص الرئيسي -->
        <section class="section">
          <h2 class="section-title">معلومات الترخيص الرئيسي</h2>
          <div class="field-grid cols-3">
            <div class="field">
              <div class="label">النوع</div>
              <div class="value"><?= $e($data['license_kind']) ?></div>
            </div>
            <div class="field">
              <div class="label">رقم الترخيص</div>
              <div class="value"><?= $e($data['license_number']) ?></div>
            </div>
            <div class="field">
              <div class="label">حالة الطلب</div>
              <div class="value"><?= $e($data['request_status']) ?></div>
            </div>
            <div class="field">
              <div class="label">تاريخ الإنشاء</div>
              <div class="value"><?= $e($data['created_date']) ?></div>
            </div>
            <div class="field">
              <div class="label">تاريخ الإنتهاء</div>
              <div class="value"><?= $e($data['expiry_date']) ?></div>
            </div>
          </div>
        </section>

        <!-- معلومات السجل التجاري -->
        <section class="section">
          <h2 class="section-title">معلومات السجل التجاري</h2>
          <div class="field-grid cols-3">
            <div class="field">
              <div class="label">اسم السجل التجاري</div>
              <div class="value"><?= $e($data['cr_name']) ?></div>
            </div>
            <div class="field">
              <div class="label">رقم السجل التجاري</div>
              <div class="value"><?= $e($data['cr_number']) ?></div>
            </div>
            <div class="field">
              <div class="label">حالة السجل التجاري</div>
              <div class="value"><?= $e($data['cr_status']) ?></div>
            </div>
            <div class="field">
              <div class="label">تاريخ إنتهاء السجل</div>
              <div class="value"><?= $e($data['cr_expiry_date']) ?></div>
            </div>
            <div class="field">
              <div class="label">نشاط السجل التجاري</div>
              <div class="value"><?= $e($data['cr_activity']) ?></div>
            </div>
          </div>
        </section>

        <!-- بيانات المنشأة/الفرد -->
        <section class="section">
          <h2 class="section-title">بيانات المنشأة/الفرد</h2>
          <div class="field-grid cols-3">
            <div class="field">
              <div class="label">اسم المنشأة</div>
              <div class="value"><?= $e($data['entity_name']) ?></div>
            </div>
            <div class="field">
              <div class="label">رقم هوية المنشأة</div>
              <div class="value"><?= $e($data['entity_id']) ?></div>
            </div>
            <div class="field">
              <div class="label">المنطقة</div>
              <div class="value"><?= $e($data['region']) ?></div>
            </div>
            <div class="field">
              <div class="label">المدينة</div>
              <div class="value"><?= $e($data['city']) ?></div>
            </div>
            <div class="field">
              <div class="label">مقر مزاولة النشاط</div>
              <div class="value"><?= $e($data['address']) ?></div>
            </div>
          </div>
        </section>

        <!-- معلومات التواصل -->
        <section class="section">
          <h2 class="section-title">معلومات التواصل</h2>
          <div class="field-grid cols-3">
            <div class="field">
              <div class="label">مسؤول الاتصال</div>
              <div class="value"><?= $e($data['contact_name']) ?></div>
            </div>
            <div class="field">
              <div class="label">رقم الجوال</div>
              <div class="value"><?= $e($data['contact_mobile']) ?></div>
            </div>
            <div class="field">
              <div class="label">البريد الالكتروني</div>
              <div class="value">
                <a href="mailto:<?= $e($data['contact_email']) ?>"><?= $e($data['contact_email']) ?></a>
              </div>
            </div>
          </div>
        </section>
