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
 * Authentication plugin: self-registration without email confirmation.
 *
 * @package    auth_registro
 * @copyright  2026 JoseDev945
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/auth/email/auth.php');

/**
 * Same as email-based self-registration, but the account is active at once
 * and the new user is logged in straight away (no confirmation email needed).
 */
class auth_plugin_registro extends auth_plugin_email {

    /**
     * Constructor.
     */
    public function __construct() {
        $this->authtype = 'registro';
        $this->config = get_config('auth_registro');
    }

    /**
     * Creates the account already confirmed and logs the user in.
     *
     * @param stdClass $user New user record from the signup form (plain-text password).
     * @param bool $notify When true, log the user in and redirect; otherwise just return.
     * @return bool
     */
    public function user_signup($user, $notify = true) {
        global $CFG, $SESSION;
        require_once($CFG->dirroot . '/user/profile/lib.php');
        require_once($CFG->dirroot . '/user/lib.php');

        // The repeated password is only for validation.
        unset($user->password2);

        $plainpassword = $user->password;
        $user->password = \core\di::get(\core\authentication\password::class)->hash($user->password);
        $user->confirmed = 1;
        if (empty($user->calendartype)) {
            $user->calendartype = $CFG->calendartype;
        }

        $user->id = \core\user::create_user($user, false, false);
        \core\user::add_password_history($user->id, $plainpassword);
        profile_save_data($user);

        \core\event\user_created::create_from_userid($user->id)->trigger();

        if (!$notify) {
            return true;
        }

        $user = get_complete_user_data('id', $user->id);
        complete_user_login($user);

        $url = !empty($SESSION->wantsurl) ? $SESSION->wantsurl : new moodle_url('/my/');
        unset($SESSION->wantsurl);
        redirect($url, get_string('welcomenewuser', 'auth_registro', fullname($user)), null,
            \core\output\notification::NOTIFY_SUCCESS);
    }

    /**
     * Accounts are confirmed on creation, so there is nothing to confirm.
     *
     * @return bool
     */
    public function can_confirm() {
        return false;
    }
}
