<?php
/**
 * This file is part of Moodle - http://moodle.org/

 * Moodle is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * Moodle is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
 * php version 8.1.1
 * 
 * @category Info

 * This file is part of the Oomax Pro Authentication package.
 *
 * @package   Auth_Cognito
 * @author    Dustin Brisebois <dustin@oomaxpro.com>
 * @copyright 2024 OOMAX PRO SOFTWARE
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @link      http://www.gnu.org/copyleft/gpl.html
 *
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

defined('MOODLE_INTERNAL') || die();

$plugin->version   = 2024100100;   // The current plugin version (Date: YYYYMMDDXX).
$plugin->release   = '1.4.0.0';
$plugin->requires  = 2015051100;             // MDL-2.9+
$plugin->component = 'auth_cognito';   // Full name of the plugin
$plugin->maturity  = 'MATURITY_STABLE';