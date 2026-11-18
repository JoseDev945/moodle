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
 * Library functions for theme_ecosistema.
 *
 * @package    theme_ecosistema
 * @copyright  2026 JoseDev945
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/** Default brand colour (navy blue). */
const THEME_ECOSISTEMA_DEFAULT_BRAND = '#1b3a6b';

/**
 * Main SCSS: Boost's preset followed by this theme's own rules.
 *
 * @param theme_config $theme
 * @return string
 */
function theme_ecosistema_get_main_scss_content($theme) {
    global $CFG;

    require_once($CFG->dirroot . '/theme/boost/lib.php');
    $scss = theme_boost_get_main_scss_content(theme_config::load('boost'));
    $scss .= "\n" . file_get_contents(__DIR__ . '/scss/ecosistema.scss');
    return $scss;
}

/**
 * Variables prepended to the SCSS (brand colours).
 *
 * @param theme_config $theme
 * @return string
 */
function theme_ecosistema_get_pre_scss($theme) {
    $colours = [
        'primary' => $theme->settings->brandcolor ?? '',
        'eco-accent' => $theme->settings->accentcolor ?? '',
        'eco-navbar' => $theme->settings->navbarcolor ?? '',
    ];
    $defaults = [
        'primary' => THEME_ECOSISTEMA_DEFAULT_BRAND,
        'eco-accent' => '#2f9be0',
        'eco-navbar' => '$primary',
    ];

    $scss = '';
    foreach ($colours as $name => $value) {
        $value = trim((string) $value);
        if (!preg_match('/^#[0-9a-fA-F]{3,8}$/', $value)) {
            $value = $defaults[$name];
        }
        $scss .= '$' . $name . ': ' . $value . ";\n";
    }
    $scss .= '$eco-brand: $primary;' . "\n";

    if (!empty($theme->settings->scsspre)) {
        $scss .= $theme->settings->scsspre;
    }
    return $scss;
}

/**
 * Extra SCSS appended last (admin custom SCSS).
 *
 * @param theme_config $theme
 * @return string
 */
function theme_ecosistema_get_extra_scss($theme) {
    $scss = '';

    // Optional photo behind the login card (the gradient stays as a dark overlay for contrast).
    $bgurl = $theme->setting_file_url('loginbackground', 'loginbackground');
    if (!empty($bgurl)) {
        // The compiled CSS is shared by every hostname the site answers on (localhost, tunnel...),
        // so keep only the path: the browser resolves it against whichever host it used.
        $bgurl = parse_url($bgurl, PHP_URL_PATH);
        $scss .= "html body.pagelayout-login .eco-login { background-image: "
            . "linear-gradient(rgba(10, 12, 15, .45), rgba(10, 12, 15, .65)), url('$bgurl'); }\n";
    }

    if (!empty($theme->settings->scss)) {
        $scss .= $theme->settings->scss;
    }
    return $scss;
}

/**
 * URL of the logo shown on the login card: uploaded setting, else the theme's bundled logo.
 *
 * @return string
 */
function theme_ecosistema_login_logo_url(): string {
    global $OUTPUT;

    $theme = theme_config::load('ecosistema');
    $url = $theme->setting_file_url('loginlogo', 'loginlogo');
    if (!empty($url)) {
        return $url;
    }
    return $OUTPUT->image_url('brand_logo', 'theme_ecosistema')->out(false);
}

/**
 * Serves files uploaded in the theme settings.
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options
 * @return bool
 */
function theme_ecosistema_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = []) {
    if ($context->contextlevel == CONTEXT_SYSTEM && in_array($filearea, ['loginlogo', 'loginbackground'], true)) {
        $theme = theme_config::load('ecosistema');
        if (!array_key_exists('cacheability', $options)) {
            $options['cacheability'] = 'public';
        }
        return $theme->setting_file_serve($filearea, $args, $forcedownload, $options);
    }
    send_file_not_found();
}
