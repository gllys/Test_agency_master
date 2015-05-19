<?php
require_once Yii::getPathOfAlias('ext').'/phpPasswordHashingLib/passwordLib.php';
class RegsupplyController extends Controller {

    public function actionIndex() {
        $organizations = array();
//        $org_id = Yii::app()->user->org_id;
//        if (!empty($org_id) && intval($org_id) > 0) {
//            $info = Organizations::api()->show(array('id' => $org_id), 0);
//            if ($info['code'] == 'succ') {
//                $organizations = ApiModel::getData($info);
//                if ($organizations['status'] != 0) {
//                    $this->redirect('/');
//                }
//            }
//        }
        $this->render('index', compact('organizations'));
    }

    //注册供应商
    public function actionCreate() {
        if (!Yii::app()->request->isPostRequest) {
            return false;
        }

        if (empty($_POST['province_id']) && empty($_POST['city_id']) && empty($_POST['district_id'])) {
            echo json_encode(array('errors' => '省市区至少选择一个'));
            exit;
        }

        //优先判断用户是否存在
        $result = SupplyAccount::api()->search(array('account' => $_POST['account']));
        if ($result['message']) {
            echo json_encode(array('errors' => '用户名已存在'));
            exit;
        }

        $paramL = array(
            'district_id' => $_POST['district_id'],
            'city_id' => $_POST['city_id'],
            'province_id' => $_POST['province_id'],
            'business_license' => $_POST['business_license'],
            'status' => 1,
            'verify_status' => 'checked',
            'address' => $_POST['address'],
            'contact' => $_POST['contact'],
            'mobile' => $_POST['mobile'],
            'name' => $_POST['name'],
            'fax' => $_POST['fax'],
            'email' => $_POST['email'],
            'telephone' => $_POST['telephone'],
            'contact' => $_POST['contact'],
            'abbreviation' => $_POST['abbreviation'],
            'description' => $_POST['description'],
        );
        //确定是否为景区机构
        if ($_POST['sell_role'] == 'scenic') {
            $paramL['supply_type'] = 1;
            $paramL['partner_type'] = $_POST['partner_type'];
            $paramL['partner_identify'] = "{'username':'huilian'}"; // 合作机构验证信息标识
        } else {
            $paramL['supply_type'] = 0;
        }
        $paramL['type'] = 'supply';
        $rs = Organizations::api()->reg($paramL);
        if ($rs['code'] == 'succ') {
            $param['organization_id'] = $rs['body']['id'];
            $paramU = array(
                'account' => $_POST['account'],
                'password' => password_hash($_POST['password'], PASSWORD_BCRYPT, array('cost' => 8)),
                'password_str' => $_POST['password'],
                'organization_id' => $param['organization_id'],
                'status' => 1,
                'is_super' => 1,
                'mobile' => $_POST['mobile'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            );
            //根据机构类型的不同，添加不同的用户
            $paramU['sell_role'] = $_POST['sell_role'];
            $userRs = SupplyAccount::api()->add($paramU);

            //AgencyUser::api()->debug = true;
            if ($userRs['code'] != 'succ') {
                echo json_encode(array('errors' => '用户已存在'));
                $paramEdit = array(
                    'id' => $param['organization_id'],
                    'is_del' => 1
                );
                Organizations::api()->edit($paramEdit);
            } else {
                echo json_encode(array('data' => $_POST));
            }
        } else {
            echo json_encode(array('errors' => $rs['message']));
        }
    }

}
