<?php

namespace Oomax\Model;

use Oomax\Model\Token;
use Oomax\Model\User;
use Oomax\Model\Messages;

class Audiences
{
    private $plugin;
    private $audiences;

    public function __construct(Token $plugin, Array | Null $audiences)
    {
        $this->plugin = $plugin->getPlugin();
        $this->audiences = $audiences;
    }

    public function processAudiences(User $oomaxUser): void
    {
        global $CFG, $DB;

        if (!is_null($this->audiences)) {
            require_once($CFG->dirroot .'/cohort/lib.php');
            $cohortids = array_filter(array_unique(explode(',', $audiences)));
            foreach ($cohortids as $cohortid) {
                try {
                    // Check that cohort exists.
                    $DB->get_record('cohort', ['id' => $cohortid], 'id', MUST_EXIST);
                    // Function takes care if the user is already member of the cohort.
                    cohort_add_member($cohortid, $oomaxUser->userId());
                } catch (\Exception $exc) {
                    // For now ignore errors when adding to cohort failed.
                    $message = new Messages($this->plugin);
                    $message->generateMessage([ 'cohortid' => $cohortid, 'message' => $exc->getMessage()]);
                    debugging($message->returnMessage('audience_fail_enrol'));
                }
            }
        }
    }
}