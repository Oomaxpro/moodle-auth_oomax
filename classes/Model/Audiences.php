<?php
/** 
 * This file is part of Moodle - http://moodle.org/
 * Moodle is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Moodle is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
 * php version 8.1.1

 * @category Engine

 * @package   Auth_Oomax
 * @author    Dustin Brisebois <dustin@oomaxpro.com>
 * @copyright 2022 OOMAX PRO SOFTWARE INC.
 
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @link    http://www.gnu.org/copyleft/gpl.html
 */


namespace Oomax\Model;

use Oomax\Model\Token;
use Oomax\Model\User;
use Oomax\Model\Messages;


/**
 * Oomax Audiences Class
 * 
 * @category Engine

 * @package   Auth_Oomax
 * @author    Dustin Brisebois <dustin@oomaxpro.com>
 * @copyright 2022 OOMAX PRO SOFTWARE INC.
 
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @link    http://www.gnu.org/copyleft/gpl.html
 */
class Audiences
{
    /**
     * This class controls audience enrolment
     * 
     * @var string
     */
    private $plugin;

    /**
     * This is the audience integers that we need to enrol
     * 
     * @var string|null
     */
    private $audiences;

    /**
     * Audience Oomax Constructor
     * 
     * @param $plugin    Token        what is the decrypt
     * @param String|null $audiences what audiences should we deal with
     */
    public function __construct(Token $plugin, $audiences = "")
    {
        $this->plugin = $plugin->getplugin();
        $this->audiences = $audiences;
    }

    /**
     * Process Audiences for Oomax
     * 
     * @param $oomaxuser this is the passed user var
     * 
     * @return void
     */
    public function processaudiences(User $oomaxuser): void
    {
        global $CFG, $DB;

        if (!is_null($this->audiences)) {
            include_once $CFG->dirroot .'/cohort/lib.php';
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
