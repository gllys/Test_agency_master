<?php

/* 
 * 分销商策略控制器
 * Date 2015-01-19
 * 
 */

class PolicyController extends Controller
{
    /**
     * 默认方法展示策略列表
     */
    public function actionIndex() {
        $data = array();
		$org_id = Yii::app()->user->org_id;
		if (intval($org_id) > 0) {
			$params['supplier_id'] = $org_id;
			$params['current'] = isset($_GET['page']) ? $_GET['page'] : 1;
			$params['items'] = 20;
            //获取策略列表
			$result = Ticketpolicy::api()->lists($params);
            
			if (isset($result['code']) && $result['code'] == 'succ') {
				$data['supplier_id'] = $org_id;
				$data['lists'] = $result['body'];
				$data['pages'] = new CPagination($data['lists']['pagination']['count']);
				$data['pages']->pageSize = $params['items'];
			}
		}

        // 获取机构的信用和储值状态
        $isCredit = Organizations::api()->show(array('id'=>$org_id,'fields'=>"is_credit,is_balance"));
        $data['isShow'] = ApiModel::getData($isCredit);

		$this->render('index', $data);
    }
    
    /**
     * 删除分销策略
     * @throws CHttpException 404
     */
    public function actionDel() {
        if (Yii::app()->request->isPOSTRequest) {
            //根据策略id删除
            $id = Yii::app()->request->getParam('id');
            $result = Ticketpolicy::api()->del(array(
				'id' => $id,
			    'supplier_id' => Yii::app()->user->org_id,
			    'user_id' => Yii::app()->user->uid,
			    'user_name' => Yii::app()->user->name,
			    'user_account' => Yii::app()->user->account
			));
			if(isset($result['code']) && $result['code'] == "succ"){
                echo json_encode(array('error'=>0,'message'=>""));
            }else{
            	echo json_encode(array('error'=>1,
                    'message'=>isset($result['message'])?$result['message']:'数据未返回'));
            }
        }else{
            Throw new  CHttpException('404',"找不到请求的页面!");
        }
    }
    
