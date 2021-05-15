<?php
class AppLoader {
    public static function load($file) {
        require_once realpath($file);
    }

    public static function controller($controller_file) {
        $controller_path = APP_PATH.'/controllers/'.$controller_file;
        self::load($controller_path);
    }

    public static function model($model_file) {
        $model_path = APP_PATH.'/models/'.$model_file;
        self::load($model_path);
    }
}
?>