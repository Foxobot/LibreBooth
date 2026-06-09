<?php

use Photobooth\Service\AdminSettingsFilterService;
use Photobooth\Utility\PathUtility;

$configsetup = require PathUtility::getAbsolutePath('lib/configsetup.inc.php');
$filterService = AdminSettingsFilterService::getInstance();
$currentFilter = $filterService->getFilter();

$mode = $currentFilter['mode'] ?? 'whitelist';
$visibleSettings = $currentFilter['settings'] ?? [];
?>

<div class="admin-settings-filter card">
    <div class="card-header">
        <h3><?php echo $languageService->translate('admin_settings_filter:title'); ?></h3>
        <p class="text-muted"><?php echo $languageService->translate('admin_settings_filter:description'); ?></p>
    </div>

    <div class="card-body">
        <form id="admin-filter-form" class="form">
            <!-- Mode Selection -->
            <div class="form-group">
                <label><?php echo $languageService->translate('admin_settings_filter:mode'); ?></label>
                
                <div class="radio-group">
                    <label class="radio-label">
                        <input type="radio" name="filter_mode" value="show_all" 
                               class="filter-mode" 
                               <?= $currentFilter === null ? 'checked' : '' ?>>
                        <?php echo $languageService->translate('admin_settings_filter:show_all'); ?>
                    </label>
                    
                    <label class="radio-label">
                        <input type="radio" name="filter_mode" value="whitelist" 
                               class="filter-mode"
                               <?= $mode === 'whitelist' ? 'checked' : '' ?>>
                        <?php echo $languageService->translate('admin_settings_filter:whitelist'); ?>
                    </label>
                    
                    <label class="radio-label">
                        <input type="radio" name="filter_mode" value="blacklist" 
                               class="filter-mode"
                               <?= $mode === 'blacklist' ? 'checked' : '' ?>>
                        <?php echo $languageService->translate('admin_settings_filter:blacklist'); ?>
                    </label>
                </div>
            </div>

            <!-- Settings Selection -->
            <div class="form-group" id="settings-group" style="display: <?= $currentFilter !== null ? 'block' : 'none' ?>;">
                <label><?php echo $languageService->translate('admin_settings_filter:select_settings'); ?></label>
                
                <div class="settings-container">
                    <?php foreach ($configsetup as $section => $settings): ?>
                        <?php if (in_array($section, ['view', 'platform'])): continue; endif; ?>
                        
                        <div class="section-group">
                            <div class="section-header">
                                <h4><?= htmlspecialchars($section) ?></h4>
                                <div class="section-controls">
                                    <button type="button" class="btn-sm select-all-btn" data-section="<?= htmlspecialchars($section) ?>">
                                        <?php echo $languageService->translate('select_all'); ?>
                                    </button>
                                    <button type="button" class="btn-sm deselect-all-btn" data-section="<?= htmlspecialchars($section) ?>">
                                        <?php echo $languageService->translate('deselect_all'); ?>
                                    </button>
                                </div>
                            </div>

                            <div class="settings-list">
                                <?php foreach ($settings as $settingKey => $settingConfig): ?>
                                    <?php if (in_array($settingKey, ['view', 'platform'])): continue; endif; ?>
                                    
                                    <?php
                                    $settingId = "$section:$settingKey";
                                    $isChecked = in_array($settingId, $visibleSettings);
                                    // For blacklist mode, invert the logic
                                    if ($mode === 'blacklist') {
                                        $isChecked = !in_array($settingId, $visibleSettings);
                                    }
                                    ?>
                                    
                                    <label class="setting-checkbox">
                                        <input type="checkbox" 
                                               name="settings[]"
                                               value="<?= htmlspecialchars($settingId) ?>"
                                               data-section="<?= htmlspecialchars($section) ?>"
                                               class="setting-item"
                                               <?= $isChecked ? 'checked' : '' ?>>
                                        <span class="setting-name"><?= htmlspecialchars($settingKey) ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </form>

        <!-- Save Button -->
        <div class="form-actions">
            <button id="save-admin-filter-btn" class="btn btn-primary">
                <i class="fa fa-save"></i> <?php echo $languageService->translate('save'); ?>
            </button>
        </div>
    </div>
