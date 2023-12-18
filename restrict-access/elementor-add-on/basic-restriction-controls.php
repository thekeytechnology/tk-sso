<?php

function tkAddBasicRestrictionControls($element)
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
            ],
        ]
    );
    $element->end_controls_section();
}


function tkBasicRestrictionShouldRender($should_render, $object) {
    $settings = $object->get_settings_for_display();
    if (isset($settings['tk_enable_restriction']) && $settings['tk_enable_restriction'] == 'yes') {
        global $tkSsoUser;
        $whitelistRoles = $settings['tk_show_content_to_roles'] ?? [];
        if (
            !$tkSsoUser->hasRole($whitelistRoles)
        ) {
            $should_render = false;
        }
        return $should_render;
    }
    return $should_render;
}
