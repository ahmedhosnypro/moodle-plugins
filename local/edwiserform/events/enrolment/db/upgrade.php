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
 * Upgrade hook
 * @package   edwiserformevents_enrolment
 * @copyright (c) 2020 WisdmLabs (https://wisdmlabs.com/) <support@wisdmlabs.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Yogesh Shirsath
 */

defined('MOODLE_INTERNAL') || die();

/**
 * upgrade this edwiserformevents enrolment database
 * @param int $oldversion The old version of the edwiserformevents enrolment plugin
 * @return bool
 */
function xmldb_edwiserformevents_enrolment_upgrade($oldversion) {
    global $CFG, $DB;
    require_once($CFG->dirroot . '/local/edwiserform/events/enrolment/db/install.php');
    xmldb_edwiserformevents_enrolment_install();

    $dbman = $DB->get_manager();

    $table = new xmldb_table('efb_forms_enrolment');

    // Adding enroll field.
    $field = new xmldb_field('enroll', XMLDB_TYPE_INTEGER, 1, null, false, false, 1);
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }
    return true;
}
