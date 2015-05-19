var storageCal = {
    init: {
        timeElement: null,
        calDiv: null,
        totalStorage: -1,
        calDivClose: function () {
            this.init.calDiv.style.display = "none";
        },
        handles: []
    },
    data: {},
    datePrice: {},
    strpad: function (str) {
        str = String(str);
        if (str.length == 1) {
            return "0" + str;
        } else {
            return str;
        }
    },
    outputHtml: function (json, year_month, fromDate) {
        var d = {};
        for (var r in json) {
            d[r.date] = r.price;
        }
        var rules = {};
        for (var k in json.rules) {
            rules[json.rules[k].date] = json.rules[k];
        }
        var nowTime = new Date();
        if (fromDate) {

            fromDate = fromDate.split("-");
            fromDate = fromDate.join("/");
            if ((new Date(fromDate)).getTime() > nowTime)
                nowTime = (new Date(fromDate));
        }

        nowTime.setHours(0, 0, 0, 0);

        nowTime = nowTime.getTime();//凌晨时间

        var beginTime = year_month + "-01";
        beginTime = beginTime.split("-");
        beginTime = beginTime.join("/");
        var beginDate = new Date(beginTime);
        var year = beginDate.getFullYear();
        var month = beginDate.getMonth() + 1;
        var lastmonth = (month - 1 == 0) ? 12 : month - 1;
        var nextmonth = (month + 1 == 13) ? 1 : month + 1;
        var days = (new Date(beginDate.getFullYear(), beginDate.getMonth() + 1, 0)).getDate();
        var monthdays = days;
        var emptydays = beginDate.getDay();
        var endtime = year_month + "-" + days;
        endtime = endtime.split("-");
        endtime = endtime.join("/");
        var endDate = new Date(endtime);
        days += beginDate.getDay() + (7 - endDate.getDay());

        beginDate.setTime(beginDate.getTime() - (24 * 3600000 * beginDate.getDay()));
        var lastmonth_none = (json.about.mintime * 1000 - beginDate.getTime() < 0) ? "" : "lastmonth_none";
        var nextmonth_none = (json.about.maxtime * 1000 - endDate.getTime() > 0) ? "" : "nextmonth_none";
        var html = '<div class="storageCal" id="storageCalContent"><div class="monthbox">' +
            '<div class="title"><span class="lastmonth ' + lastmonth_none + '"><a title="' + lastmonth + '月" class="fa fa-chevron-left"></a></span><span class="nextmonth ' + nextmonth_none + '"><a title="' + nextmonth + '月" class="fa fa-chevron-right"></a></span><span class="year">'
            + year + '年' + month + '月 <span><input type="checkbox"  id="checkAll"><label for="checkAll">全选</label></span></span><span class="close"></span><input type="hidden" class="year_month" value="' + year_month + '"/></div>' +
            '<table>' +
            '<thead>' +
            '<tr>' +
            '<th class="weeken"><div class="ckbox ckbox-success"><input type="checkbox" id="weekSele0" value="0" class="weekSele"><label for="weekSele0">日</label></div></th>' +
            '<th><div class="ckbox ckbox-success"><input type="checkbox" id="weekSele1" value="1" class="weekSele"><label for="weekSele1">一</label></div></th>' +
            '<th><div class="ckbox ckbox-success"><input type="checkbox" id="weekSele2" value="2" class="weekSele"><label for="weekSele2">二 </label></div></th>' +
            '<th><div class="ckbox ckbox-success"><input type="checkbox" id="weekSele3" value="3" class="weekSele"><label for="weekSele3">三</label></div></th>' +
            '<th><div class="ckbox ckbox-success"><input type="checkbox" id="weekSele4" value="4" class="weekSele"><label for="weekSele4">四</label></div></th>' +
            '<th><div class="ckbox ckbox-success"><input type="checkbox" id="weekSele5" value="5" class="weekSele"><label for="weekSele5">五</label></div></th>' +
            '<th class="weeken"><div class="ckbox ckbox-success"><input type="checkbox" id="weekSele6" value="6" class="weekSele"><label for="weekSele6">六</label></div></th>' +
            '</tr></thead><tbody>';

        for (var i = 0, j = 0; i < days - 1; i++) {
            if (i % 7 == 0) {
                html += '<tr>';
            }
            var date = beginDate.getFullYear() + "-" + this.strpad((beginDate.getMonth() + 1)) + "-" + this.strpad(beginDate.getDate());
            var s_price = "";
            var s_priceT = "";
            var g_price = "";
            var g_priceT = "";
            var valid = "";
            var storage = "";
            var storageT = "";
            var no_data = true;
            if (rules[date]) {
                this.datePrice[date] = rules[date].price;
                s_price = rules[date]['s_price'];
                s_priceT = s_price !== "" ? "散客价 " + s_price + '</br>' : "";

                g_price = rules[date]['g_price'];
                g_priceT = g_price !== "" ? "团队价 " + g_price + '</br>' : "";
                storage = rules[date]['storage'];
                storageT = storage !== "" && storage != 0 ? "库存 " + storage : "";                
                (function(){
                    if ((s_priceT + g_priceT + storageT) != "") {
                        no_data = false;
                    }
                })()
                j++;
            }


            if (i < emptydays || i >= monthdays + emptydays) {
                html += '<td><div class="detail"></div></td>';
            } else if (beginDate.getTime() < nowTime) {
                html += '<td><div date="' + date + '" class="detail">' +
                '<span>' + beginDate.getDate() + '</span>' +
                '<div class="price"></div></div></td>';
            } else {
                //html += '<td><div s_price="' + s_price + '" storage="' + storage + '" g_price="' + g_price + '" date="' + date + '" class="detail valid">' +
                html += '<td><div date="' + date + '" class="detail valid">' +
                '<div class="ckbox ckbox-primary"><input type="checkbox" id="checkbox-' + beginDate.getDate() + '" '+($.inArray(date, dateSelected) != -1 ? 'checked="checked"' : '')+'>' +
                '<label for="checkbox-' + beginDate.getDate() + '">' + beginDate.getDate() + '</label>' +
                (no_data ? '' : '<i class="rule-remove-btn glyphicon glyphicon-remove red" style="float:right;color: #880000;cursor:pointer;"></i>') +
                '<div class="price">' + s_priceT + g_priceT + storageT + '</div></div></td>';
            }
            if (i % 7 == 6) {
                html += '</tr>';
            }
            beginDate.setTime(beginDate.getTime() + 24 * 3600000);
        }
        html += "</tbody></table></div>";
        return html;
    },
    show: function (year_month, pid, fromDate) {
        var html;
        storageCal.init.calDiv.style.display = "block";
        storageCal.init.calDiv.innerHTML = "<div class='loading'><p class='tip'>数据加载中,请稍等....</p></div>";
        var that = this;

        var bindHtmlEvent = function () {
            $(".weekSele").click(function () {
                var indexNum = this.value; 
                if (this.checked == true) {
                    $("#storageCalContent tbody tr").each(function () {
                        $(this).find("td").eq(indexNum).find("input").prop("checked", true);
                    })
                } else {
                    $("#storageCalContent tbody tr").each(function () {
                        $(this).find("td").eq(indexNum).find("input").prop("checked", false);
                    })
                }
            });
            $("#storageCalContent #checkAll").click(function () {
                if (this.checked == true) {
                    $("#storageCalContent input[type='checkbox']").each(function () {
                        this.checked = true;
                    })
                } else {
                    $("#storageCalContent input[type='checkbox']").each(function () {
                        this.checked = false;
                    })
                }
            });
            //因url写死，改放在调用页面处理
//            $('.rule-remove-btn').click(function(){
//                var date = $(this).parent().parent().attr('date');
//                if (!confirm("确认要删除"+date+"的规则设定吗？")) return;
//                var pid = $("#pid").val();
//                $.get('/ticket/strategy/delete', {id: pid, date: date}, function(result){
//                    if (result == 1) {
//                        location.href = '/ticket/strategy/amend/id/'+pid;
//                    }
//                });
//
//            });
            //$("#storageCalContent div.detail input[type='checkbox']").click(function () {
            //    var g_price = $(this).parent().parent().attr("g_price");
            //    var s_price = $(this).parent().parent().attr("s_price");
            //    var storage = $(this).parent().parent().attr("storage") == "-1" ? "" : $(this).parent().parent().attr("storage");
            //    $("#s_price").val(s_price);
            //    $("#g_price").val(g_price);
            //    $("#daystorage").val(storage);
            //});

            /* $("#storageCalContent span.close").click(function(){
             storageCal.init.calDivClose();
             }) */
            var time = year_month + "-01";
            time = time.split("-");
            time = time.join("/");
            time = new Date(time);
            var lasttime = new Date(time.getFullYear(), time.getMonth() - 1, 1);
            var nexttime = new Date(time.getFullYear(), time.getMonth() + 1, 1);
            var lastmonth = lasttime.getFullYear() + "-" + that.strpad(lasttime.getMonth() + 1);
            var nextmonth = nexttime.getFullYear() + "-" + that.strpad(nexttime.getMonth() + 1);
            $("#storageCalContent .lastmonth").click(function () {
                $("#storageCal tbody td input").each(function () {
                    if (this.checked == false) return;
                    var detail = $(this).parent().parent();
                    dateSelected.push(detail.attr("date"));
                });
                storageCal.show(lastmonth, pid, fromDate);
            });
            $("#storageCalContent .nextmonth").click(function () {
                $("#storageCal tbody td input").each(function () {
                    if (this.checked == false) return;
                    var detail = $(this).parent().parent();
                    dateSelected.push(detail.attr("date"));
                });
                storageCal.show(nextmonth, pid, fromDate);
            });
        }


        if (that.data[year_month]) {
            html = that.outputHtml(that.data[year_month], year_month, fromDate);
            storageCal.init.calDiv.innerHTML = html;
            bindHtmlEvent();
        } else {
            /*
             $.ajax({
             url:"/d/ajaxCall/storage_Calendar_Ajax.php",
             async:true,
             type:"POST",
             dataType:"json",
             data:{"orderMonth":year_month,
             "pid":pid
             },
             success:function(json){
             html=storageCal.outputHtml(json,year_month,fromDate);
             storageCal.data[year_month]=json;
             storageCal.init.calDiv.innerHTML=html;
             bindHtmlEvent();
             }
             })
             */

            //alert(json.rules[0].date)

            html = storageCal.outputHtml(json, year_month, fromDate);
            storageCal.data[year_month] = json;
            storageCal.init.calDiv.innerHTML = html;
            bindHtmlEvent();
        }

        //return false;


    }
};
