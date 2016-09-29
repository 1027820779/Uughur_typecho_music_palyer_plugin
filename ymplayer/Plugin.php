<?php
if (!defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

/**
 * Easy to insert YMPlayer into your article
 *
 * @package ymplayer
 * @author kokororin
 * @version 0.7
 * @link https://kotori.love/
 * @fe kirainmoe
 * @fe-github https://github.com/kirainmoe/ymplayer
 * @fe-homepage https://www.imim.pw/
 * @fe-license GPL v2
 */
class ymplayer_Plugin implements Typecho_Plugin_Interface
{

    const PLUGIN_VERSION = '0.7';

    public static function activate()
    {
        if (substr(trim(dirname(__FILE__), '/'), -8) != 'ymplayer') {
            throw new Typecho_Plugin_Exception('插件目录名必须为ymplayer');
        }
        Typecho_Plugin::factory('Widget_Abstract_Contents')->contentEx = array(__CLASS__, 'parse');
        Typecho_Plugin::factory('Widget_Abstract_Contents')->excerptEx = array(__CLASS__, 'parse');
        Typecho_Plugin::factory('Widget_Archive')->header = array(__CLASS__, 'insertStyle');
        Typecho_Plugin::factory('Widget_Archive')->footer = array(__CLASS__, 'insertScript');
        Typecho_Plugin::factory('admin/write-post.php')->bottom = array(__CLASS__, 'button');
        Typecho_Plugin::factory('admin/write-page.php')->bottom = array(__CLASS__, 'button');

        return '启用成功，请根据需要设置插件_ (:з」∠) _';
    }

    public static function deactivate()
    {}

    public static function config(Typecho_Widget_Helper_Form $form)
    {
        $element = new Typecho_Widget_Helper_Form_Element_Textarea(
            'custom', null, '', 'ئۇسلۇپ بەلگىلەڭ', 'بىۋاستا css كودنى يىزىڭ،  &lt;style&gt; يىزىش ھاجەتسىز
');
        $form->addInput($element);
    }

    public static function personalConfig(Typecho_Widget_Helper_Form $form)
    {}

    public static function insertStyle()
    {
        $custom = Typecho_Widget::widget('Widget_Options')->Plugin('ymplayer')->custom;
        if ($custom != '') {
            echo "<style id=\"ymplayer_custom_style\">\n" . $custom . "\n</style>";
        }
    }

    public static function insertScript()
    {
        echo "\n<script src=\"" . Helper::options()->pluginUrl . "/ymplayer/submodules/ymplayer/dist/assets/ymplayer.js?v=" . self::getPlayerVer() . "\"></script>";
        echo "\n<script src=\"" . Helper::options()->pluginUrl . "/ymplayer/static/plugin.js?v=" . self::PLUGIN_VERSION . "\"></script>";
    }

    public static function parse($text, $widget, $lastResult)
    {
        $text = empty($lastResult) ? $text : $lastResult;

        if ($widget instanceof Widget_Abstract_Contents) {
            $text = preg_replace_callback('/{(YMPlayer)}(.*?){\/YMPlayer}/i', function ($matches) {
                $data = $matches[2];
                $html = '<div id="ymplayer-placeholder" data-opt="' . htmlspecialchars($data) . '"></div>';
                return $html;
            }, $text);
        }
        return $text;
    }

    public static function button()
    {
        ?>
        <div id="ymplayer-dialog-bg" class="wmd-prompt-background" style="display: none;position: absolute; top: 0px; z-index: 1000; opacity: 0.5; height: 1269px; left: 0px; width: 100%;"></div>
        <div id="ymplayer-dialog" style="display: none;margin-top: -200px;" class="wmd-prompt-dialog">
          <div>
            <p><b>YMPlayer قۇيغۇچىدىن پايدىلانغان</b></p>
            <p>بۇ ئادرىستىن كىرىپ كۆرۈڭ<a href="https://github.com/kirainmoe/ymplayer">https://github.com/kirainmoe/ymplayer</a></p>
            <p>ئەگەر سىز كۆپ ناخشا يوللىماقچى بولسىڭىز،بۇ مەشخۇلاتنى قايتىلاڭ</p>
          </div>
          <form id="ymplayer-dialog-form">
            <input type="text" required data-name="title" placeholder="ئسمى" />
            <input type="text" required data-name="artist" placeholder="ئورۇنلىغۇچىسى" />
            <input type="text" required data-name="cover" placeholder="مۇقاۋا رەسىم" />
            <input type="text" required data-name="src" placeholder="ئادىرسى" />
            <input type="text" data-name="lyric" placeholder="تىكىستى" />
            <input type="text" data-name="translation" placeholder="تەرجىمىسى" />
            <button data-action="submit" type="button" class="btn btn-s primary">جەزىملەشتۈرۈش</button>
            <button data-action="cancel" type="button" class="btn btn-s">ئەمەلدىن قالدۇرۇش</button>
          </form>
        </div>
        <style>#wmd-music-button span{background:url(<?php echo file_get_contents(__DIR__ . '/static/icon.base64'); ?>);font-size:large;text-align:center;color: #999999;font-family:serif;}</style>
        <script type="text/javascript" src="<?php echo Helper::options()->pluginUrl . '/ymplayer/static/admin.js'; ?>"></script>

<?php
}

    private static function getPlayerVer()
    {
        $package = file_get_contents(__DIR__ . '/submodules/ymplayer/package.json');
        $package = json_decode($package);
        return $package->version;
    }

}