    /**
     * 获取当前用户的分销商
     * @return string 分销商列表的html片段
     */
    public function actionGetDistributor() {
        //从组织机构api获取分销商列表
        $result = Organizations::api()->getlist(array('supply_id'=>Yii::app()->user->org_id,'show_all'=> 1));

        // 获取机构的信用和储值状态
        $isCredit = Organizations::api()->show(array('id'=>Yii::app()->user->org_id,'fields'=>"is_credit,is_balance"));
        $isShow = ApiModel::getData($isCredit);

        if(isset($result['code']) && $result['code'] == "succ"){
            $html = '';      //合作的分销商
            $otherhtml = ''; //未合作分销商
            $newhtml = ''; //新合作分销商
            if(count($result['body']['data'])>0){
                //合作的分销商
                foreach($result['body']['data'] as $distr){
                    $html .='<tr>';
                    $html .='<td style="width:125px;">'.$distr['distributor_name'].'</td>';
                    $html .='<td style="width:65px;"><input id="fp_'.$distr["distributor_id"].'" type="checkbox" value="'.$distr["distributor_id"].'" name="fblackname_arr['.$distr["distributor_id"].']" class="fblackgroup"></td>';
                    $html .='<td style="width:100px;"><input id="gp_'.$distr["distributor_id"].'" type="checkbox" value="'.$distr["distributor_id"].'" name="gblackname_arr['.$distr["distributor_id"].']" class="gblackgroup"></td>';
                    $html .='<td style="width:167px;"><input type="text" id="s_price_'.$distr["distributor_id"].'" name="s_price['.$distr["distributor_id"].']" class="spinner"></td>';
                    $html .='<td style="width:148px;"><input type="text" id="g_price_'.$distr["distributor_id"].'" name="g_price['.$distr["distributor_id"].']" class="spinner"></td>';
                    if($isShow['is_credit'] == 1){
                        $html .='<td style="width:130px;"><input id="credit_'.$distr["distributor_id"].'" type="checkbox" value="'.$distr["distributor_id"].'" name="credit_arr['.$distr["distributor_id"].']" class="creditgroup"></td>';
                    }
                    if($isShow['is_balance'] == 1){
                        $html .='<td><input id="advance_'.$distr["distributor_id"].'" type="checkbox" value="'.$distr["distributor_id"].'" name="advance_arr['.$distr["distributor_id"].']" class="advancegroup" style="margin-left: 17px;"></td>';
                    }
                    $html .='</tr>';
                }
                $newhtml  ='<tr>';
                $newhtml .='<td style="width:200px;">新合作分销商</td>';
                $newhtml .='<td style="width:100px;"><input id="fp_n" type="checkbox" value="1" name="new_fat_blackname_flag" class="new_fat_blackname_flag"></td>';
                $newhtml .='<td style="width:160px;"><input id="gp_n" type="checkbox" value="1" name="new_group_blackname_flag" class="new_group_blackname_flag"></td>';
                $newhtml .='<td style="width:270px;"><input type="text" class="spinner" id="s_price_n" name="new_fat_price" value="" ></td>';
                $newhtml .='<td style="width:148px;"><input type="text" class="spinner" id="g_price_n" name="new_group_price" value="" ></td>';
                if($isShow['is_credit'] == 1){
                    $newhtml .=' <td style="width:150px;"><input id="credit_n" type="checkbox" value="0" name="new_credit_flag" class="new_credit_flag"></td>';
                }else{
                    $newhtml .=' <td></td>';
                }
                if($isShow['is_balance'] == 1){
                    $newhtml .='<td><input id="advance_n" type="checkbox" value="0" name="new_advance_flag" class="new_advance_flag" ></td>';
                }
                else{
                    $newhtml .=' <td></td>';
                }
                $newhtml .='</tr>';

                $otherhtml  ='<tr style="background-color:#f7f7f7;">';
                $otherhtml .='<td style="width:125px;">未合作分销商</td>';
                $otherhtml .='<td style="width:65px;"><input id="fp_0" type="checkbox" value="0" name="fblackname_arr[0]"></td>';
                $otherhtml .='<td style="width:100px;"><input id="gp_0" type="checkbox" value="0" name="gblackname_arr[0]"></td>';
                $otherhtml .='<td style="width:167px;"><input type="text" id="s_price_0" name="s_price[0]" class="spinner"></td>';
                $otherhtml .='<td style="width:148px;"><input type="text" id="g_price_0" name="g_price[0]" class="spinner"></td>';
//                $otherhtml .='<td style="width:149px;"><input id="credit_0" type="checkbox" value="0" name="credit_arr[0]"></td>';
//                $otherhtml .='<td><input id="advance_0" type="checkbox" value="0" name="advance_arr[0]"></td>';
                $otherhtml .='<td></td>';
                $otherhtml .='<td></td>';
                $otherhtml .='</tr>';
            }
            echo json_encode(array('error'=>0,'message'=>"",'data'=>$html,'otherdata'=>$otherhtml, 'newdata'=>$newhtml));
        }else{
            echo json_encode(array('error'=>1,'message'=>$result['message']));
        }
    }
    
