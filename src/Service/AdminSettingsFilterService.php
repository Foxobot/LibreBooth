<?php

namespace Photobooth\Service;

use Photobooth\Utility\ArrayUtility;
use Photobooth\Utility\PathUtility;

/**
 * Manages the admin settings filter (which individual settings are visible in admin panel).
 * Mirrors ConfigurationService pattern - saves to config/admin.filter.inc.php
 * 
 * Filter format:
 * [
 *     'mode' => 'whitelist' or 'blacklist',
 *     'settings' => [
 *         'section:setting_key',
 *         'print:print_qrcode',
 *         ...
 *     ]
 * ]
 */
class AdminSettingsFilterService
{
    private static ?self $instance = null;
    protected ?array $filter;

    private function __construct()
    {
        $this->load();
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    protected function load(): void
    {
        // Default: no filter (show all)
        $filterPath = PathUtility::getAbsolutePath('config/admin.filter.inc.php');
        if (file_exists($filterPath)) {
            $this->filter = require $filterPath;
        } else {
            $this->filter = null;
        }
    }

    /**
     * Get the current filter configuration
     */
    public function getFilter(): ?array
    {
        return $this->filter;
    }

    /**
     * Check if a specific setting should be visible in the admin panel
     * 
     * @param string $section Section key (e.g., 'print')
     * @param string $settingKey Setting key (e.g., 'print_qrcode')
     * @return bool True if the setting should be displayed
     */
    public function isSettingVisible(string $section, string $settingKey): bool
    {
        if ($this->filter === null) {
            return true; // No filter = show everything
        }

        $mode = $this->filter['mode'] ?? 'whitelist';
        $settingId = "$section:$settingKey";
        $visibleSettings = $this->filter['settings'] ?? [];

        if ($mode === 'whitelist') {
            // Only show settings in the whitelist
            return in_array($settingId, $visibleSettings, true);
        }

        if ($mode === 'blacklist') {
            // Show everything except blacklisted settings
            return !in_array($settingId, $visibleSettings, true);
        }

        return true;
    }

    /**
     * Save filter configuration to disk
     * 
     * @param array|null $filterData Filter configuration or null to disable filter
     * @throws \RuntimeException If file cannot be written
     */
    public function update(?array $filterData): void
    {
        $filterPath = PathUtility::getAbsolutePath('config/admin.filter.inc.php');

        if ($filterData === null) {
            // Remove filter file to show all settings
            if (file_exists($filterPath)) {
                unlink($filterPath);
            }
        } else {
            // Validate filter structure
            if (!isset($filterData['mode']) || !in_array($filterData['mode'], ['whitelist', 'blacklist'])) {
                throw new \RuntimeException('Invalid filter mode. Must be "whitelist" or "blacklist".');
            }

            if (!isset($filterData['settings']) || !is_array($filterData['settings'])) {
                throw new \RuntimeException('Filter must contain "settings" array.');
            }

            // Save filter to file
            $content = "<?php\n\nreturn " . ArrayUtility::export($filterData) . ";\n";
            if (!file_put_contents($filterPath, $content)) {
                throw new \RuntimeException('Admin settings filter can not be saved!');
            }
        }

        // Reload the filter
        $this->load();
    }
}
