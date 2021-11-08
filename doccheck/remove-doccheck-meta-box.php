<?php

function tkSsoRemoveDocCheckMetaBox() {

    remove_meta_box(
        "dcl_restrict_access",
        ['post', 'page'],
        'side'
    );

    $screens = get_post_types();
    foreach ($screens as $screen) {
        remove_meta_box(
            'dcl_restrict_access',
            $screen,
            'side'
        );
    }
}

add_action('add_meta_boxes', 'tkSsoRemoveDocCheckMetaBox', 99);

