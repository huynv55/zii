<?php
class View {
    const VIEW_PATH = '/../../resources/views/';
    const VIEW_BLOCK_PATH = '/../../resources/views/blocks';
    const JS = 'javascript_src';
    const CSS = 'css_src';

    const LIB_JS = 'javascript_lib_src';
    const LIB_CSS = 'css_lib_src';

    public static function render($view, $data) {
        if (is_array($data) && !empty($data)) {
            extract($data);
        }
        ob_start();
        include realpath(__DIR__.self::VIEW_PATH.$view);
        return ob_get_clean();
    }

    public static function requireBlock() {
        $listFileBlocks = getListFileInDir(realpath(__DIR__.self::VIEW_BLOCK_PATH));
        foreach($listFileBlocks as $block) {
            require $block;
        }
    }
}

// load view block file
View::requireBlock();
?>