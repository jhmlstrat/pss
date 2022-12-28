<?php

namespace Scoring;

class FielderStats {
    public $name;
    public $e;
    public $assist;
    public $putout;

    public function __construct($name) {
        $this->name = $name;
        $this->e = 0;
        $this->assist = 0;
        $this->putout = 0;
    }

}

?>
