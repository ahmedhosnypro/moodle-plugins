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
 * Theme customizer htmleditor element class
 *
 * @package   theme_remui
 * @copyright (c) 2023 WisdmLabs (https://wisdmlabs.com/) <support@wisdmlabs.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Yogesh Shirsath
 */

namespace theme_remui\customizer\elements;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . '/adminlib.php');

use form_filemanager;
use context_system;
use stdClass;
use html_writer;
use moodle_url;

/**
 * Editor element class.
 */
class htmleditor extends base {

    /**
     * Prepare the output for the setting
     *
     * @return string element output
     */
    public function output() {
        global $OUTPUT;

        $options = $this->options;
        $label = isset($options['label']) ? $options['label'] : get_string($this->name, $this->component);
        $default = $this->get_config();

        $editor = editors_get_preferred_editor(FORMAT_HTML);
        $editor->set_text($default);
        $editor->use_editor('id_' . $this->name, array('autosave' => false));

        return $OUTPUT->render_from_template($this->component . '/customizer/elements/htmleditor', [
            'name' => $this->name,
            'label' => $label,
            'default' => $default,
            'help' => $this->get_help(),
            'options' => $this->process_options()
        ]);
    }
}
