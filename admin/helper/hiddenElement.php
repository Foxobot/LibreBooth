<?php

function isElementHidden($element_class, $setting)
{
    global $config;

    // Default visibility level if not defined
    if (empty($setting['view'])) {
        $setting['view'] = 'expert';
    }

    $currentView = $config['adminpanel']['view'];

    /**
     * ----------------------------------------
     * CUSTOM VIEW (Whitelist-based filtering)
     * ----------------------------------------
     * In custom mode, only settings listed in
     * custom_settings are shown.
     */
    if ($currentView === 'custom') {

        $settingName = $setting['name'] ?? '';

        $allowed = in_array(
            $settingName,
            $config['adminpanel']['custom_settings'] ?? [],
            true
        );

        if (!$allowed) {
            return 'hidden';
        }

        // Do NOT return here:
        // platform and type-based rules still apply below
    }

    /**
     * ----------------------------
     * DEFAULT VIEW LOGIC
     * ----------------------------
     */
    switch ($setting['view']) {

        case 'experimental':
            if (empty($config['adminpanel']['experimental_settings'])) {
                $element_class = 'hidden';
            }
            break;

        case 'expert':
            if ($currentView === 'advanced' || $currentView === 'basic') {
                $element_class = 'hidden';
            }
            break;

        case 'advanced':
            if ($currentView === 'basic') {
                $element_class = 'hidden';
            }
            break;

        case 'basic':
        default:
            break;
    }

    /**
     * ----------------------------
     * PLATFORM FILTER
     * ----------------------------
     */
    if (
        isset($fields['platform']) &&
        $fields['platform'] != 'all' &&
        $fields['platform'] != $os
    ) {
        $setting['type'] = $element_class = 'hidden';
    }

    /**
     * ----------------------------
     * HARD HIDE FLAG
     * ----------------------------
     * Forces element to be hidden regardless of view.
     */
    if (isset($setting['type']) && $setting['type'] === 'hidden') {
        $element_class = 'hidden';
    }

    return $element_class;
}