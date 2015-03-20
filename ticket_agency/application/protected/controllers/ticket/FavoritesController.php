<?php
use \common\huilian\utils\Header;

/**
 * Created by PhpStorm.
 * User: grg
 * Date: 11/19/14
 * Time: 2:19 PM
 */

class FavoritesController extends Controller
{

	public function actionIndex() {
		$data['type'] = Yii::app()->request->getParam('type', 1);
		$data['name'] = trim(Yii::app()->request->getParam('name'));
		$page = Yii::app()->request->getParam('page');

		$params = array(
			'organization_id' => Yii::app()->user->org_id,
		    'type' => $data['type'],
		    'current' => isset($page)?$page:0,
		    'items' => 15
		);
		if ($data['name']) {
			$params['name'] = $data['name'];
		}
		
		$result = Subscribes::api()->lists($params);
		if ($result['code'] == 'succ' && isset($result['body']['data'])) {
			$lists = $this->_getlan($result['body']['data']);
            $data['lists'] = $lists;
			$pagination = $result['body']['pagination'];
			$pages = new CPagination($pagination['count']);
			$pages->pageSize = $params['items']; #每页显示的数目
			$data['pages'] = $pages;
		}
		$this->render('index', $data);
	}

    /*
    * 获取景区名字
    * @return string
    */
    private function _getlan($lists = array()){
        if (!isset($singleLans)) {
            //得到所有景点信息
            $ids = PublicFunHelper::arrayKey($lists, 'scenic_id');
            $param = array();
            $param['ids'] = join(',', $ids);
            $param['items'] = 100000;
            $param['fields'] = 'id,name';
            $data = Landscape::api()->lists($param,true,30);
            $singleLans = PublicFunHelper::ArrayByUniqueKey(ApiModel::getLists($data), 'id');
        }
        if(is_array($lists)){
            foreach($lists as $key => $value){
                $_lans = explode(',', $value['scenic_id']);
                $lan_name = '';
                foreach ($_lans as $id) {
                    if (!empty($singleLans[$id])) {
                        $lan_name .= $singleLans[$id]['name'] . ' ';
                    }
                }
                $lists[$key]['lan_name'] = $lan_name;
            }
            return $lists;
        }
    }

	/**
	 * 加入、移除收藏
	 * @author grg
	 */
	public function actionToggle() {
		$type = Yii::app()->request->getParam('single');
		$done = Yii::app()->request->getParam('done');
		$done = intval($done);
		if ($done) {
			$result = Subscribes::api()->delete(array(
				'ticket_id' => Yii::app()->request->getParam('id'),
			    'organization_id' => Yii::app()->user->org_id,
			    'type' => intval($type)
			), 0);
		} else {
			$result = Subscribes::api()->add(array(
				'ticket_id' => Yii::app()->request->getParam('id'),
				'organization_id' => Yii::app()->user->org_id,
				'name' => Yii::app()->request->getParam('name'),
                'fat_price' =>  Yii::app()->request->getParam('fat_price'),
                'group_price' =>  Yii::app()->request->getParam('group_price'),
				'type' => intval($type)
			), 0);
		}
		//echo "<pre>";print_r($result);die("</pre>");
		if ($result['code'] == 'succ') {
			echo json_encode(array(
				'code' => 1,
			    'done' => 1 - $done
			), JSON_UNESCAPED_UNICODE);
		} else {
			echo json_encode(array(
				'code' => 0,
				'done' => $done
			), JSON_UNESCAPED_UNICODE);
		}
	}
} 
