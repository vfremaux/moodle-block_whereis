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
 * @package     block_user_mnet_hosts
 * @category    blocks
 * @author      Edouard Poncelet
 * @author      Valery Fremaux (valery.fremaux@gmail.com)
 * @copyright   2008 Valery Fremaux
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

function block_whereis_mnet_query($query) {
    global $DB, $CFG;

    // Get all hosts we have in the vmoodle register

    $vhosts = $DB->get_records('local_vmoodle', array('enabled' => 1));

    if (!empty($vhosts)) {

        $hostresults = array();
        $distinctusers = array();
        foreach ($vhosts as $vhost) {

            // Do not search in vhosts that are on other db servers than the current server.
            if ($CFG->dbhost != $vhost->vdbhost) {
                continue;
            }

            $sql = "
                SELECT
                    username,
                    idnumber,
                    firstname,
                    lastname,
                    lastlogin,
                    lastaccess
                FROM
                    `".$vhost->vdbname."`.{user}
                WHERE
                    lastname LIKE '".$query."' AND
                    deleted = 0
            ";

            if ($hostresults = $DB->get_records_sql($sql)) {

                foreach ($hostresults as $userrec) {
                    $vhosttpl = new StdClass;
                    $vhosttpl->hostname = $vhost->name;
                    $mnetid = $DB->get_field('mnet_hosts', 'id', ['wwwroot' => $vhost->vhostname]);
                    $vhosttpl->hosturl = new moodle_url('/auth/mnet/jump.php', array('id' => $mnetid));
                    if (!array_key_exists($userrec->idnumber, $distinctusers)) {
                        $userrec->hosts = array($vhosttpl);
                        $distinctusers[$userrec->username] = $userrec;
                    } else {
                        $distinctusers[$userrec->username]->hosts[] = $vhosttpl;
                    }
                }
            }
        }
        return array_values($distinctusers);
    }

}