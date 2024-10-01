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
 * @package     auth_cognito
 * @copyright   Oomax
 * @author      Dustin Brisebois
 * @license     MIT
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */


namespace Oomax\Model;

use Oomax\Model\Token;

/**
 * Oomax Courses Class
 */
class Courses {
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
     * @param Token plugin
     * @param String|Null courses
     */
    public function __construct(Token $plugin, $courses = "") {
        $this->plugin = $plugin->getplugin();
        $this->courses = $courses;
    }

    /**
     * Process Courses for User
     * @param Oomax\Model\User oomaxuser
     * @return void
     */
    public function processcourses(\Oomax\Model\User $oomaxuser): void {
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

                $message = new Messages($this->plugin);
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
     * @param any ctx
     * @param int courseid
     * @return bool
     */
    private function checkctx($ctx, int $courseid): bool {
        if (!$ctx) {
            $message = new Messages($this->plugin);
            $message->generatemessage([ 'courseid' => $courseid ]);
            debugging($message->returnmessage('course_not_exist'));
            return false;
        }
        return true;
    }

    /**
     * Sanity Check if is enrolled
     * @param any ctx
     * @param Oomax\Model\User oomaxuser
     * @param int courseid
     * @return bool
     */
    private function checkenrolled($ctx, \Oomax\Model\User $oomaxuser, int $courseid): bool {
        if (is_enrolled($ctx, $oomaxuser->user, '', true)) {
            $message = new Messages($this->plugin);
            $message->generatemessage([ 'courseid' => $courseid ]);
            debugging($message->returnmessage('course_user_enrolled'));
            return false;
        }
        return true;
    }
}
