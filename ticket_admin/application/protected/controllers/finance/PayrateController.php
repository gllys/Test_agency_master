<?php
/**
 * User: xuhongbin <xuhongbin@ihuilian.com>
 * Date: 2015/03/06
 * Time: 12:02
 */
class PayrateController extends Controller
{
    public function actionIndex()
    {
        $param = array();
        $payrate = Payrate::api()->lists($param);
        
        $data['payrate'] = array();
        if('succ' == $payrate['code']){ 
            $data['payrate'] = $payrate['body'];
        }
        
        $this->render('index', $data);
    }
    
    // 保存费率
    public function actionSetting()
    {
        $error = 1; $msg = null;
        $param = $_REQUEST;
        if(!empty($param['rate']) && is_numeric($param['rate'])){
            $param['rate'] = $param['rate'] / 100;
            
            $payrate = Payrate::api()->set($param);
            if('succ' == $payrate['code']){ 
                $error = 0; $msg = $param['name'].'费率修改成功';
            }else{
                $msg = $payrate['message']; 
            }
        }else{
            $msg = '费率类型必须是数字类型';
        }
        
        $this->_end($error, $msg);
    }
}
