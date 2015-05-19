<?php
/**
 * 报表控制器
 *
 * @Package controller
 * @Date 2015-3-10
 * @Author Joe
 */
class StaticsController extends Base_Controller_Api {
    /**
     * 景区流量统计
	 *
	 * @Author Joe
     */
    public function pvAction() {
        $where['date'] = $this->getParam('date');
        $where['landscape_id'] = intval($this->getParam('landscape_id'));
        !$where['landscape_id'] && Lang_Msg::error("ERROR_LANDIMG_1");
		
        $data = PvModel::model()->search($where, "id,date,in_num,out_num,hour", 'hour asc');
		
		foreach ($data as $value) { // 按hour重排序
			unset($value['id']);
			$pvs[$value['hour']] = $value;
		}
		$result = [];
		$num = ($where['date'] == date('Y-m-d'))? date('H'): 23;
		for ($i=0; $i <= $num; $i++) { // 重构完整时间段数据
			if (isset($pvs[$i]['hour']) && $pvs[$i]['hour'] == $i) {
				$result[$i] = $pvs[$i];
			} else {
				$result[$i] = [
					'date'    => $where['date'],
					'in_num'  => 0,
					'out_nun' => 0,
					'hour'    => $i
				];
			}
		}
		
		// redis获取当日总流量统计
        $day_poi_num_cache_key = PvModel::model()->redisCacheKey.':'.date('Ymd');
		$statics = [0, 0];
		if($old = PvModel::model()->redis->hget($day_poi_num_cache_key, $where['landscape_id'])){
			$statics = explode('|', $old);
		} else {
			PvModel::model()->redis->hset($day_poi_num_cache_key, $where['landscape_id'], implode('|', $statics));
		}
		
        Lang_Msg::output([
			'statics' => [
				'in_num'  => $statics[0],
				'out_num' => $statics[1],
				'now_num' => max(0, ($statics[0] - $statics[1])),
			],
			'detail'  => $result
		]);
    }

}