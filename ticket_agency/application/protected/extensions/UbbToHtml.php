<?php

require_once('ubb/cache_bbcodes.php');

//ubb函数
class UbbToHtml {

        static public function Entry($message, $time) {
                $message = self::emoticoncode($message);
                if ($time > 1367802815)
                        return self::uuzucode($message);
                else
                        return self::discuzcode($message, false, false);
        }

        static public function attachtag($pid, $aid, &$postlist) {
                return attachinpost($postlist[$pid]['attachments'][$aid]);
        }

        static public function censor($message) {
                global $_DCACHE, $posts;
                require_once('ubb/cache_censor.php');
                if ($_DCACHE['censor']['banned']) {
                        $bbcodes = 'b|i|u|color|size|font|align|list|indent|url|email|hide|quote|code|free|table|tr|td|img|swf|attach|payto|float' . ($_DCACHE['bbcodes_display'] ? '|' . implode('|', array_keys($_DCACHE['bbcodes_display'])) : '');
                        if (preg_match($_DCACHE['censor']['banned'], @preg_replace(array("/\[($bbcodes)=?.*\]/iU", "/\[\/($bbcodes)\]/i"), '', $message))) {
                                showmessage('word_banned');
                        }
                }
                return empty($_DCACHE['censor']['filter']) ? $message :
                        @preg_replace($_DCACHE['censor']['filter']['find'], $_DCACHE['censor']['filter']['replace'], $message);
        }

        static public function censormod($message) {
                global $_DCACHE, $posts;
                require_once('ubb/cache_censor.php');
                if ($_DCACHE['censor']['mod']) {
                        if (preg_match($_DCACHE['censor']['mod'], $message)) {
                                return TRUE;
                        }
                }
                return FALSE;
        }

        static public function creditshide($creditsrequire, $message, $pid) {
                global $hideattach;

                if ($GLOBALS['credits'] >= $creditsrequire || $GLOBALS['forum']['ismoderator']) {
                        return tpl_hide_credits($creditsrequire, str_replace('\\"', '"', $message));
                } else {
                        return tpl_hide_credits_hidden($creditsrequire);
                }
        }

        static public function codedisp($code) {
                global $discuzcodes;
                $discuzcodes['pcodecount']++;
                $code = self::dhtmlspecialchars(str_replace('\\"', '"', preg_replace("/^[\n\r]*(.+?)[\n\r]*$/is", "\\1", $code)));
                $code = str_replace("\n", "<li>", $code);
                $discuzcodes['codehtml'][$discuzcodes['pcodecount']] = tpl_codedisp($discuzcodes, $code);
                $discuzcodes['codecount']++;
                return "[\tDISCUZ_CODE_$discuzcodes[pcodecount]\t]";
        }

        static public function karmaimg($rate, $ratetimes) {
                $karmaimg = '';
                if ($rate && $ratetimes) {
                        $image = $rate > 0 ? 'agree.gif' : 'disagree.gif';
                        for ($i = 0; $i < ceil(abs($rate) / $ratetimes); $i++) {
                                $karmaimg .= '<img src="' . IMGDIR . '/' . $image . '" border="0" alt="" />';
                        }
                }
                return $karmaimg;
        }

