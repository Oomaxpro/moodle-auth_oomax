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
 * Authentication Plugin: Moodle Network Authentication
 * Multiple host authentication support for Moodle Network.
 *
 * @package auth_cognito
 * @author Bojan Bazdar
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

require_once __DIR__ . '/../../config.php';
require_once(__DIR__.'/vendor/autoload.php');
require_once($CFG->dirroot .'/user/lib.php');

global $SESSION;

define('AUTH_COGNITO_ERROR_USER_SUSPENDED', 'user_is_suspended');
define('AUTH_COGNITO_ERROR_INVALID_TOKEN', 'invalid_token');
define('AUTH_COGNITO_ERROR_NO_EMAIL', 'missing_email');

$user = new \auth_cognito\model\User();

$token = required_param('token',    PARAM_RAW);
$logout = required_param('logout',    PARAM_RAW);
$courses = optional_param('courses', '', PARAM_SEQUENCE);
$groups = optional_param('groups', '', PARAM_SEQUENCE);
$audiences = optional_param('audiences', '', PARAM_SEQUENCE);
$SESSION->logout = $logout;

$payload = $user->getDataFromToken($token);

if (empty($payload)) {
    redirect(new moodle_url($logout, ['code' => AUTH_COGNITO_ERROR_INVALID_TOKEN]));
}

// If payload exist process user
if ($payload) {
    $payload = json_decode(json_encode($payload), true);

    if (empty($payload['email'])) {
        redirect(new moodle_url($logout, ['code' => AUTH_COGNITO_ERROR_NO_EMAIL]));
    }

    if (isset($payload['locale'])) {
        // Convert language code from oomax format (e.g. fr-CA) to Moodle format (e.g. fr_ca).
        $lang = strtolower(str_replace('-', '_', $payload['locale']));
        $sm = get_string_manager();
        // Find appropriate installed language, or use default system language.
        if (!$sm->translation_exists($lang)) {
            // Try base language.
            $lang = explode('_', $lang)[0];
            if (!$sm->translation_exists($lang)) {
                // Use default language.
                $lang = core_user::get_property_default('lang');
            }
        }
        $payload['locale'] = $lang;
    }

    // Convert email to lowercase
    $email = strtolower($payload['email']);

    // Get user by email
    $student = $DB->get_record_select('user', 'LOWER(email) = ?', [$email]);

    if ($student) {
        if (!empty($student->suspended)) {
            $SESSION->loginerrormsg = get_string("invalidlogin");
            redirect(new moodle_url($logout, ['code' => AUTH_COGNITO_ERROR_USER_SUSPENDED]));
        }

        // If user exist perform login and redirect
        if (isset($payload['locale']) && $payload['locale'] != $student->lang) {
            $student->lang = $payload['locale'];

            user_update_user($student, false, false);
        }

        $USER = complete_user_login($student);

    } else {
        // If user doesn't exist create user and perform login and redirect.
        $userId = $user->createUser($payload);
        $userObj = $DB->get_record("user", ["id" => $userId]);
        $USER = complete_user_login($userObj);
    }

    if (!empty($courses)) {
        require_once($CFG->libdir . '/enrollib.php');
        $studentroles = get_archetype_roles('student');
        $studentroleid = reset($studentroles)->id;
        $courseids = array_filter(array_unique(explode(',', $courses)));
        foreach ($courseids as $courseid) {
            $ctx = context_course::instance($courseid, IGNORE_MISSING);
            if (!$ctx) {
                // Course does not exist.
                debugging("Course $courseid does not exist");
                continue;
            }
            if (is_enrolled($ctx, $USER, '', true)) {
                // Already enrolled.
                debugging("User is already enrolled in course $courseid");
                continue;
            }
            try {
                // Enrol user using manual enrollment method.
                if (!enrol_try_internal_enrol($courseid, $USER->id, $studentroleid)) {
                    debugging("Failed to enrol user in course $courseid");
                }
            } catch (Exception $exc) {
                // For now ignore errors when enrollment failed.
                debugging("Failed to enrol user in course $courseid : " . $exc->getMessage());
            }
        }
    }

    if (!empty($groups)) {
        require_once($CFG->dirroot .'/group/lib.php');
        $groupids = array_filter(array_unique(explode(',', $groups)));
        foreach ($groupids as $groupid) {
            try {
                // Function takes care if the user is already member of the group.
                if (!groups_add_member($groupid, $USER->id)) {
                    debugging("Failed to add user to group $groupid");
                }
            } catch (Exception $exc) {
                // For now ignore errors when adding to group failed.
                debugging("Failed to add user to group $groupid : " . $exc->getMessage());
            }
        }
    }

    if (!empty($audiences)) {
        require_once($CFG->dirroot .'/cohort/lib.php');
        $cohortids = array_filter(array_unique(explode(',', $audiences)));
        foreach ($cohortids as $cohortid) {
            try {
                // Check that cohort exists.
                $DB->get_record('cohort', ['id' => $cohortid], 'id', MUST_EXIST);
                // Function takes care if the user is already member of the cohort.
                cohort_add_member($cohortid, $USER->id);
            } catch (Exception $exc) {
                // For now ignore errors when adding to cohort failed.
                debugging("Failed to add user to audience $cohortid : " . $exc->getMessage());
            }
        }
    }

    redirect('/');
} else {
    throw new moodle_exception('cognitotoken', '', '', null,
        'User token is invalid.');
}
