<?php
require_once Yii::getPathOfAlias('ext').'/phpPasswordHashingLib/passwordLib.php';
class RegagencyController extends Controller {

    public function actionIndex() {
        $organizations = array();
        $this->render('index', compact('organizations'));
    }

    //注册分销商
    public function actionCreate() {
        if (!Yii::app()->request->isPostRequest) {
            return false;
        }

        if (empty($_POST['province_id']) && empty($_POST['city_id']) && empty($_POST['district_id'])) {
            echo json_encode(array('errors' => '省市区至少选择一个'));
            exit;
        }

        //优先判断用户是否存在
        $result = AgencyAccount::api()->search(array('account'=>$_POST['account']));
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

        $paramL['tax_license'] = $_POST['tax_license'];
        $paramL['certificate_license'] = $_POST['certificate_license'];
        $paramL['is_distribute_person'] = $_POST['is_distribute_person'];
        $paramL['is_distribute_group'] = $_POST['is_distribute_group'];
        $paramL['agency_type'] = $_POST['agency_type'];
        $paramL['type'] = 'agency';

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
            $userRs = AgencyAccount::api()->add($paramU);

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
