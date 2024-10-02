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


namespace Oomax\Model;

use Oomax\Model\Token;
use Oomax\Model\User;
use Oomax\Model\Messages;


/**
 * Oomax Audiences Class
 */
class Audiences {
    /**
     * @var string
     */
    private $plugin;

    /**
     * @var string|null
     */
    private $audiences;

    /**
     * Audience Oomax Constructor
     * @param Token plugin
     * @param String|null audiences
     */
    public function __construct(Token $plugin, $audiences = "") {
        $this->plugin = $plugin->getplugin();
        $this->audiences = $audiences;
    }

    /**
     * Process Audiences for Oomax
     * @param User oomaxuser
     * @return void
     */
    public function processaudiences(User $oomaxuser): void {
        global $CFG, $DB;

        if (!is_null($this->audiences)) {
            require_once($CFG->dirroot .'/cohort/lib.php');
            $cohortids = array_filter(array_unique(explode(',', $this->audiences)));
            foreach ($cohortids as $cohortid) {
                try {
                    // Check that cohort exists.
                    $DB->get_record('cohort', ['id' => $cohortid], 'id', MUST_EXIST);
                    // Function takes care if the user is already member of the cohort.
                    cohort_add_member($cohortid, $oomaxuser->userid());
                } catch (\Exception $exc) {
                    // For now ignore errors when adding to cohort failed.
                    $message = new Messages($this->plugin);
                    $message->generatemessage([ 'cohortid' => $cohortid, 'message' => $exc->getmessage()]);
                    debugging($message->returnmessage('audience_fail_enrol'));
                }
            }
        }
    }
}
