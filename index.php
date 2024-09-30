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
 * @package auth_oomax
 * @author Bojan Bazdar
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__.'/vendor/autoload.php');
require_once($CFG->dirroot .'/user/lib.php');

use Oomax\Model;

global $SESSION;

$token = required_param('token', PARAM_RAW);
$logout = required_param('logout', PARAM_RAW);
$courses = optional_param('courses', null, PARAM_SEQUENCE);
$groups = optional_param('groups', null, PARAM_SEQUENCE);
$audiences = optional_param('audiences', null, PARAM_SEQUENCE);
$SESSION->logout = $logout;

$oomaxToken = new Model\Token($token);
$oomaxToken->getDataFromToken();

$wantsurl = Null;
if (isset($SESSION->wantsurl)) 
{
    $wantsurl = $SESSION->wantsurl;
}

// If payload exist process user
if ($oomaxToken->isAuthorized()) 
{
    $oomaxToken->getPayload();
    $oomaxUser = new Model\User($oomaxToken);

    $oomaxUser->processUserLocale();
    $USER = $oomaxUser->UserLogin($oomaxUser);
    $oomaxUser->generateOomaxCookie();

    if (!is_null($courses)) 
    {
        $oomaxCourses = new Model\Courses($oomaxToken, $courses);
        $oomaxCourses->processCourses($oomaxUser);
    }

    if (!is_null($groups)) 
    {
        $oomaxGroups = new Model\Groups($oomaxToken, $groups);
        $oomaxGroups->processGroups($oomaxUser);
    }

    if (!is_null($audiences)) 
    {
        $oomaxAudiences = new Model\Audiences($oomaxToken, $audiences);
        $oomaxAudiences->processAudiences($oomaxUser);
    }

    if (is_null($wantsurl)) 
    {
        $wantsurl = new moodle_url(optional_param('wantsurl', $CFG->wwwroot, PARAM_URL));
    }
    
    redirect($wantsurl);
} else {
    throw new moodle_exception('oomaxtoken', '', '', null, get_string('invalid_token', 'auth_cognito'));
}