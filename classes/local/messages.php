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
 * @copyright 2024 OOMAX PRO SOFTWARE INC
 * @author    Dustin Brisebois <dustin@oomaxpro.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace auth_cognito\local;

/**
 * Messages Class: Generates Moodle Debug Messages
 */
class messages {
    /**
     * @var string
     */
    private String $plugin;

    /**
     * @var stdClass
     */
    private \stdClass $message;

    /**
     * __construct
     *
     * @param  String $plugin
     * @return void
     */
    public function __construct(String $plugin) {
        $this->plugin = $plugin;
    }

    /**
     * generatemessage
     *
     * @param  Array $message
     * @return void
     */
    public function generate_message(Array $message): void {
        $this->message = new \stdClass();
        foreach ($message as $k => $m) {
            $this->message->$k = $m;
        }
    }

    /**
     * returnmessage
     *
     * @param  String $name
     * @return string
     */
    public function return_message(String $name): string {
        return get_string($name, $this->plugin, $this->message);
    }
}