        static public function discuzcode($message, $smileyoff, $bbcodeoff, $htmlon = 1, $allowsmilies = 1, $allowbbcode = 1, $allowimgcode = 1, $allowhtml = 0, $jammer = 0, $parsetype = '0', $authorid = '0', $allowmediacode = '0', $pid = 0) {
                global $discuzcodes, $credits, $tid, $discuz_uid, $highlight, $maxsmilies, $db, $tablepre, $hideattach, $allowattachurl;

                if ($parsetype != 1 && !$bbcodeoff && $allowbbcode && (strpos($message, '[/code]') || strpos($message, '[/CODE]')) !== FALSE) {
                        $message = preg_replace("/\s?\[code\](.+?)\[\/code\]\s?/ies", "self::codedisp('\\1')", $message);
                }

                $msglower = strtolower($message);

                //$htmlon = $htmlon && $allowhtml ? 1 : 0;

                if (!$htmlon) {
                        $message = $jammer ? preg_replace("/\r\n|\n|\r/e", "self::jammer()", self::dhtmlspecialchars($message)) : self::dhtmlspecialchars($message);
                }

                if (!$smileyoff && $allowsmilies && !empty($GLOBALS['_DCACHE']['smilies']) && is_array($GLOBALS['_DCACHE']['smilies'])) {
                        if (!$discuzcodes['smiliesreplaced']) {
                                foreach ($GLOBALS['_DCACHE']['smilies']['replacearray'] AS $key => $smiley) {
                                        $GLOBALS['_DCACHE']['smilies']['replacearray'][$key] = '<img src="images/smilies/' . $GLOBALS['_DCACHE']['smileytypes'][$GLOBALS['_DCACHE']['smilies']['typearray'][$key]]['directory'] . '/' . $smiley . '" smilieid="' . $key . '" border="0" alt="" />';
                                }
                                $discuzcodes['smiliesreplaced'] = 1;
                        }
                        $message = preg_replace($GLOBALS['_DCACHE']['smilies']['searcharray'], $GLOBALS['_DCACHE']['smilies']['replacearray'], $message, $maxsmilies);
                }

                if ($allowattachurl && strpos($msglower, 'attach://') !== FALSE) {
                        $message = preg_replace("/attach:\/\/(\d+)\.?(\w*)/ie", "self::parseattachurl('\\1', '\\2')", $message);
                }

                if (!$bbcodeoff && $allowbbcode) {
                        if (strpos($msglower, '[/url]') !== FALSE) {
                                $message = preg_replace("/\[url(=((https?|ftp|gopher|news|telnet|rtsp|mms|callto|bctp|ed2k|thunder|synacast){1}:\/\/|www\.|mailto:)([^\s\[\"']+?))?\](.+?)\[\/url\]/ies", "self::parseurl('\\1', '\\5')", $message);
                                $message = preg_replace("/\[url=(\/post\/index\/id\/\d+?\/floor\/\d+?\/)\](\d+?)#\[\/url\]/is", '<a href="$1#to_$2" target="_blank">$2#</a>', $message); //回复帖子
                        }
                        if (strpos($msglower, '[/email]') !== FALSE) {
                                $message = preg_replace("/\[email(=([a-z0-9\-_.+]+)@([a-z0-9\-_]+[.][a-z0-9\-_.]+))?\](.+?)\[\/email\]/ies", "self::parseemail('\\1', '\\4')", $message);
                        }
                        $message = str_replace(array(
                            '[/color]', '[/size]', '[/font]', '[/align]', '[b]', '[/b]', '[s]', '[/s]', '[hr]', '[/p]',
                            '[i=s]', '[i]', '[/i]', '[u]', '[/u]', '[list]', '[list=1]', '[list=a]',
                            '[list=A]', '[*]', '[/list]', '[indent]', '[/indent]', '[/float]'
                                ), array(
                            '</font>', '</font>', '</font>', '</p>', '<strong>', '</strong>', '<strike>', '</strike>', '<hr class="solidline" />', '</p>', '<i class="pstatus">', '<i>',
                            '</i>', '<u>', '</u>', '<ul>', '<ul type="1" class="litype_1">', '<ul type="a" class="litype_2">',
                            '<ul type="A" class="litype_3">', '<li>', '</ul>', '<blockquote>', '</blockquote>', '</span>'
                                ), preg_replace(array(
                            "/\[color=([#\w]+?)\]/i",
                            "/\[size=(\d+?)\]/i",
                            "/\[size=(\d+(\.\d+)?(px|pt|in|cm|mm|pc|em|ex|%)+?)\]/i",
                            "/\[font=([^\[\<]+?)\]/i",
                            "/\[align=(left|center|right)\]/i",
                            "/\[p=(\d{1,2}), (\d{1,2}), (left|center|right)\]/i",
                            "/\[float=(left|right)\]/i"
                                        ), array(
                            "<font color=\"\\1\">",
                            "<font size=\"\\1\">",
                            "<font style=\"font-size: \\1\">",
                            "<font face=\"\\1 \">",
                            "<p align=\"\\1\">",
                            "<p style=\"line-height: \\1px; text-indent: \\2em; text-align: \\3;\">",
                            "<span style=\"float: \\1;\">"
                                        ), $message));
                        $nest = 0;
                        while (strpos($msglower, '[table') !== FALSE && strpos($msglower, '[/table]') !== FALSE) {
                                $message = preg_replace("/\[table(?:=(\d{1,4}%?)(?:,([\(\)%,#\w ]+))?)?\]\s*(.+?)\s*\[\/table\]/ies", "self::parsetable('\\1', '\\2', '\\3')", $message);
                                if (++$nest > 4)
                                        break;
                        }

                        if ($parsetype != 1) {
                                if (strpos($msglower, '[/quote]') !== FALSE) {
                                        $uccode = new Uccode();
                                        $message = preg_replace("/\s?\[quote\][\n\r]*(.+?)[\n\r]*\[\/quote\]\s?/is", $uccode->tpl_quote(), $message);
                                }
                                if (strpos($msglower, '[/free]') !== FALSE) {
                                        $message = preg_replace("/\s*\[free\][\n\r]*(.+?)[\n\r]*\[\/free\]\s*/is", tpl_free(), $message);
                                }
                        }
                        if (strpos($msglower, '[/media]') !== FALSE) {
                                $message = preg_replace("/\[media=([\w,]+)\]\s*([^\[\<\r\n]+?)\s*\[\/media\]/ies", $allowmediacode ? "self::parsemedia('\\1', '\\2')" : "self::bbcodeurl('\\2', '<a href=\"%s\" target=\"_blank\">%s</a>')", $message);
                        }
                        if ($allowmediacode && strpos($msglower, '[/audio]') !== FALSE) {
                                $message = preg_replace("/\[audio\]\s*([^\[\<\r\n]+?)\s*\[\/audio\]/ies", "self::parseaudio('\\1')", $message);
                        }
                        if ($allowmediacode && strpos($msglower, '[/flash]') !== FALSE) {
                                $message = preg_replace("/\[flash\]\s*([^\[\<\r\n]+?)\s*\[\/flash\]/is", "<script type=\"text/javascript\" reload=\"1\">document.write(AC_FL_RunContent('width', '550', 'height', '400', 'allowNetworking', 'internal', 'allowScriptAccess', 'never', 'src', '\\1', 'quality', 'high', 'bgcolor', '#ffffff', 'wmode', 'transparent', 'allowfullscreen', 'true'));</script>", $message);
                        }
                        if ($parsetype != 1 && $allowbbcode == 2 && $GLOBALS['_DCACHE']['bbcodes']) {
                                $message = preg_replace($GLOBALS['_DCACHE']['bbcodes']['searcharray'], $GLOBALS['_DCACHE']['bbcodes']['replacearray'], $message);
                        }
                        if ($parsetype != 1 && strpos($msglower, '[/hide]') !== FALSE) {
                                if (strpos($msglower, '[hide]') !== FALSE) {
                                        if ($GLOBALS['authorreplyexist'] === '') {
                                                $GLOBALS['authorreplyexist'] = !$GLOBALS['forum']['ismoderator'] ? $db->result_first("SELECT pid FROM {$tablepre}posts WHERE tid='$tid' AND " . ($discuz_uid ? "authorid='$discuz_uid'" : "authorid=0 AND useip='$GLOBALS[onlineip]'") . " LIMIT 1") : TRUE;
                                        }
                                        if ($GLOBALS['authorreplyexist']) {
                                                $message = preg_replace("/\[hide\]\s*(.+?)\s*\[\/hide\]/is", tpl_hide_reply(), $message);
                                        } else {
                                                $message = preg_replace("/\[hide\](.+?)\[\/hide\]/is", tpl_hide_reply_hidden(), $message);
                                                $message .= '<script type="text/javascript">replyreload += \',\' + ' . $pid . ';</script>';
                                        }
                                }
                                if (strpos($msglower, '[hide=') !== FALSE) {
                                        $message = preg_replace("/\[hide=(\d+)\]\s*(.+?)\s*\[\/hide\]/ies", "self::creditshide(\\1,'\\2', $pid)", $message);
                                }
                        }
                }

                if (!$bbcodeoff) {
                        if ($parsetype != 1 && strpos($msglower, '[swf]') !== FALSE) {
                                $message = preg_replace("/\[swf\]\s*([^\[\<\r\n]+?)\s*\[\/swf\]/ies", "self::bbcodeurl('\\1', ' <img src=\"images/attachicons/flash.gif\" align=\"absmiddle\" alt=\"\" /> <a href=\"%s\" target=\"_blank\">Flash: %s</a> ')", $message);
                        }
                        if (strpos($msglower, '[/img]') !== FALSE) {
                                $message = preg_replace(array(
                                    "/\[img\]\s*([^\[\<\r\n]+?)\s*\[\/img\]/ies",
                                    "/\[img=(\d{1,4})[x|\,](\d{1,4})\]\s*([^\[\<\r\n]+?)\s*\[\/img\]/ies"
                                        ), $allowimgcode ? array(
                                            "self::bbcodeurl('\\1', '<img src=\"%s\" onload=\"thumbImg(this)\" alt=\"\" />')",
                                            "self::parseimg('\\1', '\\2', '\\3')"
                                                ) : array(
                                            "self::bbcodeurl('\\1', '<a href=\"%s\" target=\"_blank\">%s</a>')",
                                            "self::bbcodeurl('\\3', '<a href=\"%s\" target=\"_blank\">%s</a>')"
                                                ), $message);
                        }
                }

                for ($i = 0; $i <= $discuzcodes['pcodecount']; $i++) {
                        $message = str_replace("[\tDISCUZ_CODE_$i\t]", $discuzcodes['codehtml'][$i], $message);
                }

                if ($highlight) {
                        $highlightarray = explode('+', $highlight);
                        $sppos = strrpos($message, chr(0) . chr(0) . chr(0));
                        if ($sppos !== FALSE) {
                                $specialextra = substr($postlist[$firstpid]['message'], $sppos + 3);
                                $message = substr($message, 0, $sppos);
                        }
                        $message = preg_replace(array("/(^|>)([^<]+)(?=<|$)/sUe", "/<highlight>(.*)<\/highlight>/siU"), array("self::highlight('\\2', \$highlightarray, '\\1')", "<strong><font color=\"#FF0000\">\\1</font></strong>"), $message);
                        if ($sppos !== FALSE) {
                                $message = $message . chr(0) . chr(0) . chr(0) . $specialextra;
                        }
                }
                //处理旧版表情
                $message = self::discuzSmilies($message);
                unset($msglower);

                return nl2br(str_replace(array("\t", '   ', '  '), array('&nbsp; &nbsp; &nbsp; &nbsp; ', '&nbsp; &nbsp;', '&nbsp;&nbsp;'), $message));
        }

