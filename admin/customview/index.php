<?php

require_once __DIR__ . '/../admin_boot.php';

use Photobooth\Service\LanguageService;
use Photobooth\Utility\PathUtility;

$languageService = LanguageService::getInstance();

$pageTitle = 'Custom View Settings';
$configsetup = require PathUtility::getAbsolutePath('lib/configsetup.inc.php');

include PathUtility::getAbsolutePath('admin/components/head.admin.php');
include PathUtility::getAbsolutePath('admin/helper/index.php');

$customSettings = $config['adminpanel']['custom_settings'] ?? [];

/**
 * -------------------------------------------------
 * Handle form submit
 * -------------------------------------------------
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $selected = $_POST['custom_settings'] ?? [];

    // normalize array values
    $config['adminpanel']['custom_settings'] = array_values($selected);

    /**
     * IMPORTANT:
     * Use existing Photobooth config persistence mechanism here.
     * Replace saveConfig() if your project uses another function.
     */
    saveConfig($config);

    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}

?>

<div class="w-full min-h-screen bg-brand-2 px-6 py-12 overflow-auto">

    <div class="w-full max-w-3xl mx-auto bg-white rounded-lg shadow-xl p-6">

        <h1 class="text-xl font-bold mb-6">
            Custom View Settings
        </h1>

        <form method="POST">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">

                <?php foreach ($configsetup as $section => $fields): ?>
                    <?php foreach ($fields as $key => $setting): ?>

                        <?php if (in_array($key, ['platform', 'view'], true)) continue; ?>

                        <?php
                            $name = $setting['name'] ?? $key;
                            $label = $setting['label'] ?? $name;
                            $checked = in_array($name, $customSettings, true);
                        ?>

                        <label class="flex items-center gap-2 p-2 border rounded hover:bg-gray-50">

                            <input
                                type="checkbox"
                                name="custom_settings[]"
                                value="<?= htmlspecialchars($name) ?>"
                                <?= $checked ? 'checked' : '' ?>
                            >

                            <div class="flex flex-col">
                                <div class="font-semibold">
                                    <?= htmlspecialchars($label) ?>
                                </div>

                                <div class="text-xs text-gray-500">
                                    <?= htmlspecialchars($section) ?>
                                </div>
                            </div>

                        </label>

                    <?php endforeach; ?>
                <?php endforeach; ?>

            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" class="btn btn-primary">
                    Save Custom View
                </button>
            </div>

        </form>

    </div>

</div>

<?php
include PathUtility::getAbsolutePath('admin/components/footer.scripts.php');
include PathUtility::getAbsolutePath('admin/components/footer.admin.php');
?>