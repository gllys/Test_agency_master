<?php
require("DcLog.php");
$key		= "ab0fc97de2";
$server_id	= "601001";
$dc			= DcLog::getInstance($key, $server_id);

//玩家激活     平台帐号        角色ID   激活IP        广告商ID       广告渠道ID             广告素材ID            激活时间
//             string         string    string       string        string                 string               timestamp
$dc->activate($uuzu_passport,$role_id, $register_ip, $register_ad, $register_ad_channel, $register_ad_material,$activate_time);


//玩家升级
//            平台帐号   角色ID    原等级   新等级    升级时间
//            string   string    int       int      timestamp
$dc->upgrade($passport,$role_id, $level1, $level2, $upgrade_time);

//玩家黄金变化
//                平台帐号   角色ID    当前等级 改变类型      改变渠道
//                string    string    int      int          int 
$dc->gold_change($passport, $role_id, $level, $change_type, $channel,
								//	道具ID    道具数量    消耗黄金 黄金余额    消耗时间
								//  string    int        int      int        timestamp
									$item_id, $item_num, $gold,   $gold_left,$change_time);
									
									
//新服开服前一周，每15分钟跑一次									
//                    平台帐号    角色ID    等级    黄金    激活时间   最后登录时间(int) 日期 
//                    string      string    int     int   timestamp  timestamp       timestamp
$dc->user_snapshot_rt($passport, $rold_id, $level, $gold, $act_time, $last_login,      $date);


//开服一周后，每天跑一次									
//                 平台帐号    角色ID    等级    黄金    激活时间   最后登录时间(int) 日期  
//                 string      string    int     int   timestamp  timestamp       timestamp
$dc->user_snapshot($passport, $rold_id, $level, $gold, $act_time, $last_login,      $date);

?>