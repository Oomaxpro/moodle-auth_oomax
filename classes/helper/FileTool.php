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
 * This file is part of the Oomax Pro Exam Activity package.
 *
 * @package     auth_cognito
 * @author      Bojan Bazdar
 * @license     MIT
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace auth_cognito\helper;

use context_system;
use DirectoryIterator;

defined('MOODLE_INTERNAL') || die;

/**
 * Class FileTool
 * @package mod_exam\helper
 */
class FileTool {

    /** @var string $filepath */
    private $filepath = '';

    /**
     * Get value of public key.
     *
     * @return \stored_file
     * @throws \coding_exception
     * @throws \dml_exception
     */
    private function get_settings_public_key() {

        $context = context_system::instance();
        $fs = get_file_storage();
        $files = $fs->get_area_files($context->id, 'auth_cognito', 'public_key');

        return end($files);
    }

    /**
     * Get certificate file path.
     *
     * @return string
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function get_public_key_path() {
        return $this->public_key_path_exists($this->get_data_root_path(), $this->get_file_content_hash());
    }

    /**
     * Get file content hash.
     *
     * @return string
     * @throws \coding_exception
     * @throws \dml_exception
     */
    private function get_file_content_hash() {

        $file = $this->get_settings_public_key();

        if ($file instanceof \stored_file) {
            return $file->get_contenthash();
        }

    }

    /**
     * Get moodledata path.
     *
     * @return mixed
     */
    private function get_data_root_path() {

        global $CFG;

        return $CFG->dataroot;
    }

    /**
     * Check if the hash file name exists in the data root.
     *
     * @param $moodledatapath
     * @param $publickeypath
     * @return string
     */
    private function public_key_path_exists($moodledatapath, $publickeypath) {
        $directories = new DirectoryIterator($moodledatapath);

        foreach ($directories as $directory) {
            if (!$directory->isDot()) {
                if ($directory->isDir()) {
                    $this->public_key_path_exists("$moodledatapath/$directory", $publickeypath);
                } else {
                    if (strpos($publickeypath, $directory->getFilename()) !== false) {
                        $this->filePath = $moodledatapath . "/" . $directory->getFilename();
                    }
                }
            }
        }

        if (!empty($this->filePath)) {
            return $this->filePath;
        }
    }
}
