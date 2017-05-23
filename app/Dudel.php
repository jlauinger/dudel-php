<?php

namespace Dudel;


class Dudel {

    /** @var \Base */
    private $f3;


    public function run() {
        $this->f3 = \Base::instance();

        session_start();

        if(!$_SESSION["csrf"]) $_SESSION["csrf"] = $this->guid();

        if ((float)PCRE_VERSION<7.9)
            trigger_error('PCRE version is out of date');

        $this->f3->config('../app.ini');
        $this->f3->config('../.htconfig.ini');
        $this->f3->set('DB', new \DB\SQL($this->f3->get('db.dsn'),
            $this->f3->get('db.user'),
            $this->f3->get('db.password')
        ));

        \Template::instance()->extend('json',function($node){
            $attr = $node['@attrib'];
            $data = \Template::instance()->token($attr['from']);
            return '<?php echo json_encode('.$data.'); ?>';
        });

        $this->f3->run();
    }

    public function base_url() {
        return $this->f3->get('SCHEME').'://'.$this->f3->get('HOST');
    }

    function sql_now() {
        return date('Y-m-d H:i:s');
    }

    function guid() {
        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }

    function dbm($model) {
        return new \DB\SQL\Mapper($this->f3->get('DB'), $model);
    }

    function render_json($json) {
        header("Content-Type: application/json", true);
        echo json_encode($json);
    }

    function render_layout($content) {
        F3::set('content', $content);
        echo \Template::instance()->render('layout.html');
    }
    private function render_errmes($message) {
        F3::set('ERROR.status', 'Oops, something went wrong!');
        F3::set('ERROR.text', $message);
        $this->render_framework_errmes();
    }
    function render_framework_errmes() {
        F3::set('content', 'errmes.htm');
        echo \Template::instance()->render('layout.html');
    }

}