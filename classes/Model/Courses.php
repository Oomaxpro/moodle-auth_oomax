<?php

namespace Oomax\Model;

use Oomax\Model\Token;

class Courses
{
    private $plugin;
    private $courses;

    public function __construct(Token $plugin, Array | Null $courses = Array())
    {
        $this->plugin = $plugin->getPlugin();
        $this->courses = $courses;
    }

    public function processCourses(\Oomax\Model\User $oomaxUser): void
    {
        global $CFG;

        if (!is_null($this->courses)) {
            require_once($CFG->libdir . '/enrollib.php');
            $userroles = get_archetype_roles('student');
            $userroleid = reset($userroles)->id;
            $courseids = array_filter(array_unique(explode(',', $courses)));
            foreach ($courseids as $courseid) {
                $ctx = \context_course::instance($courseid, IGNORE_MISSING);

                $this->checkCtx($ctx, $courseid);
                $this->checkEnrolled($ctx, $oomaxUser, $courseid);

                $message = new Messages($this->plugin);
                try {
                    // Enrol user using manual enrollment method.
                    $message->generateMessage([ 'courseid' => $courseid ]);
                    if (!enrol_try_internal_enrol($courseid, $oomaxUser->userId(), $userroleid)) debugging($message->returnMessage('course_failed_enrol'));
                } catch (\Exception $exc) {
                    // For now ignore errors when enrollment failed.
                    $message->generateMessage([ 'courseid' => $courseid, 'message' => $exc->getMessage() ]);
                    debugging($message->returnMessage('course_failed_enrol_msg'));
                }
            }
        }
    }

    private function checkCtx($ctx, int $courseid): bool
    {
        if (!$ctx) 
        {
            $message = new Messages($this->plugin);
            $message->generateMessage([ 'courseid' => $courseid ]);
            debugging($message->returnMessage('course_not_exist'));
            return false;
        }
        return true;
    }

    private function checkEnrolled($ctx, \Oomax\Model\User $oomaxUser, $courseid): bool
    {
        if (is_enrolled($ctx, $oomaxUser->user, '', true)) 
        {
            $message = new Messages($this->plugin);
            $message->generateMessage([ 'courseid' => $courseid ]);
            debugging($message->returnMessage('course_user_enrolled'));
            return false;
        }
        return true;
    }


}