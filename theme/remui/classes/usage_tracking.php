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
 * Edwiser Usage Tracking.
 *
 * We send anonymous user data to imporve our product compatibility with various plugins and systems.
 * Moodle's new Bootstrap theme engine
 *
 * @package   theme_remui
 * @copyright (c) 2023 WisdmLabs (https://wisdmlabs.com/) <support@wisdmlabs.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Gourav Govande
 */

namespace theme_remui;

/**
 * Edwiser Usage Tracking
 * @copyright (c) 2022 WisdmLabs (https://wisdmlabs.com/) <support@wisdmlabs.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class usage_tracking {


     // The send_usage_analytics()  method is removed from here.

    public function get_sitedata_bug_feedback_report() {
        return $this->prepare_usage_analytics();
    }

     /**
      * Prepare usage analytics
      * @return Array
      */
    private function prepare_usage_analytics() {

        global $CFG, $DB;

        // Suppressing all the errors here, just in case the setting does not exists, to avoid many if statements.
        $analyticsdata = array(
            'siteurl' => preg_replace('#^https?://#', '', rtrim(@$CFG->wwwroot, '/')), // Replace protocol and trailing slash.
            'product_name' => "Edwiser RemUI",
            'product_settings' => $this->get_plugin_settings('theme_remui'),
            // All settings in json, of current product which you are tracking.
            'active_theme' => @$CFG->theme,
            'total_courses' => $DB->count_records('course'), // Including hidden courses.
            'total_categories' => $DB->count_records('course_categories'), // Includes hidden categories.
            'total_users' => $DB->count_records('user', array('deleted' => 0)), // Exclude deleted.
            'installed_plugins' => $this->get_user_installed_plugins(), // Along with versions.
            'system_version' => @$CFG->release, // Moodle version.
            'system_lang' => @$CFG->lang,
            'system_settings' => array(
                'blog_active' => @$CFG->enableblogs,
                'cachejs_active' => @$CFG->cachejs,
                'messaging_active' => @$CFG->messaging,
                'theme_designermode_active' => @$CFG->themedesignermode,
                'multilang_filter_active' => @$CFG->filter_multilang_converted,
                'moodle_debug_mode' => @$CFG->debug,
                'moodle_debug_debugdisplay' => @$CFG->debugdisplay,
                'moodle_memory_limit' => @$CFG->extramemorylimit,
                'moodle_maxexec_time_limit' => @$CFG->maxtimelimit,
                'moodle_curlcache_ttl' => @$CFG->curlcache,
            ),
            'server_os' => @$CFG->os,
            'server_ip' => @$_SERVER['REMOTE_ADDR'],
            'web_server' => @$_SERVER['SERVER_SOFTWARE'],
            'databasename' => @$CFG->dbtype,
            'php_version' => phpversion(),
            'php_settings' => array(
                'memory_limit' => ini_get("memory_limit"),
                'max_execution_time' => ini_get("max_execution_time"),
                'post_max_size' => ini_get("post_max_size"),
                'upload_max_filesize' => ini_get("upload_max_filesize"),
                'memory_limit' => ini_get("memory_limit")
            ),
        );

        return $analyticsdata;
    }

    /**
     * Get plugins installed by user excluding the default plugins
     * @return array Plugins array
     */
    private function get_user_installed_plugins() {
        // All plugins - "external/installed by user".
        $allplugins = array();

        $pluginman = \core_plugin_manager::instance();
        $plugininfos = $pluginman->get_plugins();

        foreach ($plugininfos as $key => $modtype) {
            foreach ($modtype as $key => $plug) {
                if (!$plug->is_standard() && !$plug->is_subplugin()) {
                    // Each plugin data, // can be different structuer in case of wordpress product.
                    $allplugins[] = array(
                        'name' => $plug->displayname,
                        'versiondisk' => $plug->versiondisk,
                        'versiondb' => $plug->versiondb,
                        'versiondisk' => $plug->versiondisk,
                        'release' => $plug->release
                    );
                }
            }
        }

        return $allplugins;
    }

    /**
     * Get specific settings of the current plugin, eg: remui
     * @param  object $plugin Plugin object
     * @return array          Filtered object
     */
    private function get_plugin_settings($plugin) {
        // Get complete config.
        $pluginconfig = get_config($plugin);
        $filteredpluginconfig = array();

        // Suppressing all the errors here, just in case the setting does not exists, to avoid many if statements.

        /*General Tab*/
        $filteredpluginconfig['enableannouncement'] = @$pluginconfig->enableannouncement;
        $filteredpluginconfig['announcementtype'] = @$pluginconfig->announcementtype;
        $filteredpluginconfig['enabledismissannouncement'] = @$pluginconfig->enabledismissannouncement;
        $filteredpluginconfig['mergemessagingsidebar'] = @$pluginconfig->mergemessagingsidebar;
        $filteredpluginconfig['logoorsitename'] = @$pluginconfig->logoorsitename;
        $filteredpluginconfig['logo'] = @$pluginconfig->logo;
        $filteredpluginconfig['logomini'] = @$pluginconfig->logomini;
        $filteredpluginconfig['siteicon'] = @$pluginconfig->siteicon;
        $filteredpluginconfig['faviconurl'] = @$pluginconfig->faviconurl;
        $filteredpluginconfig['fontselect'] = @$pluginconfig->fontselect;
        $filteredpluginconfig['fontname'] = @$pluginconfig->fontname;
        $filteredpluginconfig['customcss'] = isset($pluginconfig->customcss) ? base64_encode($pluginconfig->customcss) : '';
        $filteredpluginconfig['enabledictionary'] = @$pluginconfig->enabledictionary;

        // Encode to avoid any issues with special chars in css.
        $filteredpluginconfig['activitynextpreviousbutton'] = @$pluginconfig->activitynextpreviousbutton;
        $filteredpluginconfig['enablecoursestats'] = @$pluginconfig->enablecoursestats;
        $filteredpluginconfig['poweredbyedwiser'] = @$pluginconfig->poweredbyedwiser;
        $filteredpluginconfig['navlogin_popup'] = @$pluginconfig->navlogin_popup;
        $filteredpluginconfig['loginsettingpic'] = isset($pluginconfig->loginsettingpic) ? 1 : 0;
        $filteredpluginconfig['brandlogopos'] = @$pluginconfig->brandlogopos;

        $filteredpluginconfig['courseperpage'] = @$pluginconfig->courseperpage;
        $filteredpluginconfig['enablerecentcourses'] = @$pluginconfig->enablerecentcourses;
        $homepageinstalled = \core_plugin_manager::instance()->get_plugin_info('local_remuihomepage');
        $filteredpluginconfig['new_homepage_installed'] = 0;
        if ($homepageinstalled != null) {
            $filteredpluginconfig['new_homepage_installed'] = 1;
        }
        $filteredpluginconfig['new_homepage_active'] = @$pluginconfig->frontpagechooser;

        $dashboardblocksinstalled = \core_plugin_manager::instance()->get_plugin_info('block_remuiblck');
        $filteredpluginconfig['dashboard_blocks_installed'] = 0;
        if ($dashboardblocksinstalled != null) {
            $filteredpluginconfig['dashboard_blocks_installed'] = 1;
        }
        // Adding RemUI Block plugin settings.
        $filteredpluginconfig['enablecourseprogressblock'] = @$pluginconfig->enablecourseprogressblock;
        $filteredpluginconfig['enableenrolledusersblock'] = @$pluginconfig->enableenrolledusersblock;
        $filteredpluginconfig['enablequizattemptsblock'] = @$pluginconfig->enablequizattemptsblock;
        $filteredpluginconfig['enablecourseanlyticsblock'] = @$pluginconfig->enablecourseanlyticsblock;
        $filteredpluginconfig['enablelatestmembersblock'] = @$pluginconfig->enablelatestmembersblock;
        $filteredpluginconfig['enableaddnotesblock'] = @$pluginconfig->enableaddnotesblock;
        $filteredpluginconfig['enablerecentfeedbackblock'] = @$pluginconfig->enablerecentfeedbackblock;
        $filteredpluginconfig['enablerecentforumsblock'] = @$pluginconfig->enablerecentforumsblock;
        $filteredpluginconfig['enablemanagecoursesblock'] = @$pluginconfig->enablemanagecoursesblock;
        $filteredpluginconfig['enablescheduletaskblock'] = @$pluginconfig->enablescheduletaskblock;

        // Focus Mode Setting.
        $filteredpluginconfig['enablefocusmode'] = @$pluginconfig->enablefocusmode;

        return $filteredpluginconfig;
    }

}