    /**
     * 获取分销规则的详情
     * @throws CHttpException 404
     */
    public function actionDetail() {

        // 获取机构的信用和储值状态
        $isCredit = Organizations::api()->show(array('id'=>Yii::app()->user->org_id,'fields'=>"is_credit,is_balance"));
        $isShow = ApiModel::getData($isCredit);

        if (Yii::app()->request->isPOSTRequest) {
            $id = Yii::app()->request->getParam('id');
            $result = Ticketpolicy::api()->detail(array(
				'id' => $id,
			    'supplier_id' => Yii::app()->user->org_id,
			    'show_items' => 1
			));

           // print_r($result);
			if(isset($result['code']) && $result['code'] == "succ"){
                $dist_arr = array();//保存分销商名称
                $html = '';      //合作的分销商
                $newhtml = ''; //新合作分销商
                $otherhtml = ''; //未合作分销商
                $name = isset($result['body']['name'])?$result['body']['name']:'';
                $note = isset($result['body']['note'])?$result['body']['note']:'';
                //获取分销商名称
                $dist_result = Organizations::api()->getlist(array('supply_id'=>Yii::app()->user->org_id,'show_all' => 1));
                if(isset($dist_result['code']) && $dist_result['code'] == "succ"){
                    foreach($dist_result['body']['data'] as $one_distr){
                        $dist_arr[$one_distr['distributor_id']] = $one_distr['distributor_name'];
                    }
                }
                if(count($result['body']['items'])>0){
                    //分销商列表数据
                    foreach($result['body']['items'] as $distr){
                        $tmp_fblackname = $distr['fat_blackname_flag']==1?'checked="checked"':'';
                        $tmp_gblackname = $distr['group_blackname_flag']==1?'checked="checked"':'';
                        $tmp_credit = $distr['credit_flag']==1?'':'checked="checked"';
                        $tmp_advance = $distr['advance_flag']==1?'':'checked="checked"'; 
                        $distributor_name = '';
                        //循环分销商数组，获得未被设置规则的分销商（可能是新添加的分销商）
                        if(isset($dist_arr[$distr["distributor_id"]])){
                            $distributor_name = $dist_arr[$distr["distributor_id"]];
                            unset($dist_arr[$distr["distributor_id"]]);
                        }
                        $html .='<tr>';
                        $html .='<td style="width:200px;">'.$distributor_name.'</td>';
                        $html .='<td style="width:100px;"><input id="fp_'.$distr["distributor_id"].'" type="checkbox" value="'.$distr["distributor_id"].'" name="fblackname_arr['.$distr["distributor_id"].']" '.$tmp_fblackname.' class="fblackgroup"></td>';
                        $html .='<td style="width:100px;"><input id="gp_'.$distr["distributor_id"].'" type="checkbox" value="'.$distr["distributor_id"].'" name="gblackname_arr['.$distr["distributor_id"].']" '.$tmp_gblackname.' class="gblackgroup"></td>';
                        $html .='<td style=";"><input type="text" id="s_price_'.$distr["distributor_id"].'" name="s_price['.$distr["distributor_id"].']" class="spinner" value="'.$distr['fat_price'].'"></td>';
                        $html .='<td style=""><input type="text" id="g_price_'.$distr["distributor_id"].'" name="g_price['.$distr["distributor_id"].']" class="spinner" value="'.$distr['group_price'].'"></td>';
                        if($isShow['is_credit'] == 1){
                            $html .='<td style="width:150px;"><input id="credit_'.$distr["distributor_id"].'" type="checkbox" value="'.$distr["distributor_id"].'" name="credit_arr['.$distr["distributor_id"].']" '.$tmp_credit.' class="creditgroup"></td>';
                        }
                        if($isShow['is_balance'] == 1){
                            $html .='<td style="width:150px;"><input id="advance_'.$distr["distributor_id"].'" type="checkbox" value="'.$distr["distributor_id"].'" name="advance_arr['.$distr["distributor_id"].']" '.$tmp_advance.' class="advancegroup" style="margin-left: 17px;"></td>';
                        }

                        $html .='</tr>';
                    }

                    $new_fblackname = $result['body']['new_fat_blackname_flag']==1?'checked="checked"':'';
                    $new_gblackname = $result['body']['new_group_blackname_flag']==1?'checked="checked"':'';
                    $new_credit = $result['body']['new_credit_flag']==0?'checked="checked"':'';
                    $new_advance = $result['body']['new_advance_flag']==0?'checked="checked"':'';
                    //列出未被设置规则的分销商  未设置的新合作分销商具有新合作分销商的属性
                    foreach($dist_arr as $distr_id => $distr_name){
                        $html .='<tr>';
                        $html .='<td style="width:200px;">'.$distr_name.'</td>';
                        $html .='<td style="width:162px;"><input id="p_'.$distr_id.'" type="checkbox" value="' .$distr_id.'" name="fblackname_arr['.$distr_id.']"  '.$new_fblackname.' class="fblackgroup"></td>';
                        $html .='<td style="width:162px;"><input id="p_'.$distr_id.'" type="checkbox" value="' .$distr_id.'" name="gblackname_arr['.$distr_id.']"  '.$new_gblackname.' class="gblackgroup"></td>';
                        $html .='<td style="width:167px;"><input type="text" id="s_price_'.$distr_id.'" name="s_price['.$distr_id.']" class="spinner" value="'.$result['body']['new_fat_price'].'"></td>';
                        $html .='<td style="width:148px;"><input type="text" id="g_price_'.$distr_id.'" name="g_price['.$distr_id.']" class="spinner" value="'.$result['body']['new_group_price'].'"></td>';
                        if($isShow['is_credit'] == 1){
                            $html .='<td style="width:130px;"><input id="credit_'.$distr_id.'" type="checkbox" value="'.$distr_id.'" name="credit_arr['.$distr_id.']" class="creditgroup" '.$new_credit.' ></td>';
                        }
                        if($isShow['is_balance'] == 1){
                            $html .='<td><input id="advance_'.$distr_id.'" type="checkbox" value="'.$distr_id.'" name="advance_arr['.$distr_id.']" class="advancegroup" style="margin-left: 17px;"'.$new_advance.' ></td>';
                        }

                        $html .='</tr>';
                    }


                    $newhtml  ='<tr>';
                    $newhtml .='<td style="width:200px;">新合作分销商</td>';
                    $newhtml .='<td style="width:162px;"><input id="fp_n" type="checkbox" value="1" name="new_fat_blackname_flag" class="new_fat_blackname_flag" '.$new_fblackname.'></td>';
                    $newhtml .='<td style="width:162px;"><input id="gp_n" type="checkbox" value="1" name="new_group_blackname_flag" class="new_group_blackname_flag" '.$new_gblackname.'></td>';
                    $newhtml .='<td style="width:167px;"><input type="text" class="spinner" id="s_price_n" name="new_fat_price" value="'.$result['body']['new_fat_price'].'" ></td>';
                    $newhtml .='<td style="width:148px;"><input type="text" class="spinner" id="g_price_n" name="new_group_price" value="'.$result['body']['new_group_price'].'" ></td>';
                    if($isShow['is_credit'] == 1){
                        $newhtml .=' <td style="width:150px;"><input id="credit_n" type="checkbox" value="0" name="new_credit_flag" class="new_credit_flag" '.$new_credit.'></td>';
                    }else{
                        $newhtml .=' <td></td>';
                    }
                    if($isShow['is_balance'] == 1){
                        $newhtml .='<td><input id="advance_n" type="checkbox" value="0" name="new_advance_flag" class="new_advance_flag" '.$new_advance.'></td>';
                    }else{  $newhtml .=' <td></td>';}
                    $newhtml .='</tr>';

                    $ftmp_blackname = $result['body']['other_fat_blackname_flag']==1?'checked="checked"':'';
                    $gtmp_blackname = $result['body']['other_group_blackname_flag']==1?'checked="checked"':'';
                    //$tmp_credit = $result['body']['other_credit_flag']==1?'':'checked="checked"';
                    // $tmp_advance = $result['body']['other_advance_flag']==1?'':'checked="checked"';
                    $otherhtml  ='<tr style="background-color:#f7f7f7;">';
                    $otherhtml .='<td style="width:200px;">未合作分销商</td>';                    
                    $otherhtml .='<td style="width:116px;"><input id="fp_0" type="checkbox" value="0" name="fblackname_arr[0]" '.$ftmp_blackname.'></td>';
                    $otherhtml .='<td style="width:116px;"><input id="gp_0" type="checkbox" value="0" name="gblackname_arr[0]" '.$gtmp_blackname.'></td>';
                    $otherhtml .='<td style="width:200px;"><input type="text" id="s_price_0" name="s_price[0]" class="spinner" value="'.$result['body']['other_fat_price'].'" ></td>';
                    $otherhtml .='<td style="width:200px;"><input type="text" id="g_price_0" name="g_price[0]" class="spinner" value="'.$result['body']['other_group_price'].'" ></td>';
//                    $otherhtml .='<td style="width:149px;"><input id="credit_0" type="checkbox" value="0" name="credit_arr[0]" '.$tmp_credit.'></td>';
//                    $otherhtml .='<td><input id="advance_0" type="checkbox" value="0" name="advance_arr[0]" '.$tmp_advance.'></td>';
                    $otherhtml .='<td style="width:149px;"></td>';
                    $otherhtml .='<td></td>';
                    $otherhtml .='</tr>';
                }
                echo json_encode(array('error'=>0,'message'=>"",'data'=>$html,'otherdata'=>$otherhtml,
                    'newdata'=>$newhtml,'dist_id'=>$id,'name'=>$name,'note'=>$note));
            }else{
            	echo json_encode(array('error'=>1,
                    'message'=>isset($result['message'])?$result['message']:'数据未返回'));
            }
        }else{
            Throw new  CHttpException('404',"找不到请求的页面!");
        }
    }
    /**
     * 保存分销策略
     */
    public function actionSave() {
        if (Yii::app()->request->isPostRequest) {

          //  print_r($_REQUEST);exit;

            $data = $_REQUEST;            
            $policy_items = array();
           // print_r($data);
            if (!isset($data['pname']) || empty($data['pname'])) {                
                $this->_end(1, '策略名不可以为空！');
            }
            
            if(!empty($data['distid'])){
                $field['id'] = $data['distid'];
            }
            $field['supplier_id'] = Yii::app()->user->org_id;	              //供应商ID
            $field['user_id'] = Yii::app()->user->uid;                        //操作者UID
            $field['user_name'] = Yii::app()->user->name;                     //操作者用户名
            $field['user_account'] = Yii::app()->user->account;               //操作者账号
            $field['name'] = $data['pname'];                                   //规则名称
            $field['note'] = $data['note'];                                   //说明
            $field['other_fat_price'] = isset($data['s_price'][0])?$data['s_price'][0]:0;	      //未合作分销商散客价
            $field['other_group_price'] = isset($data['g_price'][0])?$data['g_price'][0]:0;       //未合作分销商团客价
            $field['other_group_blackname_flag'] = isset($data['gblackname_arr'][0])?1:0;
            //未合作分销商黑名单开关：0关闭 1开启
            $field['other_fat_blackname_flag'] = isset($data['fblackname_arr'][0])?1:0;
            $field['other_credit_flag'] = isset($data['credit_arr'][0])?0:1;	                  //未合作分销商信用支付开关：0关闭 1开启
            $field['other_advance_flag'] = isset($data['advance_arr'][0])?0:1;                    //未合作分销商储值支付开关：0关闭 1开启

            $field['new_fat_price'] = isset($data['new_fat_price'])?$data['new_fat_price']:0;	      //新合作分销商散客价
            $field['new_group_price'] = isset($data['new_group_price'])?$data['new_group_price']:0;       //新合作分销商团客价
            $field['new_fat_blackname_flag'] = isset($data['new_fat_blackname_flag'])?1:0;	              //新合作分销商黑名单开关：0关闭 1开启
            $field['new_group_blackname_flag'] = isset($data['new_group_blackname_flag'])?1:0;
            $field['new_credit_flag'] = isset($data['new_credit_flag'])?0:1;	                  //新合作分销商信用支付开关：0关闭 1开启
            $field['new_advance_flag'] = isset($data['new_advance_flag'])?0:1;                    //新合作分销商储值支付开关：0关闭 1开启

            //各分销商的策略
            if(is_array($data['s_price'])){
                foreach ($data['s_price'] as $dist_id=>$s_price){
                    if($dist_id > 0){
                        $item['distributor_id'] = $dist_id;
                        $item['fat_price'] = empty($s_price)?0:$s_price;
                        $item['group_price'] = empty($data['g_price'][$dist_id])?0:$data['g_price'][$dist_id];
                        $item['fat_blackname_flag'] = isset($data['fblackname_arr'][$dist_id])?1:0;
                        $item['group_blackname_flag'] = isset($data['gblackname_arr'][$dist_id])?1:0;
                        $item['credit_flag'] = isset($data['credit_arr'][$dist_id])?0:1;
                        $item['advance_flag'] = isset($data['advance_arr'][$dist_id])?0:1;
                                                
                        $policy_items[] = $item;
                    }
                }
            }            
            $field['policy_items'] = json_encode($policy_items);
            //存在分销商id则是更新
           // print_r($field);
            if(isset($field['id'])){
                $rs = Ticketpolicy::api()->update($field);
            }else{
                $rs = Ticketpolicy::api()->add($field);
            }
            //Ticketpolicy::api()->debug= true;
            if ($rs['code'] == 'succ') {
                $this->_end(0, $rs['message']);
            }
            else {
                $this->_end(1, $rs['message']);
            }
        }
    }   
}