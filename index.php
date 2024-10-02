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
 * @author    Bojan Bazdar / Dustin Brisebois <dustin@oomaxpro.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright 2024 OOMAX PRO SOFTWARE INC
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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

$oomaxtoken = new Model\Token($token);
$oomaxtoken->getDataFromToken();

$wantsurl = null;
if (isset($SESSION->wantsurl)) {
    $wantsurl = $SESSION->wantsurl;
}

// If payload exist process user.
if ($oomaxtoken->isAuthorized()) {
    $oomaxtoken->getPayload();
    $oomaxuser = new Model\User($oomaxtoken);

    $oomaxuser->processUserLocale();
    $USER = $oomaxuser->UserLogin($oomaxuser);
    $oomaxuser->generateOomaxCookie();
}
if (!is_null($courses)) {
    $oomaxcourses = new Model\Courses($oomaxtoken, $courses);
    $oomaxcourses->processCourses($oomaxuser);
    $oomaxtoken->getdatafromtoken();
}
$wantsurl = null;
if (isset($SESSION->wantsurl)) {
    $wantsurl = $SESSION->wantsurl;
}

// If payload exist process user.
if ($oomaxtoken->isauthorized()) {
    $oomaxtoken->getpayload();
    $oomaxuser = new Model\User($oomaxtoken);

    $oomaxuser->processuserlocale();
    $USER = $oomaxuser->userlogin($oomaxuser);
    $oomaxuser->generateoomaxcookie();

    if (!is_null($courses)) {
        $oomaxcourses = new Model\Courses($oomaxtoken, $courses);
        $oomaxcourses->processcourses($oomaxuser);
    }

    if (!is_null($groups)) {
        $oomaxgroups = new Model\Groups($oomaxtoken, $groups);
    }

    if (!is_null($audiences)) {
        $oomaxaudiences = new Model\Audiences($oomaxtoken, $audiences);
        $oomaxaudiences->processaudiences($oomaxuser);
    }

    if (is_null($wantsurl)) {
        $wantsurl = new moodle_url(optional_param('wantsurl', $CFG->wwwroot, PARAM_URL));
    }

    redirect($wantsurl);
} else {
    throw new moodle_exception('oomaxtoken', '', '', null, get_string('invalid_token', 'auth_cognito'));
}
