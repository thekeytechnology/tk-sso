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


// Functions
function tkAddRestrictionControls($element) {
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
            'label' => __('Einschränkung aktivieren', 'tk-sso'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => '',
            'label_on' => __('Yes', 'tk-sso'),
            'label_off' => __('No', 'tk-sso'),
            'return_value' => 'yes',
        ]
    );
    $roleManager = new TkSsoRoleManager();
    $options = $roleManager->getRolesForRestriction();

    $element->add_control(
        'tk_show_content_to_roles',
        [
            'type' => \Elementor\Controls_Manager::SELECT2,
            'label' => __('Inhalte für folgende Benutzerrollen zeigen', 'tk-sso'),
            'description' => __('', 'tk-sso'),
            'options' => $options,
            'separator' => 'before',
            'multiple' => true,
            'label_block' => true,
            'condition' => [
                'tk_enable_restriction' => 'yes',
            ]
        ]
    );

    $element->add_control(
        'tk_show_content_to_roles_blacklist',
        [
            'type' => \Elementor\Controls_Manager::SELECT2,
            'label' => __('Inhalte für folgende Benutzerrollen verbergen', 'tk-sso'),
            'description' => __('', 'tk-sso'),
            'options' => $options,
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

function tkRestrictContainer($should_render, $object) {
    if (is_admin() || current_user_can('editor') || current_user_can('administrator')) return $should_render;

    $settings = $object->get_settings_for_display();
    if (isset($settings['tk_enable_restriction']) && $settings['tk_enable_restriction'] == 'yes') {
        global $tkSsoUser;
        $whitelistRoles = $settings['tk_show_content_to_roles'] ?? [];
        $blacklistRoles = $settings['tk_show_content_to_roles_blacklist'] ?? [];
        if (!$tkSsoUser->hasRole($whitelistRoles) || $tkSsoUser->hasRole($blacklistRoles)) {
            $should_render = false;
        }
        return apply_filters("tk-sso-restrict-content-elementor-should-render", $should_render, $object);
    }
    return $should_render;
}
