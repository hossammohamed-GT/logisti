        <!-- بيانات المنشأة/الفرد -->
        <section class="section">
          <h2 class="section-title">بيانات المنشأة/الفرد
          </h2>
          <div class="field-grid">
            <div class="field">
              <div class="label">الاسم</div>
              <div class="value">مؤسسة الإنجاز المتميزة
                للخدمات اللوجستية</div>
            </div>
            <div class="field">
              <div class="label">رقم هوية المنشأة</div>
              <div class="value">7027992556</div>
            </div>
          </div>
        </section>

        <!-- معلومات الترخيص الرئيسي -->
        <section class="section">
          <h2 class="section-title">معلومات الترخيص الرئيسي
          </h2>
          <div class="field-grid cols-3">
            <div class="field">
              <div class="label">رقم الترخيص</div>
              <div class="value">38/00014540</div>
            </div>
            <div class="field">
              <div class="label">نوع الترخيص/النشاط</div>
              <div class="value">نشاط النقل الخفيف للبضائع
                لأغراض تجارية (للغير - منشآت)</div>
            </div>
            <div class="field">
              <div class="label">المدينة</div>
              <div class="value">محافظة جدة</div>
            </div>
            <div class="field">
              <div class="label">تاريخ الإصدار</div>
              <div class="value">1446/09/08</div>
            </div>
            <div class="field">
              <div class="label">تاريخ الإنتهاء</div>
              <div class="value">1449/10/12</div>
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
        <?= htmlspecialchars($card['card_number'], ENT_QUOTES, 'UTF-8') ?>
      </div>
    </div>

    <div class="field">
      <div class="label">هوية السائق</div>
      <div class="value">
        <?= htmlspecialchars($card['driver_id_number'], ENT_QUOTES, 'UTF-8') ?>
      </div>
    </div>

    <div class="field">
      <div class="label">الاسم الأول</div>
      <div class="value">
        <?= htmlspecialchars($card['first_name_ar'], ENT_QUOTES, 'UTF-8') ?>
      </div>
    </div>

    <div class="field">
      <div class="label">اسم العائلة</div>
      <div class="value">
        <?= htmlspecialchars($card['family_name_ar'], ENT_QUOTES, 'UTF-8') ?>
      </div>
    </div>

    <div class="field">
      <div class="label">تاريخ إصدار البطاقة</div>
      <div class="value">
        <?= htmlspecialchars($card['issue_date'], ENT_QUOTES, 'UTF-8') ?>
      </div>
    </div>

    <div class="field">
      <div class="label">تاريخ إنتهاء البطاقة</div>
      <div class="value">
        <?= htmlspecialchars($card['expiry_date'], ENT_QUOTES, 'UTF-8') ?>
      </div>
    </div>

    <div class="field span-2">
      <div class="label">نوع البطاقة</div>
      <div class="value">
        <?= htmlspecialchars($card['card_type_ar'], ENT_QUOTES, 'UTF-8') ?>
      </div>
    </div>

  </div>
</section>

