<?php

class View {
    const VIEW_PATH = '/../../resources/views/';

    public static function render($view, $data) {
        if (is_array($data) && !empty($data)) {
			extract($data);
		}
		ob_start();
		include realpath(__DIR__.self::VIEW_PATH.$view);
		return ob_get_clean();
    }
}
?>