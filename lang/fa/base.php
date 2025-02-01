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

];