        //discuz的表情
        static public function discuzSmilies($message) {
                $smilies_type = array(
                    '2' => 'onion',
                    '3' => 'yoyocici',
                    '4' => '36ji',
                    '1' => 'default'
                );
                $smilies_array = array(
                    array(1, 1, ':)', 'smile.gif'),
                    array(1, 14, ':victory:', 'victory.gif'),
                    array(1, 15, ':time:', 'time.gif'),
                    array(1, 16, ':kiss:', 'kiss.gif'),
                    array(1, 17, ':handshake', 'handshake.gif'),
                    array(1, 18, ':call:', 'call.gif'),
                    array(1, 28, ':loveliness:', 'loveliness.gif'),
                    array(1, 29, ':funk:', 'funk.gif'),
                    array(1, 175, '{:1_175:}', 'sleepy.gif'),
                    array(1, 176, '{:1_176:}', 'dizzy.gif'),
                    array(1, 177, '{:1_177:}', 'curse.gif'),
                    array(1, 13, ':hug:', 'hug.gif'),
                    array(1, 12, ':lol', 'lol.gif'),
                    array(1, 2, ':(', 'sad.gif'),
                    array(1, 3, ':D', 'biggrin.gif'),
                    array(1, 4, ':\'(', 'cry.gif'),
                    array(1, 5, ':@', 'huffy.gif'),
                    array(1, 6, ':o', 'shocked.gif'),
                    array(1, 7, ':P', 'tongue.gif'),
                    array(1, 8, ':$', 'shy.gif'),
                    array(1, 9, ';P', 'titter.gif'),
                    array(1, 10, ':L', 'sweat.gif'),
                    array(1, 11, ':Q', 'mad.gif'),
                    array(1, 178, '{:1_178:}', 'shutup.gif'),
                    array(2, 30, '洋葱头001', '12.gif'),
                    array(2, 57, '洋葱头013', '14.gif'),
                    array(2, 58, '洋葱头014', '01.gif'),
                    array(2, 59, '洋葱头015', '07.gif'),
                    array(2, 60, '洋葱头016', '29.gif'),
                    array(2, 61, '洋葱头033', '05.gif'),
                    array(2, 62, '洋葱头031', '16.gif'),
                    array(2, 63, '洋葱头030', '19.gif'),
                    array(2, 64, '洋葱头029', '20.gif'),
                    array(2, 65, '洋葱头028', '23.gif'),
                    array(2, 66, '洋葱头027', '11.gif'),
                    array(2, 67, '洋葱头026', '21.gif'),
                    array(2, 68, '洋葱头025', '25.gif'),
                    array(2, 69, '洋葱头024', '13.gif'),
                    array(2, 70, '洋葱头023', '31.gif'),
                    array(2, 56, '洋葱头011', '17.gif'),
                    array(2, 55, '洋葱头010', '04.gif'),
                    array(2, 31, '洋葱头019', '30.gif'),
                    array(2, 32, '洋葱头020', '03.gif'),
                    array(2, 33, '洋葱头021', '18.gif'),
                    array(2, 44, '洋葱头032', '02.gif'),
                    array(2, 45, '洋葱头018', '10.gif'),
                    array(2, 46, '洋葱头017', '26.gif'),
                    array(2, 47, '洋葱头002', '08.gif'),
                    array(2, 48, '洋葱头003', '24.gif'),
                    array(2, 49, '洋葱头004', '15.gif'),
                    array(2, 50, '洋葱头005', '06.gif'),
                    array(2, 51, '洋葱头006', '27.gif'),
                    array(2, 52, '洋葱头007', '28.gif'),
                    array(2, 53, '洋葱头008', '22.gif'),
                    array(2, 54, '洋葱头009', '32.gif'),
                    array(2, 71, '洋葱头022', '09.gif'),
                    array(3, 119, 'yoyo048c', '032.gif'),
                    array(3, 106, 'yoyo035c', '029.gif'),
                    array(3, 105, 'yoyo034c', '019.gif'),
                    array(3, 104, 'yoyo033c', '028.gif'),
                    array(3, 103, 'yoyo032c', '055.gif'),
                    array(3, 102, 'yoyo031c', '075.gif'),
                    array(3, 101, 'yoyo030c', '003.gif'),
                    array(3, 100, 'yoyo029c', '039.gif'),
                    array(3, 99, 'yoyo028c', '077.gif'),
                    array(3, 98, 'yoyo027c', '059.gif'),
                    array(3, 97, 'yoyo026c', '010.gif'),
                    array(3, 107, 'yoyo036c', '061.gif'),
                    array(3, 108, 'yoyo037c', '040.gif'),
                    array(3, 118, 'yoyo047c', '050.gif'),
                    array(3, 117, 'yoyo046c', '048.gif'),
                    array(3, 116, 'yoyo045c', '018.gif'),
                    array(3, 115, 'yoyo044c', '027.gif'),
                    array(3, 114, 'yoyo043c', '012.gif'),
                    array(3, 113, 'yoyo042c', '054.gif'),
                    array(3, 112, 'yoyo041c', '031.gif'),
                    array(3, 111, 'yoyo040c', '037.gif'),
                    array(3, 110, 'yoyo039c', '001.gif'),
                    array(3, 109, 'yoyo038c', '053.gif'),
                    array(3, 96, 'yoyo025c', '068.gif'),
                    array(3, 95, 'yoyo024c', '009.gif'),
                    array(3, 82, 'yoyo011c', '072.gif'),
                    array(3, 81, 'yoyo010c', '070.gif'),
                    array(3, 80, 'yoyo009c', '056.gif'),
                    array(3, 79, 'yoyo008c', '074.gif'),
                    array(3, 78, 'yoyo007c', '030.gif'),
                    array(3, 77, 'yoyo006c', '023.gif'),
                    array(3, 76, 'yoyo005c', '021.gif'),
                    array(3, 75, 'yoyo004c', '036.gif'),
                    array(3, 74, 'yoyo003c', '016.gif'),
                    array(3, 73, 'yoyo002c', '058.gif'),
                    array(3, 83, 'yoyo012c', '004.gif'),
                    array(3, 84, 'yoyo013c', '008.gif'),
                    array(3, 94, 'yoyo023c', '045.gif'),
                    array(3, 93, 'yoyo022c', '073.gif'),
                    array(3, 92, 'yoyo021c', '002.gif'),
                    array(3, 91, 'yoyo020c', '015.gif'),
                    array(3, 90, 'yoyo019c', '006.gif'),
                    array(3, 89, 'yoyo018c', '005.gif'),
                    array(3, 88, 'yoyo017c', '043.gif'),
                    array(3, 87, 'yoyo016c', '014.gif'),
                    array(3, 86, 'yoyo015c', '076.gif'),
                    array(3, 85, 'yoyo014c', '035.gif'),
                    array(3, 72, 'yoyo001c', '007.gif'),
                    array(4, 120, 'ticon1m', 'icon1.gif'),
                    array(4, 134, 'ticon8m', 'icon8.gif'),
                    array(4, 133, 'ticon3m', 'icon3.gif'),
                    array(4, 132, 'ticon14m', 'icon14.gif'),
                    array(4, 131, 'ticon6m', 'icon6.gif'),
                    array(4, 130, 'ticon15m', 'icon15.gif'),
                    array(4, 129, 'ticon5m', 'icon5.gif'),
                    array(4, 128, 'ticon7m', 'icon7.gif'),
                    array(4, 127, 'ticon4m', 'icon4.gif'),
                    array(4, 126, 'ticon16m', 'icon16.gif'),
                    array(4, 125, 'ticon9m', 'icon9.gif'),
                    array(4, 124, 'ticon2m', 'icon2.gif'),
                    array(4, 123, 'ticon10m', 'icon10.gif'),
                    array(4, 122, 'ticon11m', 'icon11.gif'),
                    array(4, 121, 'ticon13m', 'icon13.gif'),
                    array(4, 135, 'ticon12m', 'icon12.gif'),
                );
                foreach ($smilies_array as $item) {
                        $message = str_replace($item[2], '<img src="/images/smilies/' . $smilies_type[$item[0]] . '/' . $item[3] . '" border="0" smilieid="' . $item[1] . '" alt="' . $item[2] . '" />', $message);
                }
                return $message;
        }

