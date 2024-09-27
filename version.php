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
 * @package     auth_oomax
 * @author      Bojan Bazdar
 * @license     MIT
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

defined('MOODLE_INTERNAL') || die();

$plugin->version   = 2024092502;             // The current plugin version (Date: YYYYMMDDXX).
$plugin->release   = '1.3.0.5';
$plugin->requires  = 2015051100;             // MDL-2.9+
$plugin->component = 'auth_cognito';         // Full name of the plugin (used for diagnostics).
$plugin->maturity  = 'MATURITY_STABLE';