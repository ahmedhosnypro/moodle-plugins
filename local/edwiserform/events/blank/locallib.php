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
 * Edwiser Form blank event class
 * @package   edwiserformevents_blank
 * @copyright (c) 2020 WisdmLabs (https://wisdmlabs.com/) <support@wisdmlabs.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Yogesh Shirsath
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/local/edwiserform/events/events.php');

/**
 * Edwiser Form contact event definition
 * @copyright (c) 2020 WisdmLabs (https://wisdmlabs.com/) <support@wisdmlabs.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class edwiserform_events_blank extends edwiserform_events_plugin {

    /**
     * Execute event action after form submission
     * @param  object $form Form object
     * @param  object $data Data submitted by user
     * @return object       Object with attached event data
     */
    public function attach_data($form, $data) {
        return $this->attach_common_data($form, $data);
    }

    /**
     * Returns does plugin needs activation before using this
     * @return boolean
     * @since Edwiser Form 1.0.0
     */
    public function is_pro_plugin() {
        return true;
    }

    /**
     * Returns true if form support submission of non logged-in users.
     *
     * @return bool
     */
    public function support_non_logged_in() {
        return true;
    }
}
