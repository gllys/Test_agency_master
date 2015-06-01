(function($) {
    // 验证规则
    $.fn.validationEngineLanguage = function() {
    };
    $.validationEngineLanguage = {
        newLang: function() {
            $.validationEngineLanguage.allRules = {
                "required": {// Add your regex rules here, you can take telephone as an example
                    "regex": "none",
                    "alertText": "* 请输入{tag}",
                    "alertTextCheckboxMultiple": "* 请最少选择一个{tag}",
                    "alertTextCheckboxe": "* {tag}该选项为必选",
                    "alertTextDateRange": "* 请选择{tag}"
                },
                "dateRange": {
                    "regex": "none",
                    "alertText": "* 请输入有效的{tag}",
                    "alertText2": ""
                },
                "dateTimeRange": {
                    "regex": "none",
                    "alertText": "* 请输入有效的{tag}",
                    "alertText2": ""
                },
                "minSize": {
                    "regex": "none",
                    "alertText": "* 请输入有效的{tag},最少",
                    "alertText2": " 个字符"
                },
                "maxSize": {
                    "regex": "none",
                    "alertText": "* 请输入有效的{tag},最多",
                    "alertText2": " 个字符"
                },
                "groupRequired": {
                    "regex": "none",
                    "alertText": "* 请至少填写其中一项"
                },
                "min": {
                    "regex": "none",
                    "alertText": "* 请输入有效的{tag},最小值为"
                },
                "max": {
                    "regex": "none",
                    "alertText": "* 请输入有效的{tag},最大值为"
                },
                "past": {
                    "regex": "none",
                    "alertText": "* 请输入有效的{tag},日期需在",
                    "alertText2": " 之前"
                },
                "future": {
                    "regex": "none",
                    "alertText": "* 请输入有效的{tag},日期需在",
                    "alertText2": " 之后"
                },
                "maxCheckbox": {
                    "regex": "none",
                    "alertText": "* 最多选择 ",
                    "alertText2": " 个项目"
                },
                "minCheckbox": {
                    "regex": "none",
                    "alertText": "* 最少选择 ",
                    "alertText2": " 个项目"
                },
                "equals": {
                    "regex": "none",
                    "alertText": "* 两次输入的密码不一致"
                },
                "creditCard": {
                    "regex": "none",
                    "alertText": "* 请输入有效的信用卡号码"
                },
                "phone": {
                    // credit:jquery.h5validate.js / orefalo
                    "regex": /^([\+][0-9]{1,3}[ \.\-])?([\(]{1}[0-9]{2,6}[\)])?([0-9 \.\-\/]{3,20})((x|ext|extension)[ ]?[0-9]{1,4})?$/,
                    "alertText": "* 请输入有效的电话号码"
                },
                "fax": {
                    // create by ccq
                    "regex": /^([\+][0-9]{1,3}[ \.\-])?([\(]{1}[0-9]{2,6}[\)])?([0-9 \.\-\/]{3,20})((x|ext|extension)[ ]?[0-9]{1,4})?$/,
                    "alertText": "* 请输入有效的传真号码"
                },
                "mobile": {
                    "regex": /^(1)\d{10}$/,
                    "alertText": "* 请输入有效的手机号码"
                },
                "email": {
                    // Shamelessly lifted from Scott Gonzalez via the Bassistance Validation plugin http://projects.scottsplayground.com/email_address_validation/
                    "regex": /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i,
                    "alertText": "* 请输入有效的邮件地址"
                },
                "integer": {
                    "regex": /^[\-\+]?\d+$/,
                    "alertText": "* 请输入有效的整数"
                },
                "number": {
                    // Number, including positive, negative, and floating decimal. credit:orefalo
                    "regex": /^[\-\+]?((([0-9]{1,3})([,][0-9]{3})*)|([0-9]+))?([\.]([0-9]+))?$/,
                    "alertText": "* 请输入有效的数值"
                },
                "onlyNumberPrice": {
                    // Number, including positive, negative, and floating decimal. credit:orefalo
                    "regex": /^([1-9][\d]{0,7}|0)(\.[\d]{1,2})?$/,
                    "alertText": "* 请输入有效的金额,只有有数字和小数点组成,且最多两位小数"
                },
                "date": {
                    "regex": /^\d{4}[\/\-](0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])$/,
                    "alertText": "* 请输入有效的日期，例如2015-01-01"
                },
                "ipv4": {
                    "regex": /^((([01]?[0-9]{1,2})|(2[0-4][0-9])|(25[0-5]))[.]){3}(([0-1]?[0-9]{1,2})|(2[0-4][0-9])|(25[0-5]))$/,
                    "alertText": "* 请输入有效的 IP 地址"
                },
                "url": {
                    "regex": /[\w\-_]+(\.[\w\-_]+)+([\w\-\.,@?^=%&amp;:/~\+#]*[\w\-\@?^=%&amp;/~\+#])?/,
                    "alertText": "* 请输入有效的网址"
                },
                "onlyNumberSp": {
                    "regex": /^[0-9\ ]+$/,
                    "alertText": "* 请输入数字"
                },
                "onlyLetterSp": {
                    "regex": /^[a-zA-Z\ \']+$/,
                    "alertText": "* 请输入英文字母"
                },
                "onlyLetterNumber": {
                    "regex": /^[0-9a-zA-Z]+$/,
                    "alertText": "* 请输入数字或英文字母"
                },
                //tls warning:homegrown not fielded 
                "dateFormat": {
                    "regex": /^\d{4}[\/\-](0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])$|^(?:(?:(?:0?[13578]|1[02])(\/|-)31)|(?:(?:0?[1,3-9]|1[0-2])(\/|-)(?:29|30)))(\/|-)(?:[1-9]\d\d\d|\d[1-9]\d\d|\d\d[1-9]\d|\d\d\d[1-9])$|^(?:(?:0?[1-9]|1[0-2])(\/|-)(?:0?[1-9]|1\d|2[0-8]))(\/|-)(?:[1-9]\d\d\d|\d[1-9]\d\d|\d\d[1-9]\d|\d\d\d[1-9])$|^(0?2(\/|-)29)(\/|-)(?:(?:0[48]00|[13579][26]00|[2468][048]00)|(?:\d\d)?(?:0[48]|[2468][048]|[13579][26]))$/,
                    "alertText": "* 请输入有效的日期，例如：2015-01-01 00::00:00"
                },
                //tls warning:homegrown not fielded 
                "dateTimeFormat": {
                    "regex": /^\d{4}[\/\-](0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])\s+(1[012]|0?[1-9]){1}:(0?[1-5]|[0-6][0-9]){1}:(0?[0-6]|[0-6][0-9]){1}\s+(am|pm|AM|PM){1}$|^(?:(?:(?:0?[13578]|1[02])(\/|-)31)|(?:(?:0?[1,3-9]|1[0-2])(\/|-)(?:29|30)))(\/|-)(?:[1-9]\d\d\d|\d[1-9]\d\d|\d\d[1-9]\d|\d\d\d[1-9])$|^((1[012]|0?[1-9]){1}\/(0?[1-9]|[12][0-9]|3[01]){1}\/\d{2,4}\s+(1[012]|0?[1-9]){1}:(0?[1-5]|[0-6][0-9]){1}:(0?[0-6]|[0-6][0-9]){1}\s+(am|pm|AM|PM){1})$/,
                    "alertText": "* 请输入有效的日期或时间",
                    "alertText2": "可接受的格式： ",
                    "alertText3": "mm/dd/yyyy hh:mm:ss AM|PM 或 ",
                    "alertText4": "yyyy-mm-dd hh:mm:ss AM|PM"
                },
                "requiredInFunction": {
                    "func": function(field, rules, i, options) {
                        return (field.val() == "test") ? true : false;
                    },
                    "alertText": "* 必须输入 test"
                },
                "chinese": {
                    "regex": /^[\u4E00-\u9FA5a-zA-Z]+$/,
                    "alertText": "* 请输入有效的{tag}，只能包含中文汉字与英文字母"
                },
                //  中文字母和特殊字符 \_\.\。\,
                "chiMark": {
                    "regex": /^[\u4E00-\u9FA5a-zA-Z_。,，（）——\-\(\)\.]+$/,
                    "alertText": "* 请输入有效的{tag}，只能包含汉字，英文字母和指定字符-_。.,()"
                },
                // 支持中文、字母和数字
                "numChinese": {
                    "regex": /^[\u4E00-\u9FA5a-zA-Z0-9]+$/,
                    "alertText": "* 请输入有效的{tag}，只能包含中文汉字、英文字母或数字"
                },
                "NoSp": {
                    "regex": /^[^\s]+$/,
                    "alertText": "请输入有效的{tag}，名字不能有空格"
                },
                
            };

        }
    };
    $.validationEngineLanguage.newLang();
})(jQuery);