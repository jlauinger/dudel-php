<?php

namespace Dudel\Controllers;


class FrontpageCtrl {

    public static function index(\Base $f3) {
        $f3->set("shortlink", substr(md5(rand()), 0, 8));
        $f3->set("content", "frontpage.html");
        echo \Template::instance()->render('layout.html');
    }
}