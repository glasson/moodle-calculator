<?php
declare(strict_types=1);

class block_calculator extends block_base {
    public function init() 
        
    {
        
        $this->title = get_string('pluginname', 'block_calculator');
    }
    function get_content() 
    {
        global $PAGE, $CFG;
        $PAGE->requires->css("/blocks/calculator/styles.css");

        if ($this->content !== NULL) 
            return $this->content;
        $this->content = new stdClass();
        $url_history = $CFG->wwwroot . '/blocks/calculator/history.php';
        
        $this->content->text = '
            <div class="calculate-block">
                <div class="title">Решение квадратного уравнения</div>
                <form class="fields" method="POST" action="#">
                    <div class="field"> a  <input name="a" placeholder="введите значение"></div>
                    <div class="field"> b  <input name="b" placeholder="введите значение"></div>
                    <div class="field"> c  <input name="c" placeholder="введите значение"></div>
                    <button class="modal-calc-button" type="submit">Найти решение</button>
                </form>

                
                <a class="history" href="'.$url_history.'">История</a>
            </div>
        ';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
            $a = $this->replace_comma_to_dot($_POST['a']);
            $b = $this->replace_comma_to_dot($_POST['b']);
            $c = $this->replace_comma_to_dot($_POST['c']);
            if ($a !== '' && $b !== '' && $c !== ''){
                if ($this->validate_or_set_error_html($a, $b, $c, $url_history)) {
                    $roots = $this->calculate($a, $b, $c);
                    if ($roots){
                        $result_string = "Корень 1: $roots[0]<br>
                                          Корень 2: $roots[1]";
                        if ($this->save_data($a, $b, $c, $roots[0], $roots[1]) === -1)
                            $result_string .= '<br>! Проблема записи в базу данных, возможно параметры слишком большие.';
                    }
                    else
                        $result_string = "Действительных корней нет";
                    
                    $this->content->text = '
                        <div class="calculate-block">
                            <div class="title">Решение квадратного уравнения</div>
                            <form class="fields" method="POST" action="#">
                            <div class="field"> a  <input name="a" value="'.$a.'" placeholder="введите значение"></div>
                            <div class="field"> b  <input name="b" value="'.$b.'" placeholder="введите значение"></div>
                            <div class="field"> c  <input name="c" value="'.$c.'" placeholder="введите значение"></div>
                            <div>'.$result_string.'</div>
                                <button class="modal-calc-button" type="submit">Найти решение</button>
                            </form>

                            
                            <a class="history" href="'.$url_history.'">История</a>
                        </div>
                    ';
                    
                }
            }
            else {

            }
        }
            
        
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
    
    public function validate_or_set_error_html($a, $b, $c, $url_history) {
        $message = 'invalide input for: ';
        $invalide_values = '';

        if (!is_numeric($a)) $invalide_values .= '"a" ';
        if (!is_numeric($b)) $invalide_values .= '"b" ';
        if (!is_numeric($c)) $invalide_values .= '"c" ';
        
        if ($invalide_values){
            $this->content->text = '
                <div class="calculate-block">
                    <div class="title">Решение квадратного уравнения</div>
                    <div>'.$message.$invalide_values.'</div>
                    <form class="fields" method="POST" action="#">
                        <div class="field"> a  <input name="a" value="'.$a.'" placeholder="введите значение"></div>
                        <div class="field"> b  <input name="b" value="'.$b.'" placeholder="введите значение"></div>
                        <div class="field"> c  <input name="c" value="'.$c.'" placeholder="введите значение"></div>
                        <button class="modal-calc-button" type="submit">Найти решение</button>
                    </form>

                    
                    <a class="history" href="'.$url_history.'">История</a>
                </div>
            ';
            return false;
        }
        else 
            return true;
    }

    public function calculate($a, $b, $c){
        $discriminant = $b * $b - 4 * $a * $c;
        
        if ($discriminant < 0) {
            return [];
        } else {
            $root1 = (-$b + sqrt($discriminant)) / (2 * $a);
            $root2 = (-$b - sqrt($discriminant)) / (2 * $a);
            return [$root1, $root2];
        }
    }

    public function save_data($a, $b, $c, $root1, $root2){
        global $DB;
        $ins = new stdClass();
        $ins->a = $a;
        $ins->b = $b;
        $ins->c = $c;
        $ins->root_1 = $root1;
        $ins->root_2 = $root2;
        try{
            $ins->id = $DB->insert_record('block_calculator', $ins);
        } catch (Exception $e){
            return -1;
        }
    }

    public function replace_comma_to_dot($num){
        return str_replace(',', '.', $num);
    }

}