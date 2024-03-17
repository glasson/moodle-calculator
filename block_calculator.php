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
 * Block definition class for the block_calculator plugin.
 *
 * @package   block_calculator
 * @copyright Year, You Name <your@email.address>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// require_once("{$CFG->libdir}/db/upgradelib.php");
class block_calculator extends block_base {

    /**
     * Initialises the block.
     *
     * @return void
     */
    public function init() 
    {
        $this->title = get_string('pluginname', 'block_calculator');
    }

    /**
     * Gets the block contents.
     *
     * @return string The block calculator.
     */
    function get_content() 
    {
        global $PAGE, $DB;  

        $PAGE->requires->js("/blocks/calculator/js/calculator.js");
        $PAGE->requires->css("/blocks/calculator/style.css");

        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass();

        $this->content->text = '
            <div>
                <button class="block-button">Расчитать</button>
            </div>
        ';
        
        return $this->content;
    }


    /**
     * Defines in which pages this block can be added.
     *
     * @return array of the pages where the block can be added.
     */
    public function applicable_formats() {
        return [
            'admin' => false,
            'site-index' => true,
            'course-view' => true,
            'mod' => false,
            'my' => true,
        ];
    }

}