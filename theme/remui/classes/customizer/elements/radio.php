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
 * Theme customizer radio element class
 *
 * @package   theme_remui
 * @copyright (c) 2023 WisdmLabs (https://wisdmlabs.com/) <support@wisdmlabs.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Yogesh Shirsath
 */

namespace theme_remui\customizer\elements;

use stdClass;

/**
 * radio element.
 */
class radio extends base {

    /**
     * Process form save
     *
     * @param array $settings Settings
     * @param array $errors  Errors array
     * @return void
     */
    public function process_form_save($settings, &$errors) {
        if (!$this->is_multiple()) {
            parent::process_form_save($settings, $errors);
            return;
        }
        $value = [];
        foreach ($settings as $setting) {
            if ($setting['name'] == $this->name) {
                $value[] = $setting['value'];
            }
        }
        set_config($this->name, json_encode($value), $this->get_component_for_config());
    }

    /**
     * Return true if multiple is true
     *
     * @return boolean
     */
    public function is_multiple() {
        return isset($this->options['multiple']) ? $this->options['multiple'] : false;
    }

    /**
     * Return help content if available in options for radio element.
     * @param  bool        $withdefault If true then default value will shown in help
     * @return bool|string              Boolean false if no help else help steing
     */
    public function get_help($withdefault = true) {
        global $OUTPUT;
        if (!isset($this->options['help'])) {
            return false;
        }
        if (isset($this->options['default']) && $this->options['default'] != '') {
            $help = '';
            if ($withdefault &&
                (!isset($this->options['withdefault']) ||
                (!isset($this->options['withdefault']) && $this->options['withdefault'])) &&
                isset($this->options['default'])
            ) {
                $radiooptions = $this->options['options'];

                $value = $this->options['options'][0];
                $help .= '<strong>' . get_string('default', 'moodle') . ': ' . $value['name']. '</strong><br>';
            }
            $help .= $this->options['help'];
            $data = new stdClass;
            $data->ltr = !right_to_left();
            $data->text = $help;
            return $OUTPUT->render_from_template('theme_remui/customizer/help_icon', $data);
        }
        return false;
    }

    /**
     * Prepare options for normal radio input
     *
     * @param  Array $options Options for radio input
     * @param  Mixed $default Default value
     *
     * @return Array
     */
    private function prepare_radio_options($options, $default) {
        $radiooptions = [];
        foreach ($options as $key => $value) {
            $option = [];
            foreach ($value as $vkey => $vvalue) {
                $option[$vkey] = $vvalue;
            }
            if ($value['name'] == $default) {
                $option['selected'] = 'selected';
            }
            $radiooptions[] = $option;
        }
        return $radiooptions;
    }

    /**
     * Prepare options for radio input with multiple enabled
     *
     * @param  Array  $options Options for radio input
     * @param  String $default Default value
     *
     * @return Array
     */
    private function prepare_multiple_radio_options($options, $default) {
        if (!$default = json_decode($default, true)) {
            $default = [];
        }
        $radiooptions = [];
        foreach ($options as $key => $value) {
            $option = [
                'key' => $key,
                'value' => $value
            ];
            if (array_search($key, $default) !== false) {
                $option['selected'] = 'selected';
            }
            $radiooptions[] = $option;
        }
        return $radiooptions;
    }

    /**
     * Prepare the output for the setting
     *
     * @return string element output
     */
    public function output() {
        global $OUTPUT;

        $options = $this->options;
        $label = isset($options['label']) ? $options['label'] : get_string($this->name, $this->component);
        $default = $options['default'];
        $hasselect = isset($options['hasselect']) ? $options['hasselect'] : false;
        if ($this->is_multiple()) {
            $radiooptions = $this->prepare_multiple_radio_options($options['options'], $default);
        } else {
            $radiooptions = $this->prepare_radio_options($options['options'], $default);
        }

        return $OUTPUT->render_from_template($this->component . '/customizer/elements/radio', [
            'name' => $this->name,
            'label' => $label,
            'groupname' => $this->name,
            'help' => $this->get_help(),
            'hasselect' => $hasselect,
            'default' => $default,
            'multiple' => $this->is_multiple(),
            'options' => $radiooptions
        ]);
    }
}
