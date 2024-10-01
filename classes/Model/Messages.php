<?php
<<<<<<< HEAD

namespace Oomax\Model;

class Messages
{
    private String $plugin;
    private \stdClass $message;

    public function __construct(String $plugin)
    {
        $this->plugin = $plugin;
    }

    public function generateMessage(Array $message)
    {
        $this->message = Array();
        foreach ($message as $k => $m)
        {
            $this->message[$k] = $m;
        } 
    }

    public function returnMessage(String $name)
    {
        return get_string($name, $this->plugin, $this->message);
    }
}
=======
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
 * @copyright   Oomax
 * @author      Dustin Brisebois
 * @license     MIT
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace Oomax\Model;

/**
 * Messages Class: Generates Moodle Debug Messages
 */
class Messages {
    /**
     * @var string
     */
    private String $plugin;

    /**
     * @var stdClass
     */
    private \stdClass $message;

    /**
     * Constructor for Oomax Debug Messages
     * @param string plugin
     */
    public function __construct(String $plugin) {
        $this->plugin = $plugin;
    }

    /**
     * Generates the Message
     * @param array message
     * @return void
     */
    public function generatemessage(Array $message): void {
        $this->message = [];
        foreach ($message as $k => $m) {
            $this->message[$k] = $m;
        }
    }

    /**
     * Returns the string for messsaging
     * @param string name
     * @return string
     */
    public function returnmessage(String $name): string {
        return get_string($name, $this->plugin, $this->message);
    }
}
>>>>>>> CLDOPS-525v5
