<?php

namespace Oomax\Model;

use Oomax\Model\Token;

class Groups
{
    private $plugin;
    private $groups;

    public function __construct(Token $plugin, Array | Null $groups)
    {
        $this->plugin = $plugin->getPlugin();
        $this->groups = $groups;
    }

    public function processGroups(\Oomax\Model\User $oomaxUser)
    {
        global $CFG;

        if (!is_null($this->groups)) {
            require_once($CFG->dirroot .'/group/lib.php');
            $groupids = array_filter(array_unique(explode(',', $groups)));

            $message = new Messages($this->plugin);
            foreach ($groupids as $groupid) {
                try {
                    // Function takes care if the user is already member of the group.
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