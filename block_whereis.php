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
 * Form for editing HTML block instances.
 *
 * @package   block_whereis
 * @copyright 2013 Valery Fremaux (valery.fremaux@gmail.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_whereis extends block_base {

    function init() {
        global $PAGE;

        $this->title = get_string('pluginname', 'block_whereis');
        $PAGE->requires->js('/blocks/whereis/js/js.js');
    }

    function has_config() {
        return true;
    }

    function applicable_formats() {
        return array('all' => true);
    }

    function specialization() {
        $this->title = isset($this->config->title) ? format_string($this->config->title) : get_string('whereisjohnny', 'block_whereis') ;
    }

    function instance_allow_multiple() {
        return false;
    }

    function get_content() {
        global $CFG;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->footer = '';

        $context = context_block::instance($this->instance->id);
        if (!has_capability('block/whereis:searchlocation', $context)) {
            $this->content->text = '';
            return $this->content;
        }

        $this->content->text = '<input id="id_searchwhereis" name="searchwhereis" type="text" size="15" />';
        $this->content->text .= '<input type="button" value="Go!" id="block-whereis-go" />';
        $this->content->text .= '<div id="block-whereis-results"></div>';

        return $this->content;
    }


    /**
     * Serialize and store config data
     */
    function instance_config_save($data, $nolongerused = false) {
        global $DB;

        parent::instance_config_save($data, $nolongerused);
    }

    function instance_delete() {
        global $DB;

        return true;
    }

    /**
     * The block should only be dockable when the title of the block is not empty
     * and when parent allows docking.
     *
     * @return bool
     */
    public function instance_can_be_docked() {
        return (!empty($this->config->title) && parent::instance_can_be_docked());
    }

    public function get_required_javascript() {
        global $PAGE;

        $PAGE->requires->jquery();
        $PAGE->requires->js_call_amd('block_whereis/whereis', 'init');
    }

}
