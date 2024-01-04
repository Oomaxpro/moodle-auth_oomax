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

require_once(__DIR__ . '/classes/settings/TokenHelper.php');

global $DB, $OUTPUT, $PAGE;

$ADMIN->add('authsettings', new admin_category('auth_cognito', get_string('pluginname', 'auth_cognito')));
$settings = new admin_settingpage($section, get_string('auth_cognito_settings', 'auth_cognito'), 'moodle/site:config');
if ($ADMIN->fulltree) {

	\auth_plugin_cognito\settings\TokenHelper::tokenSettings($settings);
}
