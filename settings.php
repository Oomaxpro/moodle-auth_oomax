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
 * @author      Bojan Bazdar / Dustin Brisebois
 * @license     GPL
 * @copyright   Oomax
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

defined('MOODLE_INTERNAL') || die;

global $DB, $OUTPUT, $PAGE;

$pluginname = 'auth_cognito';
$ADMIN->add('authsettings', new admin_category($pluginname, get_string('pluginname', $pluginname)));
$settings = new admin_settingpage($section, get_string("{$pluginname}_settings", $pluginname), 'moodle/site:config');
if ($ADMIN->fulltree) {
    $settings->add(
        new admin_setting_configcheckbox(
            "{$pluginname}/field_lock_email",
            get_string('config_lock_email', $pluginname),
            get_string('config_lock_email_desc', $pluginname),
            'locked',
            'locked',
            'unlocked'
        )
    );
}