</div>

<style>
.admin-settings-filter {
    margin: 20px 0;
}

.radio-group {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin: 10px 0;
}

.radio-label {
    display: flex;
    align-items: center;
    cursor: pointer;
    margin: 0;
}

.radio-label input[type="radio"] {
    margin-right: 10px;
}

.settings-container {
    margin: 15px 0;
    padding: 15px;
    background: #f9f9f9;
    border-radius: 4px;
    max-height: 600px;
    overflow-y: auto;
}

.section-group {
    margin-bottom: 25px;
    padding: 15px;
    background: white;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
    padding-bottom: 10px;
    border-bottom: 2px solid #007bff;
}

.section-header h4 {
    margin: 0;
    font-weight: 600;
    color: #333;
}

.section-controls {
    display: flex;
    gap: 8px;
}

.btn-sm {
    padding: 4px 12px;
    font-size: 0.85em;
    background: #f0f0f0;
    border: 1px solid #ccc;
    border-radius: 3px;
    cursor: pointer;
    transition: background 0.2s;
}

.btn-sm:hover {
    background: #e0e0e0;
}

.settings-list {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 10px;
}

.setting-checkbox {
    display: flex;
    align-items: center;
    cursor: pointer;
    padding: 8px;
    border-radius: 3px;
    transition: background 0.2s;
}

.setting-checkbox:hover {
    background: #f0f0f0;
}

.setting-checkbox input[type="checkbox"] {
    margin-right: 8px;
    cursor: pointer;
}

.setting-name {
    font-family: 'Courier New', monospace;
    font-size: 0.9em;
    color: #333;
}

.form-actions {
    margin-top: 20px;
    display: flex;
    gap: 10px;
}

#save-admin-filter-btn {
    padding: 10px 20px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modeRadios = document.querySelectorAll('.filter-mode');
    const settingsGroup = document.getElementById('settings-group');
    const saveBtn = document.getElementById('save-admin-filter-btn');
    const form = document.getElementById('admin-filter-form');

    // Toggle settings visibility based on mode
    modeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            settingsGroup.style.display = this.value === 'show_all' ? 'none' : 'block';
            
            // Clear all checkboxes when switching to show_all
            if (this.value === 'show_all') {
                document.querySelectorAll('.setting-item').forEach(cb => cb.checked = false);
            }
        });
    });

    // Select all / Deselect all buttons
    document.querySelectorAll('.select-all-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const section = this.dataset.section;
            document.querySelectorAll(`[data-section="${section}"]`).forEach(cb => cb.checked = true);
        });
    });

    document.querySelectorAll('.deselect-all-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const section = this.dataset.section;
            document.querySelectorAll(`[data-section="${section}"]`).forEach(cb => cb.checked = false);
        });
    });

    // Save filter
    saveBtn.addEventListener('click', function(e) {
        e.preventDefault();

        const selectedMode = document.querySelector('.filter-mode:checked').value;
        let filterData = {};

        if (selectedMode === 'show_all') {
            // Send null to remove filter
            filterData = null;
        } else {
            const checkedSettings = Array.from(document.querySelectorAll('.setting-item:checked'))
                .map(cb => cb.value);

            filterData = {
                mode: selectedMode,
                settings: checkedSettings
            };
        }

        // Show loader
        $('.pageLoader').addClass('isActive');
        $('.pageLoader').find('label').html(photoboothTools.getTranslation('saving'));

        const formData = new FormData();
        formData.append('type', 'admin_filter');
        formData.append('admin_filter', JSON.stringify(filterData));
        
        if (typeof csrf !== 'undefined') {
            formData.append(csrf.key, csrf.token);
        }

        fetch('../api/admin.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    window.location.reload();
                } else {
                    alert('Error: ' + data.message);
                    $('.pageLoader').removeClass('isActive');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error saving filter');
                $('.pageLoader').removeClass('isActive');
            });
    });
});
</script>
