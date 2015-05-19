<?php
class Security{
    /**
     * XSS
     *
     * @param $str
     *
     * @return mixed
     */
    public static function removeXSS($str) {
        $str = str_replace('<!--  -->', '', $str);
        $str = preg_replace('~/\*[ ]+\*/~i', '', $str);
        $str = preg_replace('/\\\0{0,4}4[0-9a-f]/is', '', $str);
        $str = preg_replace('/\\\0{0,4}5[0-9a]/is', '', $str);
        $str = preg_replace('/\\\0{0,4}6[0-9a-f]/is', '', $str);
        $str = preg_replace('/\\\0{0,4}7[0-9a]/is', '', $str);
        $str = preg_replace('/&#x0{0,8}[0-9a-f]{2};/is', '', $str);
        $str = preg_replace('/&#0{0,8}[0-9]{2,3};/is', '', $str);
        $str = preg_replace('/&#0{0,8}[0-9]{2,3};/is', '', $str);

        $str = htmlspecialchars($str);
        //$str = preg_replace('/&lt;/i', '<', $str);
        //$str = preg_replace('/&gt;/i', '>', $str);
        // 非成对标签
        $lone_tags = array("img", "param", "br", "hr");
        foreach ($lone_tags as $key => $val) {
            $val = preg_quote($val);
            $str = preg_replace('/&lt;' . $val . '(.*)(\/?)&gt;/isU', '<' . $val . "\\1\\2>", $str);
            $str = self::transCase($str);
            $str = preg_replace_callback('/<' . $val . '(.+?)>/i', create_function('$temp', 'return str_replace("&quot;","\"",$temp[0]);'), $str);
        }
        $str = preg_replace('/&amp;/i', '&', $str);

        // 成对标签
        $double_tags = array("table", "tr", "td", "font", "a", "object", "embed", "p", "strong", "em", "u", "ol", "ul", "li", "div", "tbody", "span", "blockquote", "pre", "b", "font");
        foreach ($double_tags as $key => $val) {
            $val = preg_quote($val);
            $str = preg_replace('/&lt;' . $val . '(.*)&gt;/isU', '<' . $val . "\\1>", $str);
            $str = self::transCase($str);
            $str = preg_replace_callback('/<' . $val . '(.+?)>/i', create_function('$temp', 'return str_replace("&quot;","\"",$temp[0]);'), $str);
            $str = preg_replace('/&lt;\/' . $val . '&gt;/is', '</' . $val . ">", $str);
        }
        // 清理js
        $tags = Array(
            'javascript',
            'vbscript',
            'expression',
            'applet',
            'meta',
            'xml',
            'behaviour',
            'blink',
            'link',
            'style',
            'script',
            'embed',
            'object',
            'iframe',
            'frame',
            'frameset',
            'ilayer',
            'layer',
            'bgsound',
            'title',
            'base',
            'font'
        );

        foreach ($tags as $tag) {
            $tag = preg_quote($tag);
            $str = preg_replace('/' . $tag . '\(.*\)/isU', '\\1', $str);
            $str = preg_replace('/' . $tag . '\s*:/isU', $tag . '\:', $str);
        }

        $str = preg_replace('/[\s]+on[\w]+[\s]*=/is', '', $str);

        Return $str;
    }

    public static function replaceAccentedChars($str) {
        $patterns = array(/* Lowercase */
            '/[\x{0105}\x{00E0}\x{00E1}\x{00E2}\x{00E3}\x{00E4}\x{00E5}]/u',
            '/[\x{00E7}\x{010D}\x{0107}]/u',
            '/[\x{010F}]/u',
            '/[\x{00E8}\x{00E9}\x{00EA}\x{00EB}\x{011B}\x{0119}]/u',
            '/[\x{00EC}\x{00ED}\x{00EE}\x{00EF}]/u',
            '/[\x{0142}\x{013E}\x{013A}]/u',
            '/[\x{00F1}\x{0148}]/u',
            '/[\x{00F2}\x{00F3}\x{00F4}\x{00F5}\x{00F6}\x{00F8}]/u',
            '/[\x{0159}\x{0155}]/u',
            '/[\x{015B}\x{0161}]/u',
            '/[\x{00DF}]/u',
            '/[\x{0165}]/u',
            '/[\x{00F9}\x{00FA}\x{00FB}\x{00FC}\x{016F}]/u',
            '/[\x{00FD}\x{00FF}]/u',
            '/[\x{017C}\x{017A}\x{017E}]/u',
            '/[\x{00E6}]/u',
            '/[\x{0153}]/u',
            /* Uppercase */
            '/[\x{0104}\x{00C0}\x{00C1}\x{00C2}\x{00C3}\x{00C4}\x{00C5}]/u',
            '/[\x{00C7}\x{010C}\x{0106}]/u',
            '/[\x{010E}]/u',
            '/[\x{00C8}\x{00C9}\x{00CA}\x{00CB}\x{011A}\x{0118}]/u',
            '/[\x{0141}\x{013D}\x{0139}]/u',
            '/[\x{00D1}\x{0147}]/u',
            '/[\x{00D3}]/u',
            '/[\x{0158}\x{0154}]/u',
            '/[\x{015A}\x{0160}]/u',
            '/[\x{0164}]/u',
            '/[\x{00D9}\x{00DA}\x{00DB}\x{00DC}\x{016E}]/u',
            '/[\x{017B}\x{0179}\x{017D}]/u',
            '/[\x{00C6}]/u',
            '/[\x{0152}]/u'
        );

        $replacements = array(
            'a',
            'c',
            'd',
            'e',
            'i',
            'l',
            'n',
            'o',
            'r',
            's',
            'ss',
            't',
            'u',
            'y',
            'z',
            'ae',
            'oe',
            'A',
            'C',
            'D',
            'E',
            'L',
            'N',
            'O',
            'R',
            'S',
            'T',
            'U',
            'Z',
            'AE',
            'OE'
        );

        return preg_replace($patterns, $replacements, $str);
    }
}