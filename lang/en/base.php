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
        "layout" => "Layout",
    ],

    "fields" => [
        "name" => "Name",
        "status" => "Status",
        "pages" => "Pages",
        "application" => "Application",
        "page" => "Page",
        "plugins" => "Plugins",
        "plugin_id" => "Plugin",
        "position" => "Position",
        "ordering" => "Ordering",
    ],

    "validation" => [
        "check_exist_name" => "The :attribute has already been taken.",
        "object_not_found" => "The Layout was not found.",
    ],

    "exceptions" => [
        "model_layout_contract_not_found" => "Model ':model' not implements 'JobMetric\Layout\Contracts\LayoutContract' interface!",
        "collection_property_not_exist" => "The ':field' property not exist in ':model' model!",
    ],

    "messages" => [
        "found" => "The layout was found successfully.",
        "created" => "The layout was created successfully.",
        "updated" => "The layout was updated successfully.",
        "deleted" => "The layout was deleted successfully and sent to the trash.",
        "restored" => "The layout was restored successfully.",
        "permanently_deleted" => "The layout was permanently deleted successfully.",
    ],

    "events" => [
        "group" => "Layout",
        "layout_stored" => [
            "title" => "Layout Stored",
            "description" => "This event is triggered when a Layout is stored.",
        ],
        "layout_updated" => [
            "title" => "Layout Updated",
            "description" => "This event is triggered when a Layout is updated.",
        ],
        "layout_deleted" => [
            "title" => "Layout Deleted",
            "description" => "This event is triggered when a Layout is deleted.",
        ],
        "layout_restored" => [
            "title" => "Layout Restored",
            "description" => "This event is triggered when a Layout is restored.",
        ],
        "layout_force_deleted" => [
            "title" => "Layout Force Deleted",
            "description" => "This event is triggered when a Layout is force deleted.",
        ],
        "layout_pages_synced" => [
            "title" => "Layout Pages Synced",
            "description" => "This event is triggered when layout pages are synced.",
        ],
        "layout_plugins_synced" => [
            "title" => "Layout Plugins Synced",
            "description" => "This event is triggered when layout plugins are synced.",
        ],
    ],

];
