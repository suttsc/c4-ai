<?php


class board
{
    public $array = [];

    public function set($gamestate) {
        $gs = json_decode($gamestate);
        $this->array = $gs;
    }

    public function get() {
        return $this->array;
    }

    public function cols() {
        return count($this->array);
    }

    public function rows() {
        return count($this->array[0]);
    }

    public function check_cols($as_player) {
        $moves = [];
        for($c = 0; $c < $this->cols(); $c++) {
            $cnt = 0;
            $cur = 0;
            for ($r = 0; $r < $this->rows(); $r++) {
                $val = $this->get()[$c][$r];
                if($val != $cur) {
                    if($val == 0){
                        if($cnt == 3 && $cur == $as_player) $moves[$c] = 5;
                        if($cnt == 2 && $cur == $as_player) $moves[$c] = 1;
                    }
                    $cur = $val;
                    $cnt = 1;
                } else {
                    $cnt++;
                }
            }
        }
        return $moves;
    }

    public function check_rows($as_player) {
        $patterns = [0, $as_player, $as_player, $as_player];
        $patterns[] = array_reverse($patterns[0]);

        $moves = [];
        for($c = 0; $c < $this->cols(); $c++) {
            for ($r = 0; $r < $this->rows(); $r++) {

            }
        }
    }
}