        static public function parseurl($url, $text) {
                if (!$url && preg_match("/((https?|ftp|gopher|news|telnet|rtsp|mms|callto|bctp|ed2k|thunder|synacast){1}:\/\/|www\.)[^\[\"']+/i", trim($text), $matches)) {
                        $url = $matches[0];
                        $length = 65;
                        if (strlen($url) > $length) {
                                $text = substr($url, 0, intval($length * 0.5)) . ' ... ' . substr($url, - intval($length * 0.3));
                        }
                        return '<a href="' . (substr(strtolower($url), 0, 4) == 'www.' ? 'http://' . $url : $url) . '" target="_blank">' . $text . '</a>';
                } else {
                        $url = substr($url, 1);
                        if (substr(strtolower($url), 0, 4) == 'www.') {
                                $url = 'http://' . $url;
                        }
                        return '<a href="' . $url . '" target="_blank">' . $text . '</a>';
                }
        }

        static public function parseattachurl($aid, $ext) {
                $GLOBALS['skipaidlist'][] = $aid;
                return $GLOBALS['boardurl'] . 'attachment.php?aid=' . aidencode($aid) . ($ext ? '&request=yes&_f=.' . $ext : '');
        }

        static public function parseemail($email, $text) {
                if (!$email && preg_match("/\s*([a-z0-9\-_.+]+)@([a-z0-9\-_]+[.][a-z0-9\-_.]+)\s*/i", $text, $matches)) {
                        $email = trim($matches[0]);
                        return '<a href="mailto:' . $email . '">' . $email . '</a>';
                } else {
                        return '<a href="mailto:' . substr($email, 1) . '">' . $text . '</a>';
                }
        }

