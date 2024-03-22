<?php
declare(strict_types=1);

class block_calculator extends block_base
{
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

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->content->text = $this->make_html_block(['url_history' => $url_history]);
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $validated_vars = $this->validate_and_convert_to_float($_POST, ['a', 'b', 'c']);
            if ($validated_vars !== null) {
                $a = $validated_vars['a'];
                $b = $validated_vars['b'];
                $c = $validated_vars['c'];
                $html_data = [
                    'a' => $a,
                    'b' => $b,
                    'c' => $c,
                    'url_history' => $url_history,
                ];

                    $roots = $this->calculate($a, $b, $c);
                    if (count($roots) === 0) {
                        $result_string = "Невозможно определить корни";
                    } else {
                        $result_string = count($roots) == 1 ? "Корень: $roots[0]" : "Корень 1: $roots[0] <br> Корень 2: $roots[1]";
                        if ($this->save_data($a, $b, $c, $roots[0], $roots[1]) === false)
                            $result_string .= '<br>! Проблема записи в базу данных, возможно параметры слишком большие.';
                    }
                    $html_data['result_string'] = $result_string;
            } else
                $html_data = [
                    'a' => $_POST['a'],
                    'b' => $_POST['b'],
                    'c' => $_POST['c'],
                    'url_history' => $url_history,
                    'invalid_values_message' => 'введены неверные значения'
                ];

            $this->content->text = $this->make_html_block($html_data);

        }
        return $this->content;
    }

    public function replace_comma_to_dot($num): string
    {
        return str_replace(',', '.', $num);
    }

    public function get_invalid_values($a, $b, $c): string
    {
        $invalid_values = '';

        if (!is_numeric($a)) $invalid_values .= '"a" ';
        if (!is_numeric($b)) $invalid_values .= '"b" ';
        if (!is_numeric($c)) $invalid_values .= '"c" ';

        return $invalid_values;
    }

    public function validate_and_convert_to_float($data, $keys): ?array
    {
        $vars = [];
        foreach ($keys as $key){
            if ($data[$key] === '')
                return null;
            if (is_numeric($data[$key]))
                $vars[$key] = (float) $this->replace_comma_to_dot($data[$key]);
            else
                return null;
        }
        return $vars;
    }

    public function calculate(float $a, float $b, float $c): array
    {
        if (($a === 0 && $b === 0) || ($b === 0 && $c === 0) || ($a === 0 && $c === 0)) {
            return []; // Нет конкретных корней
        } else if ($a === 0) {
            $root = (-$c) / $b;
            return [$root, $root]; //уравнение линейное
        }
        $discriminant = $b * $b - 4 * $a * $c;
        if ($discriminant < 0) {
            return []; // корни не действительные
        } else {
            $root1 = (-$b + sqrt($discriminant)) / (2 * $a);
            $root2 = (-$b - sqrt($discriminant)) / (2 * $a);
            return [$root1, $root2];
        }
    }

    public function save_data($a, $b, $c, $root1, $root2): bool
    {
        global $DB;
        $ins = new stdClass();
        $ins->a = $a;
        $ins->b = $b;
        $ins->c = $c;
        $ins->root_1 = $root1;
        $ins->root_2 = $root2;
        try {
            $ins->id = $DB->insert_record('block_calculator', $ins);
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

    public function make_html_block(array $data): string
    {
        $html = '
                <div class="calculate-block">
                    <div class="title">Решение квадратного уравнения</div>';

        // указать неверные данные
        if (array_key_exists('invalid_values_message', $data))
            $html .= '<div>' . $data['invalid_values_message'] . '</div>';

        $html .= '<form class="fields" method="POST" action="#">';

        // подставить значения параметров
        if (array_key_exists('a', $data) || array_key_exists('b', $data) || array_key_exists('c', $data)) {
            $html .= '<div class="field"> a <input name="a" value="' . $data['a'] . '" placeholder="введите значение"></div>
                        <div class="field"> b <input name="b" value="' . $data['b'] . '" placeholder="введите значение"></div>
                        <div class="field"> c <input name="c" value="' . $data['c'] . '" placeholder="введите значение"></div>';
        } else {
            $html .= '<div class="field"> a <input name="a" placeholder="введите значение"></div>
                        <div class="field"> b <input name="b" placeholder="введите значение"></div>
                        <div class="field"> c <input name="c" placeholder="введите значение"></div>';
        }

        // вывести результат
        if (array_key_exists('result_string', $data)) {
            $html .= '<div>' . $data['result_string'] . '</div>';
        }

        $html .= '<button class="modal-calc-button" type="submit">Найти решение</button>
                    </form>
                    <a class="history" href="' . $data['url_history'] . '">История</a>
                </div>
            ';

        return $html;
    }

    public function applicable_formats()
    {
        return [
            'admin' => false,
            'site-index' => true,
            'course-view' => true,
            'mod' => false,
            'my' => true,
        ];
    }

}