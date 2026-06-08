<?php

require_once __DIR__ . '/../admin_boot.php';

use Photobooth\Service\LanguageService;
use Photobooth\Utility\PathUtility;

$languageService = LanguageService::getInstance();

$pageTitle = 'Custom View Settings';
include PathUtility::getAbsolutePath('admin/components/head.admin.php');
include PathUtility::getAbsolutePath('admin/helper/index.php');

$customSettings = $config['adminpanel']['custom_settings'] ?? [];

// handle submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $selected = $_POST['custom_settings'] ?? [];

    $config['adminpanel']['custom_settings'] = array_values($selected);

    // IMPORTANT: use existing config save system here
    saveConfig($config);

    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}
<div class="w-full min-h-screen bg-brand-2 px-6 py-12 overflow-auto">

    <div class="w-full max-w-3xl mx-auto bg-white rounded-lg shadow-xl p-6">

        <h1 class="text-xl font-bold mb-6">
            Custom View Settings
        </h1>

        <form method="POST">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">

                <?php foreach ($configsetup as $section => $fields): ?>
                    <?php foreach ($fields as $key => $setting): ?>

                        <?php if (in_array($key, ['platform', 'view'])) continue; ?>

                        <?php
                            $name = $setting['name'] ?? $key;
                            $checked = in_array($name, $customSettings, true);
                        ?>

                        <label class="flex items-center gap-2 p-2 border rounded">

                            <input
                                type="checkbox"
                                name="custom_settings[]"
                                value="<?= htmlspecialchars($name) ?>"
                                <?= $checked ? 'checked' : '' ?>
                            >

                            <div>
                                <div class="font-semibold">
                                    <?= htmlspecialchars($setting['label'] ?? $name) ?>
                                </div>
                                <div class="text-xs text-gray-500">
                                    <?= htmlspecialchars($section) ?>
                                </div>
                            </div>

                        </label>

                    <?php endforeach; ?>
                <?php endforeach; ?>

            </div>

            <button type="submit" class="mt-6 btn btn-primary">
                Save
            </button>

        </form>

    </div>

</div>
include PathUtility::getAbsolutePath('admin/components/footer.scripts.php');
include PathUtility::getAbsolutePath('admin/components/footer.admin.php');