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

    'validation' => [
        'errors' => 'Validation errors occurred.',
        'check_exist_name' => 'The :attribute has already been taken.',
        'object_not_found' => 'The Layout was not found.',
    ],

    "exceptions" => [
        'model_layout_contract_not_found' => 'Model ":model" not implements "JobMetric\Layout\Contracts\LayoutContract" interface!',
        'collection_property_not_exist' => 'The ":field" property not exist in ":model" model!',
    ],

    'messages' => [
        'found' => 'The layout was found successfully.',
        'created' => 'The layout was created successfully.',
        'updated' => 'The layout was updated successfully.',
        'deleted' => 'The layout was deleted successfully and sent to the trash.',
        'restored' => 'The layout was restored successfully.',
        'permanently_deleted' => 'The layout was permanently deleted successfully.',
    ],

];
