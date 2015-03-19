<style>
    #try_print_ticket {
        position: static;
        visibility: visible;
        margin: 0px;
        padding: 0px;
        width: 80mm;
        height: 190mm;
        font-size: 12px;
        font-family: 黑体;
    }

    div, p {
        margin: 0px;
        padding: 0px;
    }

    .head:after, .foot:after, .footer:after {
        content: '';
        display: inline-block;
        height: 100%;
        width: 1px;
        vertical-align: middle;
    }

    .info, .qr {
        display: inline-block;
        vertical-align: middle
    }

    .img {
        width: 18mm;
    }
</style>
<div id="try_print_ticket">
    <div class="main print_box" style="width: 80mm;font-size: 14px;">
        <div class="head" style="height: 30mm; position: relative; margin-top: 5mm">
            <span class="qr"><img src="<[qrcode]>" alt="" class="img"></span>

            <div class="info" style="position: absolute;margin-top: 3mm">
                <p class="order_id">No.<[code]></p>

                <p class="ticket_name"><[ticket_name]></p>

                <p class="payment"><[payment]>-已支付</p>

                <p class="begin_time">游玩日期：<[begin_time]></p>
            </div>
        </div>
        <div class="body" style="width: 80mm;height: 100mm; font-size: 12px;">
        </div>
        <div class="footer" style="height: 24mm;">
			    <span class="qr">
				    <img src="<[qrcode]>" class="img">
				</span>
        </div>
        <div class="foot" style="height: 30mm;position: relative; padding-top: 5mm">
                <span class="qr">
					<img src="<[qrcode]>" class="img">
				</span>

            <div class="info" style="position: absolute;margin-top: 3mm">
                <p class="order_id">No.<[code]></p>

                <p class="ticket_name"><[ticket_name]></p>

                <p class="payment"><[payment]>-已支付</p>

                <p class="begin_time">游玩日期：<[begin_time]></p>
            </div>
        </div>
    </div>
</div>
