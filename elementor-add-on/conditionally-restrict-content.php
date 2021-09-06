<?php

/*
 * TK SSO Elementor Add on to conditionally restrict content
 */

// Hooks
add_action('elementor/element/common/_section_style/before_section_start', 'tkAddRestrictionControls', 10, 1);
add_action('elementor/element/column/layout/after_section_end', 'tkAddRestrictionControls', 10, 1);
add_action('elementor/element/section/section_typo/after_section_end', 'tkAddRestrictionControls', 10, 1);

add_filter('elementor/widget/render_content', 'tkRestrictWidget', 10, 2);
add_filter('elementor/frontend/section/should_render', 'tkRestrictContainer', 10, 2);
add_filter('elementor/frontend/column/should_render', 'tkRestrictContainer', 10, 2);


// Functions
function tkAddRestrictionControls($element)
{
    $element->start_controls_section(
        'tk_control_section',
        [
            'tab' => \Elementor\Controls_Manager::TAB_ADVANCED,
            'label' => __('Inhalt einschränken', 'restrict-elementor-widgets'),
        ]
    );
    $element->add_control(
        'tk_enable_restriction',
        [
            'label' => __('Einschränkung aktivieren', 'tkt-sso'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => '',
            'label_on' => __('Yes', 'tkt-sso'),
            'label_off' => __('No', 'tkt-sso'),
            'return_value' => 'yes',
        ]
    );

    $element->add_control(
        'tk_show_content_to_roles',
        [
            'type' => \Elementor\Controls_Manager::SELECT2,
            'label' => __('Inhalte für folgende Benutzerrollen zeigen', 'tkt-sso'),
            'description' => __('', 'tkt-sso'),
            'options' => [
                'doctor' => __('Arzt', 'tkt-sso'),
                'pharmacist' => __('Apotheker', 'tkt-sso'),
                'not_logged_in' => __('Nicht eingeloggt', 'tkt-sso-sso')
            ],
            'separator' => 'before',
            'multiple' => true,
            'label_block' => true,
            'condition' => [
                'tk_enable_restriction' => 'yes',
            ]
        ]
    );

    $element->end_controls_section();
}


function tkRestrictWidget($content, $widget)
{
    global $tkSsoBroker;
    $currentUserRole = isset($_COOKIE['tk_sso_token']) ? $tkSsoBroker->authenticate($_COOKIE['tk_sso_token'], 'role') : 'not_logged_in';
    if (is_admin()) return $content;
    $settings = $widget->get_settings_for_display();
    if (isset($settings['tk_enable_restriction']) && $settings['tk_enable_restriction'] == 'yes') {
        if (! empty($settings['tk_show_content_to_roles'])) {
            if(! in_array($currentUserRole, $settings['tk_show_content_to_roles'])) {
                return '';
            }
        }
    }
    return $content;
}


function tkRestrictContainer($should_render, $object)
{
    global $tkSsoBroker;
    $currentUserRole = isset($_COOKIE['tk_sso_token']) ? $tkSsoBroker->authenticate($_COOKIE['tk_sso_token'], 'role') : 'not_logged_in';
    if (is_admin()) return $should_render;
    $settings = $object->get_settings_for_display();
    if (isset($settings['tk_enable_restriction']) && $settings['tk_enable_restriction'] == 'yes') {
        if (! empty($settings['tk_show_content_to_roles'])) {
            if(! in_array($currentUserRole, $settings['tk_show_content_to_roles'])) {
                return false;
            }
        }
    }
    return $should_render;
}