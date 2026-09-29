<?php
/**
 * بيانات ثابتة (Static Data) للتوكنات الخاصة ببطاقة التشغيل والترخيص.
 * ملاحظة: هذه البيانات تجريبية/تعليمية فقط ولا تأتي من قاعدة البيانات.
 * لإضافة توكن جديد: أضف عنصرًا جديدًا بنفس الشكل مع نوعه (type).
 */

return [

    // ===== Operation Card - بطاقة تشغيل =====
    'eddc5956-9eb8-49b7-ac12-380247e30099' => [
        'type'  => 'operation_card',
        'title' => 'تفاصيل بطاقة التشغيل',
        'data'  => [
            'entity_name'      => 'مؤسسة غايتكم للخدمات اللوجستية',

            'license_number'   => '81/00000424',
            'license_type'     => 'نقل البضائع عبر الدراجات الآلية لأغراض تجارية',
            'city'             => 'محافظة جدة',

            'card_number'      => '81-00003239',
            'card_type'        => 'نقل البضائع عبر الدراجة الآلية لأغراض تجارية',
            'issue_date'       => '1448/03/19',
            'expiry_date'      => '1449/03/10',
            'renewal_date'     => '',

            'vehicle_model'    => 'دراجة نارية سويد',
            'plate_number'     => 'د ب 6285',
            'manufacture_year' => '2025',
            'vehicle_color'    => 'رصاصي',
            'serial_number'    => '127771120',
        ],
    ],

    // ===== License - ترخيص =====
    'dcd63444-e888-454a-8c47-bbdbfdde04cd' => [
        'type'  => 'license',
        'title' => 'تفاصيل الترخيص',
        'data'  => [
            'activity'       => 'نقل البضائع عبر الدراجات الآلية لأغراض تجارية',

            'license_kind'   => 'رئيسي',
            'license_number' => '81/00000424',
            'request_status' => 'نشط',
            'created_date'   => '1448-02-29',
            'expiry_date'    => '1449-03-10',

            'cr_name'        => 'مؤسسة غايتكم للخدمات اللوجستية',
            'cr_number'      => '1010650025',
            'cr_status'      => 'نشط',
            'cr_expiry_date' => '',
            'cr_activity'    => '492311 - النقل الخفيف',

            'entity_name'    => 'مؤسسة غايتكم للخدمات اللوجستية',
            'entity_id'      => '7017775516',
            'region'         => 'مكّة المكرّمة',
            'city'           => 'محافظة جدة',
            'address'        => '23466, عمرو ابن سنان, الاجواد',

            'contact_name'   => 'محمد بن غرامه الاسمري',
            'contact_mobile' => '966559879689',
            'contact_email'  => 'ghaya.com21@gmail.com',
        ],
    ],

];
