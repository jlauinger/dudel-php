<?php

namespace Dudel\Controllers;

use \Dudel\Models\Poll;


class PollCtrl {

    public static function create(\Base $f3) {
        $poll = new Poll($f3);
        $poll->copyfrom('POST');
        $poll->save();
        $f3->reroute("/$poll->shortlink/choices/1");
    }

    public static function show(\Base $f3, $params) {
        $polls = new Poll($f3);
        $poll = $polls->findone(array('shortlink=?', $params["shortlink"]));
        $f3->set("poll", $poll);
        $f3->set("nav", "show");
        $f3->set("pollframe", "poll/show.html");
        $f3->set("content", "poll/frame.html");
        echo \Template::instance()->render('layout.html');
    }

    public static function edit(\Base $f3, $params) {
        $polls = new Poll($f3);
        $poll = $polls->findone(array('shortlink=?', $params["shortlink"]));
        $f3->set("poll", $poll);
        $f3->set("nav", "edit");
        $f3->set("pollframe", "poll/edit.html");
        $f3->set("content", "poll/frame.html");
        echo \Template::instance()->render('layout.html');
    }

    public static function choices(\Base $f3, $params) {
        $polls = new Poll($f3);
        $poll = $polls->findone(array('shortlink=?', $params["shortlink"]));
        $f3->reroute("/$poll->shortlink/choices/1");
    }

    public static function choicesStep1(\Base $f3, $params) {
        $polls = new Poll($f3);
        $poll = $polls->findone(array('shortlink=?', $params["shortlink"]));
        $f3->set("poll", $poll);
        $f3->set("nav", "choices");
        $f3->set("pollframe", "poll/choices-1.html");
        $f3->set("content", "poll/frame.html");
        $f3->set("dates", $poll->getDatesString());
        echo \Template::instance()->render('layout.html');
    }

    public static function choicesStep2(\Base $f3, $params) {
        $polls = new Poll($f3);
        $poll = $polls->findone(array('shortlink=?', $params["shortlink"]));
        $f3->set("poll", $poll);
        $f3->set("nav", "choices");
        $f3->set("pollframe", "poll/choices-2.html");
        $f3->set("content", "poll/frame.html");
        $f3->set("suggestedTimes", explode(",", $f3->get("timeslots.offer")));
        $f3->set("dates", $f3->get('SESSION.dates'));
        $f3->set("times", $poll->getTimesString());
        echo \Template::instance()->render('layout.html');
    }

    public static function choicesStep3(\Base $f3, $params) {
        $polls = new Poll($f3);
        $poll = $polls->findone(array('shortlink=?', $params["shortlink"]));
        $f3->set("poll", $poll);
        $f3->set("nav", "choices");
        $f3->set("pollframe", "poll/choices-3.html");
        $f3->set("content", "poll/frame.html");
        $f3->set("dates", explode(",", $f3->get('SESSION.dates')));
        $f3->set("times", explode(",", $f3->get('SESSION.times')));
        echo \Template::instance()->render('layout.html');
    }

    public static function update(\Base $f3, $params) {
        $polls = new Poll($f3);
        $poll = $polls->findone(array('shortlink=?', $params["shortlink"]));
        if ($_POST["step"] == "1") {
            $f3->set("SESSION.dates", $_POST["dates"]);
            $f3->reroute("/$poll->shortlink/choices/2");
        } else if ($_POST["step"] == "2") {
            $f3->set("SESSION.dates", $_POST["dates"]);
            $f3->set("SESSION.times", $_POST["times"]);
            $f3->reroute("/$poll->shortlink/choices/3");
        } else if ($_POST["step"] == "3") {
            $datetimes = $f3->get("POST.datetimes");
            $poll->setChoices($datetimes);
            $f3->reroute("/$poll->shortlink");
        } else {
            $poll->copyfrom('POST');
            $poll->save();
            $f3->reroute("/$poll->shortlink");
        }
    }
}