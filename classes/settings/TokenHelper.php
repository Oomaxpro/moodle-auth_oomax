<?php

namespace auth_plugin_cognito\settings;

use admin_setting_configstoredfile;

/**
 * Class TokenHelper
 * @package local_dash_connector\settings
 */
class TokenHelper
{
	/**
	 * Token section settings.
	 *
	 * @param \admin_setting $settings
	 * @throws \coding_exception
	 * @throws \dml_exception
	 * return @void
	 */
	public static function tokenSettings($settings)
	{

        $settings->add(
            new admin_setting_configstoredfile(
                'auth_cognito/public_key',
                get_string('public_key_settings', 'auth_cognito'),
                '',
                'public_key',
                0,
                ['accepted_types' => '.json']
            )
        );
	}

}