        static public function parsetable($width, $bgcolor, $message) {
                if (!preg_match("/^\[tr(?:=([\(\)%,#\w]+))?\]\s*\[td(?:=(\d{1,2}),(\d{1,2})(?:,(\d{1,4}%?))?)?\]/", $message) && !preg_match("/^<tr[^>]*?>\s*<td[^>]*?>/", $message)) {
                        return str_replace('\\"', '"', preg_replace("/\[tr(?:=([\(\)%,#\w]+))?\]|\[td(?:=(\d{1,2}),(\d{1,2})(?:,(\d{1,4}%?))?)?\]|\[\/td\]|\[\/tr\]/", '', $message));
                }
                if (substr($width, -1) == '%') {
                        $width = substr($width, 0, -1) <= 98 ? intval($width) . '%' : '98%';
                } else {
                        $width = intval($width);
                        $width = $width ? ($width <= 560 ? $width . 'px' : '98%') : '';
                }
                return '<table cellspacing="0" class="t_table" ' .
                        ($width == '' ? NULL : 'style="width:' . $width . '"') .
                        ($bgcolor ? ' bgcolor="' . $bgcolor . '">' : '>') .
                        str_replace('\\"', '"', preg_replace(array(
                            "/\[tr(?:=([\(\)%,#\w]+))?\]\s*\[td(?:=(\d{1,2}),(\d{1,2})(?:,(\d{1,4}%?))?)?\]/ie",
                            "/\[\/td\]\s*\[td(?:=(\d{1,2}),(\d{1,2})(?:,(\d{1,4}%?))?)?\]/ie",
                            "/\[\/td\]\s*\[\/tr\]\s*/i"
                                        ), array(
                            "self::parsetrtd('\\1', '\\2', '\\3', '\\4')",
                            "self::parsetrtd('td', '\\1', '\\2', '\\3')",
                            '</td></tr>'
                                        ), $message)
                        ) . '</table>';
        }

        static public function parsetrtd($bgcolor, $colspan, $rowspan, $width) {
                return ($bgcolor == 'td' ? '</td>' : '<tr' . ($bgcolor ? ' bgcolor="' . $bgcolor . '"' : '') . '>') . '<td' . ($colspan > 1 ? ' colspan="' . $colspan . '"' : '') . ($rowspan > 1 ? ' rowspan="' . $rowspan . '"' : '') . ($width ? ' width="' . $width . '"' : '') . '>';
        }

