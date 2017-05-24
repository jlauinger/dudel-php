<?php

namespace Dudel\Models;


class Slot extends \DB\SQL\Mapper {

    function __construct(\Base $f3) {
        parent::__construct($f3->get("DB"), 'slots');
    }

    public function toString() {
        return date('Y-m-d H:i', strtotime($this->time));
    }

    public function getDate() {
        return date('Y-m-d', strtotime($this->time));
    }

    public function getTime() {
        return date('H:i', strtotime($this->time));
    }

    public static function fromArray($f3, $arr) {
        $slot = new Slot($f3);
        return array_map(function($x) use($slot) {
            return $slot->findone(array("id=?", $x->id));
        }, $arr);
    }
}