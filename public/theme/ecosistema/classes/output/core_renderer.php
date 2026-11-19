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

namespace theme_ecosistema\output;

/**
 * Core renderer for theme_ecosistema.
 *
 * Adds the "ecosystem" hero and service tiles on the site home and dashboard,
 * without overriding any Boost layout.
 *
 * @package    theme_ecosistema
 * @copyright  2026 JoseDev945
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_renderer extends \theme_boost\output\core_renderer {

    /**
     * Prepends the ecosystem hero to the page header on home and dashboard.
     *
     * @return string
     */
    public function full_header() {
        $html = parent::full_header();
        // Only the site home and the dashboard: other pages (e.g. messages) reuse the 'mydashboard' layout.
        if (!in_array($this->page->pagetype, ['site-index', 'my-index'], true)) {
            return $html;
        }
        if (get_config('theme_ecosistema', 'showhero') === '0') {
            return $html;
        }
        return $this->render_from_template('theme_ecosistema/ecosystem', $this->ecosystem_context()) . $html;
    }

    /**
     * Logo URL for the login card (used by core/login_layout override).
     *
     * @return string
     */
    public function eco_login_logo_url(): string {
        global $CFG;
        require_once($CFG->dirroot . '/theme/ecosistema/lib.php');
        return theme_ecosistema_login_logo_url();
    }

    /**
     * Site full name for the login card.
     *
     * @return string
     */
    public function eco_site_name(): string {
        global $SITE;
        return format_string($SITE->fullname, true, ['context' => \context_system::instance()]);
    }

    /**
     * Adds the brand web fonts (Manrope + Playfair Display) when enabled in the theme settings.
     *
     * @return string
     */
    public function standard_head_html() {
        $html = parent::standard_head_html();
        if (get_config('theme_ecosistema', 'brandfonts')) {
            $html .= '<link rel="preconnect" href="https://fonts.googleapis.com">'
                . '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>'
                . '<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800'
                . '&family=Playfair+Display:wght@700;800&display=swap">';
        }
        return $html;
    }

    /**
     * Builds the template context for the hero and tiles.
     *
     * @return array
     */
    protected function ecosystem_context(): array {
        global $USER, $SITE;

        $config = get_config('theme_ecosistema');
        $sitename = format_string($SITE->fullname, true, ['context' => \context_system::instance()]);

        $greeting = isloggedin() && !isguestuser()
            ? get_string('herogreeting', 'theme_ecosistema', s($USER->firstname))
            : get_string('herowelcome', 'theme_ecosistema', $sitename);

        $tiles = [];
        $definitions = [
            'aula' => ['icon' => 'fa-chalkboard-user', 'url' => '/my/courses.php'],
            'biblioteca' => ['icon' => 'fa-book-open', 'url' => '/course/index.php'],
            'simulaciones' => ['icon' => 'fa-flask', 'url' => 'https://phet.colorado.edu/es/'],
            'capacitacion' => ['icon' => 'fa-graduation-cap', 'url' => '/course/index.php'],
            'calendario' => ['icon' => 'fa-calendar-days', 'url' => '/calendar/view.php'],
            'comunidad' => ['icon' => 'fa-comments', 'url' => '/message/index.php'],
        ];
        foreach ($definitions as $key => $def) {
            $url = trim((string) ($config->{'url' . $key} ?? ''));
            if ($url === '') {
                $url = $def['url'];
            }
            $external = (bool) preg_match('#^https?://#i', $url);
            $tiles[] = [
                'key' => $key,
                'icon' => $def['icon'],
                'title' => get_string('tile' . $key, 'theme_ecosistema'),
                'description' => get_string('tile' . $key . '_desc', 'theme_ecosistema'),
                'url' => $external ? $url : (new \core\url($url))->out(false),
                'external' => $external,
            ];
        }

        $subtitle = trim((string) ($config->herosubtitle ?? ''));

        return [
            'greeting' => $greeting,
            'subtitle' => $subtitle !== '' ? format_string($subtitle) : get_string('herosubtitle_default', 'theme_ecosistema'),
            'tiles' => $tiles,
        ];
    }
}