        static public function parseaudio($url, $width = 400, $autostart = 0) {
                $ext = strtolower(substr(strrchr($url, '.'), 1, 5));
                switch ($ext) {
                        case 'mp3':
                        case 'wma':
                        case 'mid':
                        case 'wav':
                                return '<object classid="clsid:6BF52A52-394A-11d3-B153-00C04F79FAA6" width="' . $width . '" height="64"><param name="invokeURLs" value="0"><param name="autostart" value="' . $autostart . '" /><param name="wmode" value="transparent"><param name="url" value="' . $url . '" /><embed src="' . $url . '" autostart="' . $autostart . '" type="application/x-mplayer2" width="' . $width . '" height="64"></embed></object>';
                        case 'ra':
                        case 'rm':
                        case 'ram':
                                $mediaid = 'media_' . random(3);
                                return '<object classid="clsid:CFCDAA03-8BE4-11CF-B84B-0020AFBBCCFA" width="' . $width . '" height="32"><param name="autostart" value="' . $autostart . '" /><param name="src" value="' . $url . '" /><param name="wmode" value="transparent"><param name="controls" value="controlpanel" /><param name="console" value="' . $mediaid . '_" /><embed src="' . $url . '" type="audio/x-pn-realaudio-plugin" controls="ControlPanel" console="' . $mediaid . '_" width="' . $width . '" height="32"></embed></object>';
                }
        }

        static public function parsemedia($params, $url) {
                $params = explode(',', $params);
                $width = intval($params[1]) > 800 ? 800 : intval($params[1]);
                $height = intval($params[2]) > 600 ? 600 : intval($params[2]);
                $autostart = !empty($params[3]) ? 1 : 0;
                if ($flv = self::parseflv($url, $width, $height)) {
                        return $flv;
                }
                if (in_array(count($params), array(3, 4))) {
                        $type = $params[0];
                        $url = str_replace(array('<', '>'), '', str_replace('\\"', '\"', $url));
                        switch ($type) {
                                case 'mp3':
                                case 'wma':
                                case 'ra':
                                case 'ram':
                                case 'wav':
                                case 'mid':
                                        return self::parseaudio($url, $width, $autostart);
                                case 'rm':
                                case 'rmvb':
                                case 'rtsp':
                                        $mediaid = 'media_' . random(3);
                                        return '<object classid="clsid:CFCDAA03-8BE4-11cf-B84B-0020AFBBCCFA" width="' . $width . '" height="' . $height . '"><param name="autostart" value="' . $autostart . '" /><param name="src" value="' . $url . '" /><param name="wmode" value="transparent"><param name="controls" value="imagewindow" /><param name="console" value="' . $mediaid . '_" /><embed src="' . $url . '" type="audio/x-pn-realaudio-plugin" controls="imagewindow" console="' . $mediaid . '_" width="' . $width . '" height="' . $height . '"></embed></object><br /><object classid="clsid:CFCDAA03-8BE4-11CF-B84B-0020AFBBCCFA" width="' . $width . '" height="32"><param name="src" value="' . $url . '" /><param name="wmode" value="transparent"><param name="controls" value="controlpanel" /><param name="console" value="' . $mediaid . '_" /><embed src="' . $url . '" type="audio/x-pn-realaudio-plugin" controls="controlpanel" console="' . $mediaid . '_" width="' . $width . '" height="32"' . ($autostart ? ' autostart="true"' : '') . '></embed></object>';
                                case 'flv':
                                        return '<script type="text/javascript" reload="1">document.write(AC_FL_RunContent(\'width\', \'' . $width . '\', \'height\', \'' . $height . '\', \'allowNetworking\', \'internal\', \'allowScriptAccess\', \'never\', \'src\', \'images/common/flvplayer.swf\', \'flashvars\', \'file=' . rawurlencode($url) . '\', \'quality\', \'high\', \'wmode\', \'transparent\', \'allowfullscreen\', \'true\'));</script>';
                                case 'swf':
                                        return '<script type="text/javascript" reload="1">document.write(AC_FL_RunContent(\'width\', \'' . $width . '\', \'height\', \'' . $height . '\', \'allowNetworking\', \'internal\', \'allowScriptAccess\', \'never\', \'src\', \'' . $url . '\', \'quality\', \'high\', \'bgcolor\', \'#ffffff\', \'wmode\', \'transparent\', \'allowfullscreen\', \'true\'));</script>';
                                case 'asf':
                                case 'asx':
                                case 'wmv':
                                case 'mms':
                                case 'avi':
                                case 'mpg':
                                case 'mpeg':
                                        return '<object classid="clsid:6BF52A52-394A-11d3-B153-00C04F79FAA6" width="' . $width . '" height="' . $height . '"><param name="invokeURLs" value="0"><param name="autostart" value="' . $autostart . '" /><param name="wmode" value="transparent"><param name="url" value="' . $url . '" /><embed src="' . $url . '" autostart="' . $autostart . '" type="application/x-mplayer2" width="' . $width . '" height="' . $height . '"></embed></object>';
                                case 'mov':
                                        return '<object classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" width="' . $width . '" height="' . $height . '"><param name="autostart" value="' . ($autostart ? '' : 'false') . '" /><param name="wmode" value="transparent"><param name="src" value="' . $url . '" /><embed src="' . $url . '" autostart="' . ($autostart ? 'true' : 'false') . '" type="video/quicktime" controller="true" width="' . $width . '" height="' . $height . '"></embed></object>';
                                default:
                                        return '<a href="' . $url . '" target="_blank">' . $url . '</a>';
                        }
                }
                return;
        }

        static public function bbcodeurl($url, $tags) {
                if (!preg_match("/<.+?>/s", $url)) {
                        if (!in_array(strtolower(substr($url, 0, 6)), array('http:/', 'https:', 'ftp://', 'rtsp:/', 'mms://'))) {
                                $url = 'http://' . $url;
                        }
                        return str_replace(array('submit', 'logging.php'), array('', ''), sprintf($tags, $url, addslashes($url)));
                } else {
                        return '&nbsp;' . $url;
                }
        }

