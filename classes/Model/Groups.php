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

/**
 * Oomax Groups Constructor
 */
class Groups {
    /**
     * @var $plugin
     */
    private $plugin;

    /**
     * @var $groups
     */
    private $groups;

    /**
     * Oomax Groups Constructor
     * @param Token $plugin
     * @param String $groups
     */
    public function __construct(Token $plugin, $groups = "") {
        $this->plugin = $plugin->getplugin();
        $this->groups = $groups;
    }

    /**
     * Oomax Groups Processor
     * @param string $oomaxuser Holds OOMAX user.
     * @return void
     */
    public function processgroups(\Oomax\Model\User $oomaxuser): void {
        global $CFG;

        if (!is_null($this->groups)) {
            require_once($CFG->dirroot .'/group/lib.php');
            $groupids = array_filter(array_unique(explode(',', $this->groups)));

            $message = new Messages($this->plugin);
            foreach ($groupids as $groupid) {
                try {
                    // Function takes care if the user is already member of the group.
                    if (!groups_add_member($groupid, $oomaxuser->userId())) {
                        $message->generatemessage([ 'groupid' => $groupid ]);
                        debugging($message->returnmessage('groups_failed_user_group'));
                    }
                } catch (\Exception $exc) {
                    // For now ignore errors when adding to group failed.
                    $message->generatemessage([ 'groupid' => $groupid, 'message' => $exc->getMessage() ]);
                    debugging($message->returnmessage('groups_failed_user_group_msg'));
                }
            }
        }
    }
}
