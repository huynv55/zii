<?php
class View {
    const VIEW_PATH = '/../../resources/views';
    const VIEW_CACHED = '/../../resources/cache/';
    const VIEW_BLOCK_PATH = '/../../resources/views/blocks';
    const JS = 'javascript_src';
    const CSS = 'css_src';

    const LIB_JS = 'javascript_lib_src';
    const LIB_CSS = 'css_lib_src';

    public static $template_engin = null;

    public static function render($view, $data)
    {
        //Log::debug($data);
        $html = self::$template_engin->render($view, $data);
        return self::minify_html($html);
    }

    public static function checkFileViewExist($f)
    {
        return is_file(__DIR__.self::VIEW_PATH.$f);
    }

    public static function requireBlock()
    {
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
    static function compilePHPTag($code)
    {
        return preg_replace('~\{%\s*(.+?)\s*\%}~is', "<?php $1 ?>", $code);
    }

    /**
     * [compileEchos tag {{ <php_variable> }} ]
     * @param  [type] $code [description]
     * @return [type]       [description]
     */
    static function compileEchos($code)
    {
        return preg_replace('~\{{\s*(.+?)\s*\}}~is', "<?php if(isset($1)) echo $1; ?>", $code);
    }

    /**
     * [compileHtmlEchos tag {@ <php_variable_html_value> @} ]
     * @param  [type] $code [description]
     * @return [type]       [description]
     */
    static function compileHtmlEchos($code)
    {
        return preg_replace('~\{@\s*(.+?)\s*\@}~is', '<?php if(isset($1)) echo htmlentities($1); ?>', $code);
    }

    public static function minify_html($html)
    {
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
View::$template_engin = new \League\Plates\Engine(realpath(__DIR__.View::VIEW_PATH), 'phtml');
// load view block file
View::requireBlock();
?>