        static public function jammer() {
                $randomstr = '';
                for ($i = 0; $i < mt_rand(5, 15); $i++) {
                        $randomstr .= chr(mt_rand(32, 59)) . ' ' . chr(mt_rand(63, 126));
                }
                $seo = !$GLOBALS['tagstatus'] ? $GLOBALS['discuzcodes']['seoarray'][mt_rand(0, 5)] : '';
                return mt_rand(0, 1) ? '<font style="font-size:0px;color:' . WRAPBG . '">' . $seo . $randomstr . '</font>' . "\r\n" :
                        "\r\n" . '<span style="display:none">' . $randomstr . $seo . '</span>';
        }

        static public function highlight($text, $words, $prepend) {
                $text = str_replace('\"', '"', $text);
                foreach ($words AS $key => $replaceword) {
                        $text = str_replace($replaceword, '<highlight>' . $replaceword . '</highlight>', $text);
                }
                return "$prepend$text";
        }

        static public function parseflv($url, $width, $height) {
                $lowerurl = strtolower($url);
                $flv = '';
                if ($lowerurl != str_replace(array('player.youku.com/player.php/sid/', 'tudou.com/v/', 'player.ku6.com/refer/'), '', $lowerurl)) {
                        $flv = $url;
                } elseif (strpos($lowerurl, 'v.youku.com/v_show/') !== FALSE) {
                        if (preg_match("/http:\/\/v.youku.com\/v_show\/id_([^\/]+)(.html|)/i", $url, $matches)) {
                                $flv = 'http://player.youku.com/player.php/sid/' . $matches[1] . '/v.swf';
                        }
                } elseif (strpos($lowerurl, 'tudou.com/programs/view/') !== FALSE) {
                        if (preg_match("/http:\/\/(www.)?tudou.com\/programs\/view\/([^\/]+)/i", $url, $matches)) {
                                $flv = 'http://www.tudou.com/v/' . $matches[2];
                        }
                } elseif (strpos($lowerurl, 'v.ku6.com/show/') !== FALSE) {
                        if (preg_match("/http:\/\/v.ku6.com\/show\/([^\/]+).html/i", $url, $matches)) {
                                $flv = 'http://player.ku6.com/refer/' . $matches[1] . '/v.swf';
                        }
                } elseif (strpos($lowerurl, 'v.ku6.com/special/show_') !== FALSE) {
                        if (preg_match("/http:\/\/v.ku6.com\/special\/show_\d+\/([^\/]+).html/i", $url, $matches)) {
                                $flv = 'http://player.ku6.com/refer/' . $matches[1] . '/v.swf';
                        }
                }
                if ($flv) {
                        return '<script type="text/javascript" reload="1">document.write(AC_FL_RunContent(\'width\', \'' . $width . '\', \'height\', \'' . $height . '\', \'allowNetworking\', \'internal\', \'allowScriptAccess\', \'never\', \'src\', \'' . $flv . '\', \'quality\', \'high\', \'bgcolor\', \'#ffffff\', \'wmode\', \'transparent\', \'allowfullscreen\', \'true\'));</script>';
                } else {
                        return FALSE;
                }
        }

        static public function parseimg($width, $height, $src) {
                return self::bbcodeurl($src, '<img' . ($width > 0 ? " width=\"$width\"" : '') . ($height > 0 ? " height=\"$height\"" : '') . " src=\"$src\" border=\"0\" alt=\"\" />");
        }

