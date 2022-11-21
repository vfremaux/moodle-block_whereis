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
 * @package block_whereis
 * @category blocks
 * @author Valery Fremaux (valery@club-internet.fr)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL
 */

require('../../../config.php');

$context = context_system::instance();
$PAGE->set_context($context);

$query = required_param('query', PARAM_TEXT);
$url = new moodle_url('/blocks/whereis/ajax/whereis.php', array('query' => $query));

require_login();

/**
 * This wrapper searches in central register last recent location of a user.
 *
 */

if (is_dir($CFG->dirroot.'/auth/multimnet')) {
    // Can we use multimnet register ?
    if ($auth = multimnet_get_enabled()) {

        /*
         * If we use mnet register, we try to get some results from the register.
         * The register tells us exactly where an active session of the user is running.
         */

        $results = (array)$auth->search_location($query);

        if (!empty($results)) {
            if (count($results) > 1) {
                echo get_string('toomanyresults', 'auth_multimnet');
            } else {
                $location = array_pop($results);
                if ($location->lastmovetime > time() - MINSECS * 10) {
                    $locallocation = $DB->get_record('mnet_host', array('wwwroot' => $location->wwwroot));
                    if ($locallocation) {
                        $a->fullname = fullname($location);
                        $a->jumplink = "<a href=\"javascript:jump('{$CFG->wwwroot}','{$locallocation->id}')\">{$locallocation->name}</a>";
                        echo get_string('johnishere', 'auth_multimnet', $a);
                    } else {
                        echo get_string('johnisherenoreach', 'auth_multimnet', $location->wwwroot);
                    }
                } else {
                    echo get_string('nothingfound', 'auth_multimnet', $query);
                }
            }
        }
        die;
    }
}

/*
 * Otherwise the strategy is to get info about where the user has accounts in the network.
 * To be quick (but dirty) we use direct SQL queries to remote mnet DBs
 * In that case there can be multiple positive answers if the user is present in several subnodes.
 */

if (!isset($results)) {
    include($CFG->dirroot.'/blocks/whereis/locallib.php');
    // No multimnet avalable, use direct VMoodle queries.
    // Note this may consume some time on large arrays. We'll check performance issues on tryouts.

    $template = new Stdclass;
    $template->distinctusers = block_whereis_mnet_query($query);

}

$template->strnoresults = get_string('nothingfound', 'block_whereis', $query);

echo $OUTPUT->render_from_template('block_whereis/hostresults', $template);