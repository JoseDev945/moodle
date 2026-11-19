<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Admin settings for theme_ecosistema.
 *
 * @package    theme_ecosistema
 * @copyright  2026 JoseDev945
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Settings are loaded on every admin tree build, even when this theme is not active
// (e.g. during a fresh install), so its constants must be loaded explicitly.
require_once(__DIR__ . '/lib.php');

if ($ADMIN->fulltree) {
    $settings = new theme_boost_admin_settingspage_tabs('themesettingecosistema', get_string('configtitle', 'theme_ecosistema'));

    // General tab.
    $page = new admin_settingpage('theme_ecosistema_general', get_string('generalsettings', 'theme_ecosistema'));

    $setting = new admin_setting_configcolourpicker('theme_ecosistema/brandcolor',
        get_string('brandcolor', 'theme_ecosistema'), get_string('brandcolor_desc', 'theme_ecosistema'),
        THEME_ECOSISTEMA_DEFAULT_BRAND);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $setting = new admin_setting_configcolourpicker('theme_ecosistema/accentcolor',
        get_string('accentcolor', 'theme_ecosistema'), get_string('accentcolor_desc', 'theme_ecosistema'), '#2f9be0');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $setting = new admin_setting_configcolourpicker('theme_ecosistema/navbarcolor',
        get_string('navbarcolor', 'theme_ecosistema'), get_string('navbarcolor_desc', 'theme_ecosistema'), '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $page->add(new admin_setting_configcheckbox('theme_ecosistema/brandfonts',
        get_string('brandfonts', 'theme_ecosistema'), get_string('brandfonts_desc', 'theme_ecosistema'), 0));

    $page->add(new admin_setting_configcheckbox('theme_ecosistema/showhero',
        get_string('showhero', 'theme_ecosistema'), get_string('showhero_desc', 'theme_ecosistema'), 1));

    $page->add(new admin_setting_configtext('theme_ecosistema/herosubtitle',
        get_string('herosubtitle', 'theme_ecosistema'), get_string('herosubtitle_desc', 'theme_ecosistema'), '',
        PARAM_TEXT));

    $settings->add($page);

    // Login page tab.
    $page = new admin_settingpage('theme_ecosistema_login', get_string('loginsettings', 'theme_ecosistema'));
    $setting = new admin_setting_configstoredfile('theme_ecosistema/loginlogo',
        get_string('loginlogo', 'theme_ecosistema'), get_string('loginlogo_desc', 'theme_ecosistema'), 'loginlogo', 0,
        ['maxfiles' => 1, 'accepted_types' => ['web_image']]);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    $setting = new admin_setting_configstoredfile('theme_ecosistema/loginbackground',
        get_string('loginbackground', 'theme_ecosistema'), get_string('loginbackground_desc', 'theme_ecosistema'),
        'loginbackground', 0, ['maxfiles' => 1, 'accepted_types' => ['web_image']]);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    $settings->add($page);

    // Ecosystem links tab.
    $page = new admin_settingpage('theme_ecosistema_links', get_string('linkssettings', 'theme_ecosistema'));
    foreach (['aula', 'biblioteca', 'simulaciones', 'capacitacion', 'calendario', 'comunidad'] as $key) {
        $page->add(new admin_setting_configtext('theme_ecosistema/url' . $key,
            get_string('tile' . $key, 'theme_ecosistema'), get_string('tileurl_desc', 'theme_ecosistema'), '',
            PARAM_RAW_TRIMMED));
    }
    $settings->add($page);

    // Advanced tab.
    $page = new admin_settingpage('theme_ecosistema_advanced', get_string('advancedsettings', 'theme_ecosistema'));
    $setting = new admin_setting_scsscode('theme_ecosistema/scsspre',
        get_string('rawscsspre', 'theme_boost'), get_string('rawscsspre_desc', 'theme_boost'), '', PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    $setting = new admin_setting_scsscode('theme_ecosistema/scss',
        get_string('rawscss', 'theme_boost'), get_string('rawscss_desc', 'theme_boost'), '', PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    $settings->add($page);
}