        static public function dhtmlspecialchars($string) {
                if (is_array($string)) {
                        foreach ($string as $key => $val) {
                                $string[$key] = dhtmlspecialchars($val);
                        }
                } else {
                        $string = preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4}));)/', '&\\1',
                                //$string = preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4})|[a-zA-Z][a-z0-9]{2,5});)/', '&\\1',
                                str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $string));
                }
                return $string;
        }

        static public function emoticoncode($message) {
                $message = preg_replace(array(
                    "/\[emoticon\](\d+?)\[\/emoticon\]/i",
                        ), array(
                    '<img src="' . Yii::app()->params['emoticon_url'] . '/' . Yii::app()->params['emoticon_path'] . '$1.gif" />',
                        ), $message);
                return $message;
        }

        static public function uuzucode($message) {

                //常规替换 
                $message = str_replace(array(
                    '[hr]', '[br]'
                    , '[b]', '[i]', '[em]', '[u]', '[s]', '[sub]', '[sup]', '[tbody]', '[tr]', '[pre]'
                    , '[/b]', '[/i]', '[/em]', '[/u]', '[/s]', '[/sub]', '[/sup]', '[/tbody]', '[/tr]', '[/pre]'
                    , '[/p]', '[/ol]', '[/ul]', '[/li]', '[/blockquote]', '[/h1]', '[/h2]', '[/h3]', '[/h4]', '[/h5]', '[/h6]'
                    , '[/a]', '[/td]', '[/th]', '[/table]', '[/div]', '[/span]', '[/font]'
                        ), array(
                    '<hr/>', '<br/>'
                    , '<b>', '<i>', '<em>', '<u>', '<s>', '<sub>', '<sup>', '<tbody>', '<tr>', '<pre>'
                    , '</b>', '</i>', '</em>', '</u>', '</s>', '</sub>', '</sup>', '</tbody>', '</tr>', '</pre>'
                    , '</p>', '</ol>', '</ul>', '</li>', '</blockquote>', '</h1>', '</h2>', '</h3>', '</h4>', '</h5>', '</h6>'
                    , '</a>', '</td>', '</th>', '</table>', '</div>', '</span>', '</font>'
                        ), preg_replace(
                                array(
                    "/\[img=([^\]]*)\](.*?)\[\/img\]/ies",
                    "/\[(p|ol|ul|li|blockquote|h1|h2|h3|h4|h5|h6)=([^\]]*)\]/ies",
                    "/\[a=([^\]]*)\]/ies",
                    "/\[(td|th)=([^\]]*)\]/ies",
                    "/\[table=([^\]]*)\]/ies",
                    "/\[div=([^\]]*)\]/ies",
                    "/\[span=([^\]]*)\]/ies",
                    "/\[font=([^\]]*)\]/ies",
                                ), array(
                    "self::uuzuParseImg('$1','$2')",
                    "self::uuzuCommon1('$1','$2')",
                    "self::uuzuParseA('$1')",
                    "self::uuzuCommon2('$1','$2')",
                    "self::uuzuParseTable('$1')",
                    "self::uuzuParseDiv('$1')",
                    "self::uuzuParseSpan('$1')",
                    "self::uuzuParseFont('$1')",
                                ), $message
                        )
                );


                return $message;
        }

        static public function uuzuParseImg($attribute, $url) {
                $atrrs = array('width', 'height', 'border', 'alt', 'title', 'align');
                $styles = array('width', 'height', 'border');

                $_atrrStr = self::getAttrs($attribute, $atrrs, $styles); // 得到属性
                return '<img ' . $_atrrStr . ' src="' . $url . '"/>';
        }

        static public function uuzuCommon1($tagName, $attribute) {
                $atrrs = array('align');
                $styles = array('text-align', 'background-color', 'color', 'font-size', 'font-family', 'background',
                    'font-weight', 'font-style', 'text-decoration', 'vertical-align', 'text-indent', 'margin-left');

                $_atrrStr = ' ' . self::getAttrs($attribute, $atrrs, $styles); // 得到属性
                if ($_atrrStr == ' ')
                        $_atrrStr = '';
                return '<' . $tagName . $_atrrStr . '>';
        }

        static public function uuzuCommon2($tagName, $attribute) {
                $atrrs = array('align', 'valign', 'width', 'height', 'colspan', 'rowspan', 'bgcolor');
                $styles = array('text-align', 'background-color', 'color', 'font-size', 'font-family', 'font-weight', 'font-style', 'text-decoration', 'vertical-align', 'background', 'border');

                $_atrrStr = ' ' . self::getAttrs($attribute, $atrrs, $styles); // 得到属性
                if ($_atrrStr == ' ')
                        $_atrrStr = '';
                return '<' . $tagName . $_atrrStr . '>';
        }

        static public function uuzuParseA($attribute) {
                $atrrs = array('href', 'target', 'name', 'class');
                $styles = array();

                $_atrrStr = ' ' . self::getAttrs($attribute, $atrrs, $styles); // 得到属性
                if ($_atrrStr == ' ')
                        $_atrrStr = '';
                return '<a ' . $_atrrStr . '>';
        }

        static public function uuzuParseTable($attribute) {
                $atrrs = array('border', 'cellspacing', 'cellpadding', 'width', 'height', 'align', 'bordercolor');
                $styles = array('padding', 'margin', 'border', 'bgcolor', 'text-align', 'background-color', 'color', 'font-size', 'font-family', 'font-weight', 'font-style', 'text-decoration', 'background', 'width', 'height', 'border-collapse');

                $_atrrStr = ' ' . self::getAttrs($attribute, $atrrs, $styles); // 得到属性
                if ($_atrrStr == ' ')
                        $_atrrStr = '';
                return '<table ' . $_atrrStr . '>';
        }

        static public function uuzuParseDiv($attribute) {
                $atrrs = array('align');
                $styles = array('border', 'margin', 'padding', 'text-align', 'background-color', 'color', 'font-size', 'font-family', 'font-weight', 'background', 'font-style', 'text-decoration', 'vertical-align', 'margin-left');

                $_atrrStr = ' ' . self::getAttrs($attribute, $atrrs, $styles); // 得到属性
                if ($_atrrStr == ' ')
                        $_atrrStr = '';
                return '<div ' . $_atrrStr . '>';
        }

        static public function uuzuParseSpan($attribute) {
                $atrrs = array();
                $styles = array('background-color', 'color', 'font-size', 'font-family', 'background', 'font-weight', 'font-style', 'text-decoration', 'vertical-align', 'line-height');

                $_atrrStr = ' ' . self::getAttrs($attribute, $atrrs, $styles); // 得到属性
                if ($_atrrStr == ' ')
                        $_atrrStr = '';
                return '<span ' . $_atrrStr . '>';
        }

        static public function uuzuParseFont($attribute) {
                $atrrs = array('color', 'size', 'face');
                $styles = array('background-color');

                $_atrrStr = ' ' . self::getAttrs($attribute, $atrrs, $styles); // 得到属性
                if ($_atrrStr == ' ')
                        $_atrrStr = '';
                return '<font ' . $_atrrStr . '>';
        }

        static public function getAttrs($attribute, $atrrs, $styles) {
                if (!$attribute)
                        return '';
                $_atrrs = explode(',', $attribute);
                $_atrrArr = array();
                $_styleArr = array();
                foreach ($_atrrs as $val) {
                        $_aArr = preg_split('/(?<!http)\:/', $val);
                        $_a = $_aArr[0];
                        $_index = substr($_a, 1);
                        $_v = $_aArr[1];
                        if ($_a{0} == 'a') {
                                $_atrrArr[] = $atrrs[$_index] . '="' . $_v . '"';
                        } else {
                                $_styleArr[] = $styles[$_index] . ':' . $_v;
                        }
                }

                $str = join(' ', $_atrrArr);
                if ($_styleArr)
                        $str .= 'style="' . join(';', $_styleArr) . '"';
                return $str;
        }

}

?>
