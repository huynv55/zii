<?php
class View {
    const VIEW_PATH = '/../../resources/views/';
    const VIEW_CACHED = '/../../resources/cache/';
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
        $v = __DIR__.self::VIEW_PATH.$view;
        include realpath($v);
        $html = ob_get_clean();
        $code = self::compilePHPTag($html);
        $code = self::compileEchos($code);
        $code = self::compileHtmlEchos($code);
        //Log::debug($code);
        ob_start();
        eval('?>'.$code);
        $code = ob_get_clean();
        return self::minify_html($code);
    }

    public static function requireBlock() {
        $listFileBlocks = getListFileInDir(realpath(__DIR__.self::VIEW_BLOCK_PATH));
        foreach($listFileBlocks as $block) {
            require $block;
        }
    }

    /**
     * [compilePHPTag tag {% <code_php> %} ]
     * @param  [type] $code [description]
     * @return [type]       [description]
     */
    static function compilePHPTag($code) {
        return preg_replace('~\{%\s*(.+?)\s*\%}~is', "<?php $1 ?>", $code);
    }

    /**
     * [compileEchos tag {{ <php_variable> }} ]
     * @param  [type] $code [description]
     * @return [type]       [description]
     */
    static function compileEchos($code) {
        return preg_replace('~\{{\s*(.+?)\s*\}}~is', "<?php if(isset($1)) echo $1; ?>", $code);
    }

    /**
     * [compileHtmlEchos tag {@ <php_variable_html_value> @} ]
     * @param  [type] $code [description]
     * @return [type]       [description]
     */
    static function compileHtmlEchos($code) {
        return preg_replace('~\{@\s*(.+?)\s*\@}~is', '<?php if(isset($1)) echo htmlentities($1); ?>', $code);
    }

    public static function minify_html($html) {
        //return $html;
        $search = array(
            '/(\n|^)(\x20+|\t)/',
            '/(\n|^)\/\/(.*?)(\n|$)/',
            '/\n/',
            '/\<\!--.*?-->/',
            '/(\x20+|\t)/', # Delete multispace (Without \n)
            '/\>\s+\</', # strip whitespaces between tags
            '/(\"|\')\s+\>/', # strip whitespaces between quotation ("') and end tags
            '/=\s+(\"|\')/'); # strip whitespaces between = "'
            
        $replace = array("\n","\n"," ",""," ","><","$1>","=$1");
        $html = preg_replace($search,$replace,$html);
        return $html;
    }
}

// load view block file
View::requireBlock();
?>