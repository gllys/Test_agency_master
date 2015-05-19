$(function() {
    window.BBS = new Object();
    BBS.ToUbb = function(str) {
        str = recursion('b', str, 'simpletag', 'b');
        str = recursion('strong', str, 'simpletag', 'b');
        str = recursion('i', str, 'simpletag', 'i');
        str = recursion('em', str, 'simpletag', 'i');
        str = recursion('u', str, 'simpletag', 'u');
        str = recursion('strike', str, 'simpletag', 's');
        str = recursion('s', str, 'simpletag', 's');
        str = recursion('sub', str, 'simpletag', 'sub');
        str = recursion('sup', str, 'simpletag', 'sup');
        str = recursion('tbody', str, 'simpletag', 'tbody');
        str = recursion('tr', str, 'simpletag', 'tr');
        str = recursion('pre', str, 'simpletag', 'pre');

        str = str.replace(/<(hr)[^>]*>/ig, '[$1]');
        str = str.replace(/<img([^>]*src[^>]*)>/ig, function($1, $2) {
            return imgtag($2);
        });
        str = str.replace(/<(p|ol|ul|li|blockquote|h1|h2|h3|h4|h5|h6)([^>]*)>/ig, function($1, $2, $3) {
            return common1tag($2, $3);
        });
        str = str.replace(/<a([^>]*)>/ig, function($1, $2, $3) {
            return atag($2, $3);
        });
        str = str.replace(/<(td|th)([^>]*)>/ig, function($1, $2, $3) {
            return common2tag($2, $3);
        });
        str = str.replace(/<table([^>]*)>/ig, function($1, $2) {
            return tabletag($2);
        });
        str = str.replace(/<div([^>]*)>/ig, function($1, $2) {
            return divtag($2);
        });
        str = str.replace(/<span([^>]*)>/ig, function($1, $2) {
            return spantag($2);
        });
        str = str.replace(/<font([^>]*)>/ig, function($1, $2) {
            return fonttag($2);
        });

        str = str.replace(/<\/(tr|tbody|pre|p|ol|ul|li|blockquote|h1|h2|h3|h4|h5|h6|a|td|th|table|div|span|font)([^>]*)>/ig, '[/$1]');
		str = str.replace(/[\r\n]+/, '');
        str = str.replace(/<br[^>]*>/ig, '[br]');
        return str;
    };

    function imgtag(content) {
        var $atrrs = ['width', 'height', 'border', 'alt', 'title', 'align'];
        var $styles = ['width', 'height', 'border'];

        var attrStr = attrs_styles($atrrs, $styles, content);
        var re = /src=(["'])([\s\S]*?)(\1)/i;
        var matches = re.exec(content);
        if (matches != null) {
            var src = matches[2];
        } else {
            return '';
        }
        return '[img=' + attrStr + "]" + src + '[/img]';
    }

    function common1tag(tag, attribute) {
        var $atrrs = ['align'];
        var $styles = ['text-align', 'background-color', 'color', 'font-size', 'font-family', 'background',
            'font-weight', 'font-style', 'text-decoration', 'vertical-align', 'text-indent', 'margin-left'];

        var attrStr = attrs_styles($atrrs, $styles, attribute);
        return '[' + tag + '=' + attrStr + ']';
    }

    function common2tag(tag, attribute, content) {
        var $atrrs = ['align', 'valign', 'width', 'height', 'colspan', 'rowspan', 'bgcolor'];
        var $styles = ['text-align', 'background-color', 'color', 'font-size', 'font-family', 'font-weight', 'font-style', 'text-decoration', 'vertical-align', 'background', 'border'];

        var attrStr = attrs_styles($atrrs, $styles, attribute);
        return '[' + tag + '=' + attrStr + "]";
    }

    function atag(attribute) {
        var $atrrs = ['href', 'target', 'name', 'class'];
        var attrStr = attrs($atrrs, attribute);
        return '[a=' + attrStr + "]";
    }

    function tabletag(attribute) {
        var $atrrs = ['border', 'cellspacing', 'cellpadding', 'width', 'height', 'align', 'bordercolor'];
        var $styles = ['padding', 'margin', 'border', 'bgcolor', 'text-align', 'background-color', 'color', 'font-size', 'font-family', 'font-weight', 'font-style', 'text-decoration', 'background', 'width', 'height', 'border-collapse'];
        var attrStr = attrs_styles($atrrs, $styles, attribute);
        return '[table=' + attrStr + "]";
    }

    function divtag(attribute, content) {
        var $atrrs = ['align'];
        var $styles = ['border', 'margin', 'padding', 'text-align', 'background-color', 'color', 'font-size', 'font-family', 'font-weight', 'background', 'font-style', 'text-decoration', 'vertical-align', 'margin-left'];
        var attrStr = attrs_styles($atrrs, $styles, attribute);
        return '[div=' + attrStr + "]";
    }

    function spantag(attribute) {
        var $styles = ['background-color', 'color', 'font-size', 'font-family', 'background', 'font-weight', 'font-style', 'text-decoration', 'vertical-align', 'line-height'];
        var attrStr = styles($styles, attribute);
        return '[span=' + attrStr + "]";
    }

    function fonttag(attribute) {
        var $atrrs = ['color', 'size', 'face'];
        var $styles = ['background-color'];
        var attrStr = attrs_styles($atrrs, $styles, attribute);
        return '[font=' + attrStr + "]";
    }

    function attrs_styles($atrrs, $styles, content) {

        var attrStr = attrs($atrrs, content);
        var styleStr = styles($styles, content);
        if (attrStr == '')
            return styleStr;
        if (styleStr != '')
            return  attrStr = attrStr + ',' + styleStr;
        return attrStr;
    }

    function attrs($attrs, content) {
        var _attrs = new Array();
        for (var i = 0, len = $attrs.length; i < len; i++) {
            var _reg = $attrs[i] + "=([\"'])([\\s\\S]*?)([\"'])";
            var re = new RegExp(_reg, 'ig');
            var matches = re.exec(content);
            if (matches != null && matches[2] != '')
                _attrs.push('a' + i + ':' + matches[2]);
        }
        return _attrs.join(',');
    }

    function styles($attrs, content) {
        var _attrs = new Array();
        for (var i = 0, len = $attrs.length; i < len; i++) {
            var _reg = $attrs[i] + "\\s?:\\s?([\\s\\S]*?)([;\"'])";
            var re = new RegExp(_reg, 'ig');
            var matches = re.exec(content);
            if (matches != null && matches[2] != '')
                _attrs.push('s' + i + ':' + matches[1]);
            if ($attrs[i] == 'background-color')
                content = content.replace('background-color', '');
        }
        return _attrs.join(',');
    }

    function recursion(tagname, text, dofunction, extraargs) {
        if (extraargs == null) {
            extraargs = '';
        }
        tagname = tagname.toLowerCase();

        var open_tag = '<' + tagname;
        var open_tag_len = open_tag.length;
        var close_tag = '</' + tagname + '>';
        var close_tag_len = close_tag.length;
        var beginsearchpos = 0;

        do {
            var textlower = text.toLowerCase();
            var tagbegin = textlower.indexOf(open_tag, beginsearchpos);
            if (tagbegin == -1) {
                break;
            }

            var strlen = text.length;

            var inquote = '';
            var found = false;
            var tagnameend = false;
            var optionend = 0;
            var t_char = '';

            for (optionend = tagbegin; optionend <= strlen; optionend++) {
                t_char = text.charAt(optionend);
                if ((t_char == '"' || t_char == "'") && inquote == '') {
                    inquote = t_char;
                } else if ((t_char == '"' || t_char == "'") && inquote == t_char) {
                    inquote = '';
                } else if (t_char == '>' && !inquote) {
                    found = true;
                    break;
                } else if ((t_char == '=' || t_char == ' ') && !tagnameend) {
                    tagnameend = optionend;
                }
            }

            if (!found) {
                break;
            }
            if (!tagnameend) {
                tagnameend = optionend;
            }

            var offset = optionend - (tagbegin + open_tag_len);
            var tagoptions = text.substr(tagbegin + open_tag_len, offset);
            var acttagname = textlower.substr(tagbegin * 1 + 1, tagnameend - tagbegin - 1);

            if (acttagname != tagname) {
                beginsearchpos = optionend;
                continue;
            }

            var tagend = textlower.indexOf(close_tag, optionend);
            if (tagend == -1) {
                break;
            }

            var nestedopenpos = textlower.indexOf(open_tag, optionend);
            while (nestedopenpos != -1 && tagend != -1) {
                if (nestedopenpos > tagend) {
                    break;
                }
                tagend = textlower.indexOf(close_tag, tagend + close_tag_len);
                nestedopenpos = textlower.indexOf(open_tag, nestedopenpos + open_tag_len);
            }

            if (tagend == -1) {
                beginsearchpos = optionend;
                continue;
            }

            var localbegin = optionend + 1;
            var localtext = eval(dofunction)(tagoptions, text.substr(localbegin, tagend - localbegin), tagname, extraargs);

            text = text.substring(0, tagbegin) + localtext + text.substring(tagend + close_tag_len);

            beginsearchpos = tagbegin + localtext.length;

        } while (tagbegin != -1);

        return text;
    }

    function simpletag(options, text, tagname, parseto) {
        if ($.trim(text) == '') {
            return '';
        }
        text = recursion(tagname, text, 'simpletag', parseto);
        return '[' + parseto + ']' + text + '[/' + parseto + ']';
    }

});