<?php
/**
 * 产品仓库价格、库存策略
 *
 * Created by PhpStorm.
 * User: grg
 * Date: 11/3/14
 * Time: 2:33 PM
 */

class StrategyController extends Controller
{
	private $types = array('+', '-', '%+', '%-');

	/**
	 * 全部策略列表
	 *
	 * @author grg
	 */
	public function actionIndex() {
		$data = array();
		$org_id = Yii::app()->user->org_id;
		if (intval($org_id) > 0) {
			$params['supplier_id'] = $org_id;
			$params['current'] = isset($params['page']) ? $params['page'] : 1;
			$params['items'] = 20;
			$result = Ticketrule::api()->lists($params);
			if ($result['code'] == 'succ') {
				$data['lists'] = $result['body'];
				$data['pages'] = new CPagination($data['lists']['pagination']['count']);
				$data['pages']->pageSize = $params['items'];
			}
		}
		$this->render('index', $data);
	}

	/**
	 * 定制策略
	 *
	 * @author grg
	 */
	public function actionAmend() {
//		$units = array("", "十", "百", "千", "万", "十万", "百万", "千万", "亿");
		$id = Yii::app()->request->getParam('id');
		$data['id'] = $id;
		if (intval($id) > 0) {
			$result = Ticketrule::api()->detail(array(
				'id' => $id,
			    'supplier_id' => Yii::app()->user->org_id,
			    'show_items' => 1
			));
			if ($result['code'] == 'succ') {
				$data = $result['body'];
				foreach ($data['rule_items'] as $item) {
					$rule = array();
					$rule['date'] = $item['date'];
					$rule['s_price'] = $item['fat_price'];
					if (strpos($rule['s_price'], '%') === 0) {
						$rule['s_price']{0} = '';
						$rule['s_price'] .= '%';
					}
					$rule['g_price'] = $item['group_price'];
					if (strpos($rule['g_price'], '%') === 0) {
						$rule['g_price']{0} = '';
						$rule['g_price'] .= '%';
					}
//					if (intval($item['reserve']) > 0) {
//						$item['reserve'] = $item['reserve'] ?
//							round(
//								$item['reserve']/pow(
//									10, ($i = floor(log10($item['reserve'])))
//								), 2
//							) . $units[$i] : '';
//					}
					$rule['storage'] = $item['reserve'];
					$data['rules'][] = $rule;
				}
			}
		}
		$this->render('amend', $data);
	}

	public function actionCommit() {
		header('Content-type: application/json');

		$params = Yii::app()->request->getParam('params');
		$pid = Yii::app()->request->getParam('pid');

		$data['supplier_id'] = Yii::app()->user->org_id;
		$data['name'] = Yii::app()->request->getParam('name');
		$data['desc'] = Yii::app()->request->getParam('desc');
		$data['user_id'] = Yii::app()->user->uid;
		$data['user_name'] = Yii::app()->user->account;
		if (intval($pid) > 0) {
			$data['id'] = intval($pid);
			Ticketrule::api()->update($data);
		} else {
			$result = Ticketrule::api()->add($data);
			if ($result['code'] == 'succ') {
				$data['id'] = $pid = $result['body']['id'];
			} else {
				echo json_encode(array(
					'code' => 500,
				    'message' => '保存失败'.$result['message']
				), JSON_UNESCAPED_UNICODE);
				exit;
			}
		}

		if ($pid > 0 && count($params) > 0) {
			$data = array();
			$data['rule_id'] = $pid;
			$data['supplier_id'] = Yii::app()->user->org_id;
			$data['days'] = implode(',', $params);
			$data['fat_price'] = Yii::app()->request->getParam('s_price');
			if (intval($data['fat_price']) > 0) {
				$data['fat_price'] = $this->types[Yii::app()->request->getParam('s_type')] . $data['fat_price'];
			} else {
				unset($data['fat_price']);
			}
			$data['group_price'] = Yii::app()->request->getParam('g_price');
			if (intval($data['group_price']) > 0) {
				$data['group_price'] = $this->types[Yii::app()->request->getParam('g_type')] . $data['group_price'];
			} else {
				unset($data['group_price']);
			}
			$data['reserve'] = Yii::app()->request->getParam('storage');
			if (intval($data['reserve']) == 0) {
				unset($data['reserve']);
			}
			$data['user_id'] = Yii::app()->user->uid;
			$data['user_name'] = Yii::app()->user->account;
			$result = Ticketrule::api()->setitem($data);
			if ($result['code'] == 'succ') {
				echo json_encode(array(
					'code' => 200,
					'id' => $pid,
					'message' => '保存成功'
				), JSON_UNESCAPED_UNICODE);
				exit;
			} else {
				echo json_encode(array(
					'code' => 500,
					'message' => '保存失败'.$result['message']
				), JSON_UNESCAPED_UNICODE);
				exit;
			}
		}

		echo json_encode(array(
			'code' => 200,
			'id' => $pid,
			'message' => '保存成功'
		), JSON_UNESCAPED_UNICODE);
	}

	/**
	 * 删除条目
	 *
	 * @author grg
	 */
	public function actionDelete() {
		$id = Yii::app()->request->getParam('id');
		$date = Yii::app()->request->getParam('date');
		if (intval($id) > 0 && strlen($date) == 10) {
			$result = Ticketrule::api()->delitem(array(
				'rule_id' => $id,
				'days' => $date,
				'user_id' => Yii::app()->user->uid,
				'user_name' => Yii::app()->user->account,
				'supplier_id' => Yii::app()->user->org_id
			));
			if ($result['code'] == 'succ') {
				echo 1;
				exit;
			}
		}
		echo 0;
	}

	/**
	 * 删除规则
	 *
	 * @author grg
	 */
	public function actionDel() {
		if (Yii::app()->request->isPOSTRequest) {
            $id = Yii::app()->request->getParam('id');
            $result = Ticketrule::api()->update(array(
				'id' => $id,
			    'supplier_id' => Yii::app()->user->org_id,
			    'deleted' => 1,
			    'user_id' => Yii::app()->user->uid,
			    'user_name' => Yii::app()->user->account
			));
			if($result['code'] == "succ"){
                echo json_encode(array('error'=>0,'message'=>""));
            }else{
            	echo json_encode(array('error'=>1,'message'=>$result['message']));
            }
        }else{
            Throw new  CHttpException('404',"找不到请求的页面!");
        }
		// $id = Yii::app()->request->getParam('id');
		// if (intval($id) > 0) {
		// 	$result = Ticketrule::api()->update(array(
		// 		'id' => $id,
		// 	    'supplier_id' => Yii::app()->user->org_id,
		// 	    'deleted' => 1,
		// 	    'user_id' => Yii::app()->user->uid,
		// 	    'user_name' => Yii::app()->user->account
		// 	));
		// 	if ($result['code'] == 'succ') {
  //               $this->redirect("/ticket/strategy/");
		// 		exit;
		// 	}else{
		// 		$this->redirect(array("/ticket/strategy/",'error'=>$result['message']));
		// 	}
		// }
		// Throw new  CHttpException('404',"找不到请求的页面!");
	}
}
