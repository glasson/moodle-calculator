<?php

class block_calculator extends block_base {
    public function init() 
    {
        $this->title = get_string('pluginname', 'block_calculator');
    }
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