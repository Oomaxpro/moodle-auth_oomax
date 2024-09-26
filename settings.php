<?php
/*
 * Copyright (C) 2010 onwards Totara Learning Solutions LTD
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package local_dash_connector
 */

defined('MOODLE_INTERNAL') || die;

// require_once(__DIR__ . '/classes/settings/TokenHelper.php');

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

	$settings->add(
		new admin_setting_configcheckbox(
			"{$pluginname}/sso_bypass",
			get_string('sso_bypass', $pluginname),
			get_string('sso_bypass_desc', $pluginname),
			false,
			true,
			false
		)
	);

    $settings->add(
		new admin_setting_configtext(
			"{$pluginname}/oomax_uri",
            get_string('oomax_uri', $pluginname),
            get_string('oomax_uri_desc', $pluginname),
            '',
            PARAM_RAW));
}
