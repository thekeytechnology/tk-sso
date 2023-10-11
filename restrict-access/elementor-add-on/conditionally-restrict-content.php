<?php

/*
 * TK SSO Elementor Add on to conditionally restrict content
 */

// Hooks
add_action('elementor/element/common/_section_style/before_section_start', 'tkAddRestrictionControls', 10, 1);
add_action('elementor/element/column/layout/after_section_end', 'tkAddRestrictionControls', 10, 1);
add_action('elementor/element/section/section_typo/after_section_end', 'tkAddRestrictionControls', 10, 1);

add_filter('elementor/frontend/widget/should_render', 'tkRestrictContainer', 10, 2);
add_filter('elementor/frontend/section/should_render', 'tkRestrictContainer', 10, 2);
add_filter('elementor/frontend/column/should_render', 'tkRestrictContainer', 10, 2);


function tkAddRestrictionControls($element)
{
    tkAddBasicRestrictionControls($element);
    tkAddTimeRestrictionControls($element);
}

function tkRestrictContainer($should_render, $object)
{

    if (is_admin() || current_user_can('editor') || current_user_can('administrator')) return $should_render;

    $basicRestrictionShouldRender = tkBasicRestrictionShouldRender($should_render, $object);
    $timeRestrictionShouldRender = tkTimeRestrictionShouldRender($should_render, $object);
    if (!$timeRestrictionShouldRender || !$basicRestrictionShouldRender) {
        $should_render = false;
    }


    return apply_filters("tk-sso-restrict-content-elementor-should-render", $should_render, $object);
}

