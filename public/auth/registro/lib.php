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
 * Signup form callbacks for auth_registro.
 *
 * @package    auth_registro
 * @copyright  2026 JoseDev945
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Whether this plugin is the one handling self-registration.
 *
 * @return bool
 */
function auth_registro_is_signup_auth(): bool {
    global $CFG;
    return ($CFG->registerauth ?? '') === 'registro' && is_enabled_auth('registro');
}

/**
 * Adds a "repeat password" field right after the password field.
 *
 * @param MoodleQuickForm $mform
 */
function auth_registro_extend_signup_form($mform) {
    if (!auth_registro_is_signup_auth()) {
        return;
    }

    $element = $mform->createElement('password', 'password2', get_string('passwordagain', 'auth_registro'), [
        'maxlength' => MAX_PASSWORD_CHARACTERS,
        'size' => 12,
        'autocomplete' => 'new-password',
    ]);
    $mform->insertElementBefore($element, 'email');
    $mform->setType('password2', core_user::get_property_type('password'));
    $mform->addRule('password2', get_string('missingpassword'), 'required', null, 'client');

    // Ask for the email only once. Core validation still compares "email2" with "email",
    // so keep it as a hidden field that always mirrors the submitted email.
    if ($mform->elementExists('email2')) {
        $mform->removeElement('email2');
        $mform->addElement('hidden', 'email2');
        $mform->setType('email2', core_user::get_property_type('email'));
        $mform->setConstant('email2', optional_param('email', '', PARAM_RAW_TRIMMED));
    }
}

/**
 * Checks both passwords match.
 *
 * @param array $data Submitted form data.
 * @return array Errors keyed by field name.
 */
function auth_registro_validate_extend_signup_form($data) {
    if (!auth_registro_is_signup_auth()) {
        return [];
    }

    $errors = [];
    if (!isset($data['password2']) || $data['password2'] === '') {
        $errors['password2'] = get_string('missingpassword');
    } else if (($data['password'] ?? '') !== $data['password2']) {
        $errors['password2'] = get_string('passwordsdiffer');
    }
    return $errors;
}
