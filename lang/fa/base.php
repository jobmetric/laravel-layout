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
