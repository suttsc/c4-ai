<?php

function dbg($msg) {
    if(isset($_REQUEST['debug'])) {
        echo $msg . '<br/>';
    }
}

class ai
{
    //0,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,
    static $testboard = [[0,0,0,0,0,0],[0,0,0,0,0,0],[0,0,0,0,0,0],[0,0,0,0,0,1],[0,0,0,0,0,0],[0,0,0,0,0,0],[0,0,0,0,0,2]];

    public $gamestate;
    public $board = [];

    private $player = 0;

    private $valid_moves = [];

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
            $this->player = $this->calculate_player();
        }
        dbg('is player ' . $this->player);
    }

    private function calculate_player() {
        $p1 = 0;
        $p2 = 0;
        for ($c = 0; $c < 7; $c++) {
            for ($r = 0; $r < 6; $r++) {
                $val = (int)$this->gamestate[$c][$r];
                if($val == 1) $p1++;
                else if($val == 2) $p2++;
            }
        }
        if($p1 > $p2) return 2;
        else return 1;
    }

    private function parse_gamestate() {
        $gs = $this->gamestate;

        $gs = trim($gs, '[]');
        dbg('parsed:' .$gs);
        $cols = explode('],[', $gs);
        $result = [];
        foreach ($cols as $c => $col) {
            $rows = explode(',', $col);
            foreach ($rows as $r => $val) {
                $result[(int)$c][(int)$r] = (int)$val;
            }
        }
        dbg('result:<pre>' . print_r($result, 'r') . '</pre>');
        $this->board = $result;
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
        dbg('valid moves<pre>' . print_r($this->valid_moves, 'r') . '</pre>');
        $rand = rand(0,count($this->valid_moves)-1);
        dbg('random number:' . $rand);
        $valid_moves = array_keys($this->valid_moves);
        dbg('valid moves key<pre>' . print_r($valid_moves, 'r') . '</pre>');
        dbg('valid_moves:' . implode(',', $valid_moves));
        echo array_keys($this->valid_moves)[$rand];
    }

    private function visualise_board() {
        $return = '';
        $return .= 'Player ' . $this->player;
        $return .= '<table>';
        for ($r = 0; $r < 6; $r++) {
            $return .= '<tr>';
            for ($c = 0; $c < 7; $c++) {
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
            $this->valid_moves[2] = 2;
        } else {
            $this->full_cols();
        }
    }

    private function calculate_current_move() {
        $m = 0;
        for ($c = 0; $c < 7; $c++) {
            for ($r = 0; $r < 6; $r++) {
                $val = $this->board[$c][$r];
                if((int)$val == (int)$this->player) {
                    dbg('val:' . $val . ':' . $this->player . ':m:' . $m);
                    $m++;
                }
            }
        }
        return $m;
    }

    private function full_cols() {
        dbg('full_cols()');
        for ($c = 0; $c < 7; $c++) {
            $val = $this->board[$c][0];
            dbg('col' . $c . '=' . $val);
            if($val == 0) {
                $this->valid_moves[$c] = $c;
            }
        }
    }
}