<?php

function tkAddTimeRestrictionControls($element)
{
    $element->start_controls_section(
        'tk_time_control_section',
        [
            'tab' => \Elementor\Controls_Manager::TAB_ADVANCED,
            'label' => __('Sichtbarkeit zeitlich kontrollieren', 'restrict-elementor-widgets'),
        ]
    );

    $element->add_control(
        'tk_enable_time_restriction',
        [
            'label' => __('Einschränkung aktivieren', 'tk-sso'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => '',
            'label_on' => __('Yes', 'tk-sso'),
            'label_off' => __('No', 'tk-sso'),
            'return_value' => 'yes',
        ]
    );

    $element->add_control(
        'tk_show_content_time_from',
        [
            'label' => __('Von:', 'tk-sso'),
            'type' => \Elementor\Controls_Manager::DATE_TIME,
            'default' => '',
            'condition' => [
                'tk_enable_time_restriction' => 'yes',
            ],
        ]
    );

    $element->add_control(
        'tk_show_content_time_to',
        [
            'label' => __('Bis:', 'tk-sso'),
            'type' => \Elementor\Controls_Manager::DATE_TIME,
            'default' => '',
            'condition' => [
                'tk_enable_time_restriction' => 'yes',
            ],
        ]
    );

    $element->add_control(
        'tk_visibility_option',
        [
            'label' => __('Sichtbarkeitsoption', 'tk-sso'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'visible',
            'options' => [
                'visible' => __('Sichtbar', 'tk-sso'),
                'hidden' => __('Versteckt', 'tk-sso'),
            ],
            'condition' => [
                'tk_enable_time_restriction' => 'yes',
            ],
        ]
    );

    $element->end_controls_section();
}

function tkTimeRestrictionShouldRender($should_render, $object) {
    $settings = $object->get_settings_for_display();
    if (isset($settings['tk_enable_time_restriction']) && $settings['tk_enable_time_restriction'] == 'yes') {
        $current_time = current_time('Y-m-d H:i:s');
        $from_time = $settings['tk_show_content_time_from'];
        $to_time = $settings['tk_show_content_time_to'];
        $isVisible = true;

        if(!empty($from_time) && $current_time < $from_time) {
            $isVisible = false;
        }

        if(!empty($to_time) && $current_time > $to_time) {
            $isVisible = false;
        }

        if($settings['tk_visibility_option'] === 'hidden') {
            $isVisible = !$isVisible;
        }

        if(!$isVisible) {
            $should_render = false;
        }

        return apply_filters("tk-sso-restrict-content-elementor-should-render", $should_render, $object);
    }

    return $should_render;
}

