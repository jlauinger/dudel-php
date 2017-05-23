<?php

namespace Dudel\Controllers;


class Welcome {

    public static function index(\Base $f3) {
        $f3->set("content", "frontpage.html");
        echo \Template::instance()->render('layout.html');
    }
}