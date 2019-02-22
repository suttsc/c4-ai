<?php

function dbg($msg) {
    if(isset($_REQUEST['debug'])) {
        echo $msg . '<br/>';
    }
}

require_once('board.php');

class ai
{
    static $testboard = "[[1,2,1,2,0,0],[1,1,1,0,0,0],[0,0,0,0,0,0],[1,2,2,2,0,0],[2,0,0,0,0,0],[1,0,0,0,0,0],[2,0,0,0,0,0]]";

    public $gamestate;
    public $board = [];

    private $player = 0;
    private $enemy = 0;

    private $valid_moves = [];

    public function __construct()
    {
        $this->board = new board();
    }

    private function get_gamestate() {
        if(!empty($_REQUEST['gamestate'])) {
            dbg('gamestate request set as ' . $_REQUEST['gamestate']);
            $this->gamestate = $_REQUEST['gamestate'];
        } else {
            dbg('gamestate not set');
            $this->gamestate = static::$testboard;
        }
        if(!empty($_REQUEST['player'])) {
            $this->player = $_REQUEST['player'];
        } else {
            $this->calculate_player();
        }
        dbg('is player ' . $this->player);
    }

    private function calculate_player() {
        $p1 = 0;
        $p2 = 0;
        for ($c = 0; $c < $this->board->cols(); $c++) {
            for ($r = 0; $r < $this->board->rows(); $r++) {
                $val = (int)$this->board->get()[$c][$r];
                if($val == 1) $p1++;
                else if($val == 2) $p2++;
            }
        }
        if($p1 > $p2) {
            $this->player = 2;
            $this->enemy = 1;
        } else {
            $this->player = 1;
            $this->enemy = 2;
        }
    }

    private function create_board() {
        $this->board->set($this->gamestate);
    }

    private function set_gamestate() {
        $this->get_gamestate();
        $this->create_board();
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
        dbg('valid moves<pre>' . print_r($this->valid_moves, 'r') . '</pre>');
        $high = 0;
        $final_moves = [];
        foreach ($this->valid_moves as $c => $h) {
            if($h > $high) {
                $high = $h;
                $final_moves = [$c];
            } else if($h == $high) {
                $final_moves[] = $c;
            }
        }

        $rand = rand(0,count($final_moves)-1);
        dbg('random number:' . $rand);
        //$final_moves = array_keys($final_moves);
        dbg('valid moves key<pre>' . print_r($final_moves, 'r') . '</pre>');
        dbg('valid_moves:' . implode(',', $final_moves));
        echo $final_moves[$rand];
    }

    private function visualise_board() {
        $return = '';
        $return .= 'Player ' . $this->player;
        $return .= '<table>';
        for ($r = 0; $r < $this->board->rows(); $r++) {
            $return .= '<tr>';
            for ($c = 0; $c < $this->board->cols(); $c++) {
                $return .= '<td>' . $this->board[$c][$r] . '</td>';
            }
            $return .= '</tr>';
        }
        $return .= '</table>';
        echo $return;
    }

    private function calculate_move() {
        $current_move = $this->calculate_current_move();
        dbg('cm:' . $current_move);
        if($current_move == 0) {
            $this->valid_moves[3] = 3;
        } else if($current_move == 1) {
            $this->valid_moves[3] = 3;
        } else {
            $this->set_valid_moves();
            $this->check_col();
        }
    }

    private function calculate_current_move() {
        $m = 0;
        for ($c = 0; $c < $this->board->cols(); $c++) {
            for ($r = 0; $r < $this->board->rows(); $r++) {
                $val = $this->board->get()[$c][$r];
                if((int)$val == (int)$this->player) {
                    dbg('val:' . $val . ':' . $this->player . ':m:' . $m);
                    $m++;
                }
            }
        }
        return $m;
    }

    private function set_valid_moves() {
        dbg('full_cols()');
        for ($c = 0; $c < $this->board->cols(); $c++) {
            $val = $this->board->get()[$c][$this->board->rows()-1];
            dbg('col' . $c . '=' . $val);
            if($val == 0) {
                $this->valid_moves[$c] = 1;
            }
        }
    }

    private function check_col() {
        $moves = $this->board->check_cols($this->player);
        if(!empty($moves)) {
            foreach ($moves as $c => $goodness) {
                $this->valid_moves[$c] += $goodness;
            }
        }
        $moves = $this->board->check_cols($this->enemy);
        if(!empty($moves)) {
            foreach ($moves as $c => $badness) {
                $this->valid_moves[$c] += $badness;
            }
        }
        /*foreach ($this->valid_moves as $c) {
            $col = $this->board->get()[$c];
            $lowest = 999999999;
            for($r = 0; $r < count($col); $r++) {
                if($col[$r] != 0) {
                    $lowest = $r;
                    break;
                }
            }
            //check position against rows/cols/diags for match
        }*/
    }
}