<?php

require_once __DIR__ . '/../admin_boot.php';

use Photobooth\Utility\PathUtility;
use Photobooth\Service\LanguageService;

$languageService = LanguageService::getInstance();

$pageTitle = 'Custom View Settings';

// load config definitions
$configsetup = require PathUtility::getAbsolutePath('lib/configsetup.inc.php');

// current config
$customSettings = $config['adminpanel']['custom_settings'] ?? [];

include PathUtility::getAbsolutePath('admin/components/head.admin.php');
include PathUtility::getAbsolutePath('admin/helper/index.php');

?>

<div class="w-full h-full flex flex-col overflow-hidden bg-brand-2 px-6 py-12">

    <div class="w-full max-w-5xl mx-auto bg-white rounded-lg shadow-xl p-6 flex flex-col h-full">

        <h1 class="text-xl font-bold mb-6">
            Custom View Settings
        </h1>

        <!-- IMPORTANT:
             This MUST behave like normal admin form -->
        <form class="flex flex-col flex-1 overflow-hidden">

            <!-- scrollable area -->
            <div class="flex-1 overflow-y-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 pr-2">

                <?php foreach ($configsetup as $section => $fields): ?>

                    <div class="col-span-full mt-4 mb-2 font-bold text-gray-600">
                        <?= htmlspecialchars($section) ?>
                    </div>

                    <?php foreach ($fields as $key => $setting): ?>

                        <?php if (in_array($key, ['platform', 'view'], true)) continue; ?>

                        <?php
                            $name = $setting['name'] ?? $key;
                            $label = $setting['label'] ?? $name;
                            $checked = in_array($name, $customSettings, true);
                        ?>

                        <label class="flex items-start gap-2 p-3 border rounded hover:bg-gray-50 cursor-pointer">

                            <input
                                type="checkbox"
                                name="adminpanel[custom_settings][]"
                                value="<?= htmlspecialchars($name) ?>"
                                <?= $checked ? 'checked' : '' ?>
                                class="mt-1"
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

            <!-- IMPORTANT: reuse global admin save button -->
            <div class="mt-6 flex justify-end shrink-0">
                <?php
                    // reuse existing admin save system
                    echo \Photobooth\Utility\AdminInput::renderCta('save', 'save-admin-btn');
                ?>
            </div>

        </form>

    </div>

</div>

<?php
include PathUtility::getAbsolutePath('admin/components/footer.scripts.php');
include PathUtility::getAbsolutePath('admin/components/footer.admin.php');
?>