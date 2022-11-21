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
 * Javascript controller for whereis queries.
 *
 * @module     block_whereis/whereis
 * @package    block_whereis
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// jshint unused: true, undef:true
define(['jquery', 'core/config', 'core/log'], function($, config, log) {

    /**
     *
     */
    return {

        init: function(args) {
            $('#block-whereis-go').on('click', this.usersearch);

            log.debug('Block whereis AMD initialized');
        },

        usersearch: function(blockid) {


            url = config.wwwroot + '/blocks/whereis/ajax/whereis.php?query=' + $('#id_searchwhereis').val();


            $.post(url, function(data) {

                $('#block-whereis-results').html(data);

            }, 'html');

        }

    };
});