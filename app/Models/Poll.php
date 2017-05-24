<?php

namespace Dudel\Models;


use function PHPSTORM_META\map;

class Poll extends \DB\SQL\Mapper {

    /** @var \Base */
    private $f3;

    function __construct(\Base $f3) {
        $this->f3 = $f3;
        parent::__construct($this->f3->get("DB"), 'polls');
    }

    public function getSlots() {
        $slots = new Slot($this->f3);
        return $slots->find(array("poll_id=?", $this->id));
    }

    public function setChoices(array $datetimes) {
        $existingSlots = Slot::fromArray($this->f3, $this->getSlots());
        // delete slots that are not any more used
        foreach($existingSlots as $slot) {
            if (!in_array($slot->toString(), $datetimes)) {
                $slot->erase();
            }
        }
        // create slots that are new
        foreach($datetimes as $time) {
            if (!in_array($time, array_map(function($x){return $x->toString();}, $existingSlots))) {
                $slot = new Slot($this->f3);
                $slot->copyfrom(array(
                    "poll_id" => $this->id,
                    "time" => $time
                ));
                $slot->save();
            }
        }
    }

    public function getDatesString() {
        return implode(",", array_unique(array_map(function($x){return $x->getDate();}, $this->getSlots())));
    }

    public function getTimesString() {
        return implode(",", array_unique(array_map(function($x){return $x->getTime();}, $this->getSlots())));
    }
}