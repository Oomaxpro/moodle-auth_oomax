<?php
<<<<<<< HEAD
=======
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

>>>>>>> CLDOPS-525v5

namespace Oomax\Model;

use Oomax\Model\Token;

<<<<<<< HEAD
class Groups
{
    private $plugin;
    private $groups;

    public function __construct(Token $plugin, String | Null $groups = "")
    {
        $this->plugin = $plugin->getPlugin();
        $this->groups = $groups;
    }

    public function processGroups(\Oomax\Model\User $oomaxUser)
    {
=======
/**
 * Oomax Groups Constructor
 */
class Groups {
    /**
     * @var string
     */
    private $plugin;

    /**
     * @var string|null
     */
    private $groups;

    /**
     * Oomax Groups Constructor
     */
    public function __construct(Token $plugin, $groups = "") {
        $this->plugin = $plugin->getplugin();
        $this->groups = $groups;
    }

    /**
     * Oomax Groups Processor
     * @param User oomaxuser
     * @return void
     */
    public function processgroups(\Oomax\Model\User $oomaxuser): void {
>>>>>>> CLDOPS-525v5
        global $CFG;

        if (!is_null($this->groups)) {
            require_once($CFG->dirroot .'/group/lib.php');
            $groupids = array_filter(array_unique(explode(',', $this->groups)));

            $message = new Messages($this->plugin);
            foreach ($groupids as $groupid) {
                try {
                    // Function takes care if the user is already member of the group.
<<<<<<< HEAD
                    if (!groups_add_member($groupid, $oomaxUser->userId())) {
                        $message->generateMessage([ 'groupid' => $groupid ]);
                        debugging($message->returnMessage('groups_failed_user_group'));
                    }
                } catch (\Exception $exc) {
                    // For now ignore errors when adding to group failed.
                    $message->generateMessage([ 'groupid' => $groupid, 'message' => $exc->getMessage() ]);
                    debugging($message->returnMessage('groups_failed_user_group_msg'));
                }
            }
        }

    }
}
=======
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
>>>>>>> CLDOPS-525v5
