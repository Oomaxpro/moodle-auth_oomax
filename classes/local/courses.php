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
 * This file is part of the Oomax Pro Authentication package.
 *
 * @package   auth_cognito
 * @copyright 2024 OOMAX PRO SOFTWARE INC
 * @author    Dustin Brisebois <dustin@oomaxpro.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace auth_cognito\local;

use \auth_cognito\local\messages;
use \auth_cognito\local\token;
use \auth_cognito\local\user;

/**
 * Oomax Courses Class
 */
class courses {
    /**
     * @var string
     */
    private string $plugin;

    /**
     * @var string | array | null
     */
    private $courses;

    /**
     * Constructor for Courses
     * @param token $plugin
     * @param String $courses
     */
    public function __construct(token $plugin, $courses = "") {
        $this->plugin = $plugin->getplugin();
        $this->courses = $courses;
    }

    /**
     * processcourses
     *
     * @param  auth_cognito\model\user $oomaxuser
     * @return void
     */
    public function processcourses(user $oomaxuser): void {
        global $CFG;

        if (!is_null($this->courses)) {
            require_once($CFG->libdir . '/enrollib.php');
            $userroles = get_archetype_roles('student');
            $userroleid = reset($userroles)->id;
            $courseids = array_filter(array_unique(explode(',', $this->courses)));

            foreach ($courseids as $courseid) {
                $ctx = \context_course::instance($courseid, IGNORE_MISSING);

                $this->checkctx($ctx, $courseid);
                $this->checkenrolled($ctx, $oomaxuser, $courseid);

                $message = new messages($this->plugin);
                try {
                    // Enrol user using manual enrollment method.
                    $message->generatemessage([ 'courseid' => $courseid ]);
                    if (!enrol_try_internal_enrol($courseid, $oomaxuser->userid(), $userroleid)) {
                        debugging($message->returnmessage('course_failed_enrol'));
                    }
                } catch (\Exception $exc) {
                    // For now ignore errors when enrollment failed.
                    $message->generatemessage([ 'courseid' => $courseid, 'message' => $exc->getmessage() ]);
                    debugging($message->returnmessage('course_failed_enrol_msg'));
                }
            }
        }
    }

    /**
     * Sanity Check if is context
     * @param string $ctx Checking context.
     * @param int $courseid Store course IDs.
     * @return bool
     */
    private function checkctx($ctx, int $courseid): bool {
        if (!$ctx) {
            $message = new messages($this->plugin);
            $message->generatemessage([ 'courseid' => $courseid ]);
            debugging($message->returnmessage('course_not_exist'));
            return false;
        }
        return true;
    }

    /**
     * checkenrolled
     *
     * @param  int $ctx
     * @param  \auth_cognito\model\user $oomaxuser
     * @param  int $courseid
     * @return bool
     */
    private function checkenrolled($ctx, user $oomaxuser, int $courseid): bool {
        if (is_enrolled($ctx, $oomaxuser->user, '', true)) {
            $message = new messages($this->plugin);
            $message->generatemessage([ 'courseid' => $courseid ]);
            debugging($message->returnmessage('course_user_enrolled'));
            return false;
        }
        return true;
    }
}
