<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Base Layout Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during Layout for
    | various messages that we need to display to the user.
    |
    */

    "entity_names" => [
        "layout" => "لایه",
    ],

    "fields" => [
        "name" => "نام",
        "status" => "وضعیت",
        "pages" => "صفحات",
        "application" => "اپلیکیشن",
        "page" => "صفحه",
        "plugins" => "پلاگین‌ها",
        "plugin_id" => "پلاگین",
        "position" => "موقعیت",
        "ordering" => "ترتیب",
    ],

    "validation" => [
        "check_exist_name" => ":attribute قبلاً انتخاب شده است.",
        "object_not_found" => "لایه‌ی مورد نظر یافت نشد.",
    ],

    "exceptions" => [
        "model_layout_contract_not_found" => "مدل ':model' اینترفیس 'JobMetric\Layout\Contracts\LayoutContract' را پیاده‌سازی نکرده است!",
        "collection_property_not_exist" => "ویژگی ':field' در مدل ':model' وجود ندارد!",
    ],

    "messages" => [
        "found" => "لایه با موفقیت یافت شد.",
        "created" => "لایه با موفقیت ایجاد شد.",
        "updated" => "لایه با موفقیت به‌روزرسانی شد.",
        "deleted" => "لایه با موفقیت حذف شد و به سطل زباله ارسال شد.",
        "restored" => "لایه با موفقیت بازیابی شد.",
        "permanently_deleted" => "لایه با موفقیت به طور دائمی حذف شد.",
    ],

    "events" => [
        "group" => "لایه",
        "layout_stored" => [
            "title" => "لایه ذخیره شد",
            "description" => "این رویداد هنگام ذخیره شدن یک لایه اجرا می‌شود.",
        ],
        "layout_updated" => [
            "title" => "لایه به‌روزرسانی شد",
            "description" => "این رویداد هنگام به‌روزرسانی یک لایه اجرا می‌شود.",
        ],
        "layout_deleted" => [
            "title" => "لایه حذف شد",
            "description" => "این رویداد هنگام حذف یک لایه اجرا می‌شود.",
        ],
        "layout_restored" => [
            "title" => "لایه بازیابی شد",
            "description" => "این رویداد هنگام بازیابی یک لایه اجرا می‌شود.",
        ],
        "layout_force_deleted" => [
            "title" => "لایه برای همیشه حذف شد",
            "description" => "این رویداد هنگام حذف دائمی یک لایه اجرا می‌شود.",
        ],
        "layout_pages_synced" => [
            "title" => "صفحات لایه همگام شد",
            "description" => "این رویداد هنگام همگام‌سازی صفحات لایه اجرا می‌شود.",
        ],
        "layout_plugins_synced" => [
            "title" => "پلاگین‌های لایه همگام شد",
            "description" => "این رویداد هنگام همگام‌سازی پلاگین‌های لایه اجرا می‌شود.",
        ],
    ],

];
