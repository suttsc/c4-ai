<?php


class ai
{
    //0,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,
    static $testboard =  [
        0, 1, 0, 0, 0, 0, 0,
        0, 0, 0, 0, 0, 0, 0,
        0, 0, 0, 0, 0, 0, 0,
        0, 0, 0, 0, 0, 0, 0,
        0, 0, 0, 0, 0, 0, 0,
        0, 0, 0, 0, 0, 0, 0,
    ];

    public $gamestate;
    public $board = [];

    private $player = 0;

    private $valid_moves = [];

    private function get_gamestate() {
        if(!empty($_REQUEST['gamestate'])) {
            if(is_array($_REQUEST['gamestate'])) {
                $this->gamestate = $_REQUEST['gamestate'];
            } else {
                $this->gamestate = static::$testboard;
            }
        } else {
            //die('cannot find game state');
        }
        if(!empty($_REQUEST['player'])) {
            $this->player = $_REQUEST['player'];
        } else {
            //die('cannot find player id');
        }
    }

    private function parse_gamestate() {
        $h = 0;
        for ($i = 0; $i < 6; $i++) {
            for ($j = 0; $j < 7; $j++) {
                $this->board[$i][$j] = $this->gamestate[$h];
                $h++;
            }
        }
    }

    private function set_gamestate() {
        $this->get_gamestate();
        $this->parse_gamestate();
    }

    public function go() {
        $this->set_gamestate();
        if(isset($_REQUEST['visualise'])) {
            $this->visualise_board();
        } else {
            $this->calculate_move();
            $this->return_move();
        }
    }

    private function return_move() {
        echo array_keys($this->valid_moves)[rand(0,count($this->valid_moves)-1)];
    }

    private function visualise_board() {
        echo 'Player ' . $this->player;
        echo '<table>';
        $board = array_reverse($this->board);
        foreach ($board as $row) {
            echo '<tr>';
            foreach ($row as $col) {
                echo '<td>' . $col . '</td>';
            }
            echo '</tr>';
        }
        echo '</table>';
    }

    private function calculate_move() {
        $this->full_cols();
    }

    private function full_cols() {
        $board = $this->board;
        $toprow = $board[count($board)-1];
        foreach ($toprow as $col => $val) {
            if($val == 0) {
                $this->valid_moves[$col] = $val;
            }
        }
    }
}