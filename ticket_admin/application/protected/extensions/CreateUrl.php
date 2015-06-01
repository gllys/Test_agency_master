<?php

class CreateUrl {

    public $baseUrl = '/';
    private static $_model = null;

    public static function model($className = __CLASS__) {

        if (self::$_model == null) {
            self::$_model = new $className();
        }
        return self::$_model;
    }

    public function createMenu() {
        $count = count($this->titles);
        $html = '';
        for ($i = 0; $i < $count; $i++) {
            $list = $this->createList($i);
            if (empty($list))
                continue;

            $title = $this->createTitle($i);

            if (empty($title))
                continue;
            $html .= sprintf('<li class="parent">%s<ul class="children">%s</ul></li>', $title, $list);
        }

        return $html;
    }

    public function createHeader() {
        $count = count($this->titles);
        $html = '';

        for ($i = 0; $i < $count; $i++) {
            $list = $this->createList($i);
            if (empty($list))
                continue;
            $html .= sprintf('<li id="nav_%s"><div class="m-t-small">%s</div></li>', $i, $this->createTitle($i));
        }

        return $html;
    }

    public function createBody($nav) {
        $index = $this->getIndex($nav);
        return $this->createList($index);
    }

    /**
     * 创建URL
     * @param String $accessString 用于验证用户是否具有此链接权限的字符串, "*" 表示不验证权限
     * @param Array $params 根据此参数 创建HTML 节点
     * 	example: 创建 <li class="current"><a href="http://cdns.uuzuonline.com" class="url">CDNS管理系统</a></li>
     * 	$params = array(
     * array('name'=>'li', 'params'=>array('class'=>'current')),
     * array('name'=>'li', 'params'=>array('class'=>'url', 'href'=>'http://cdns.uuzuonline.com'), 'content'=>'CDNS管理系统'),
     * );
     *
     * return String 返回生产好的HTML， 没有权限时  返回null
     */
    public function create($accessString, $params = array()) {
        if ($accessString != '*') {
            $auth = Yii::app()->user->checkAccess($accessString);
            if (!$auth)
                return null;
        }

        $html = '';
        foreach ($params as $node) {
            if (!isset($node['name']))
                continue;

            $html .= '<' . $node['name'];
            if (isset($node['params']) && is_array($node['params'])) {
                foreach ($node['params'] as $key => $value) {
                    $html .= ' ' . $key . '="' . $value . '"';
                }
            }
            $html .= '>';
            if (isset($node['content']))
                $html .= $node['content'];
        }

        while (count($params)) {
            $node = array_pop($params);
            if (!isset($node['name']))
                continue;
            $html .= '</' . $node['name'] . '>';
        }

        return $html;
    }

    /**
     * @param String $accessString 用于验证用户是否具有此链接权限的字符串, "*" 表示不验证权限
     * @param String $string
     */
    public function authAndShow($accessString, $string) {
        $auth = Yii::app()->user->checkAccess($accessString);
        if (!$auth)
            return null;

        echo $string;
    }

    // 菜单标题
    public function createTitle($index) {
        if (!isset($this->titles[$index]))
            return null;
        if ($this->titles[$index]['content'] == '') {
            return '';
        } else {
            $html = '<a href="javascript:void(0);" id="drop_' . $index . '"><i class="' . $this->titles[$index]['params']['class'] . '"></i> <span>';
        }

        $html .= sprintf('%s</span></a>', $this->titles[$index]['content']);

        return $html;
    }

    // 菜单列表
    public function createList($index) {
        if (!isset($this->lists[$index]))
            return null;

        $html = '';
        foreach ($this->lists[$index] as $item) {
            if (!$this->checkAuth($item))
                continue;
            $html .= '<li><a';
            foreach ($item['params'] as $key => $value) {
                if ($key == 'href') {
                    $html .= sprintf(' %s="/site/switch/#%s"', $key, $value);
                } else {
                    $html .= sprintf(' %s="%s"', $key, $value);
                }
            }
            $html .= sprintf('>%s</a></li>', $item['content']);
        }
        return $html;
    }

    // 菜单列表
    public function getListOne($index) {
        if (!isset($this->lists[$index]))
            return null;

        $html = '';
        foreach ($this->lists[$index] as $item) {
            if (!$this->checkAuth($item))
                continue;

            return $item;
        }
        return $this->lists[0];
    }

    //跳转第一个有权限的菜单做为首页
    public function getRedirectOne() {
        $count = count($this->titles);
        for ($i = 0; $i < $count; $i++) {
            foreach ($this->lists[$i] as $item) {
                if (!$this->checkAuth($item))
                    continue;
                return $item;
            }
        }
        return $this->lists[0];
    }

    // 检测权限，在 auth 字段为空时，根据 URL 进行权限验证
    private static $_singles = array();

    private function checkAuth($item) {
        //return true;
        #得到用户信息
        $user = self::$_singles['users'] = isset(self::$_singles['users']) ? self::$_singles['users'] : #单例，可以不看
            Users::model()->findByAttributes(array('account' => Yii::app()->user->id));

        #如果是超级管理员，有所有权限
        if ($user['is_super']) {
            return true;
        }

        #得到用户较色
        $userRole = self::$_singles['userRole'] = isset(self::$_singles['userRole']) ? self::$_singles['userRole'] : #单例，可以不看
            RoleUser::model()->findByAttributes(array('uid' => $user['id']));
        if (!$userRole) {
            return false;
        }


        #得到用户权限
        $role = self::$_singles['role'] = isset(self::$_singles['role']) ? self::$_singles['role'] : #单例，可以不看
            Role::model()->findByPk($userRole['role_id']);
        if (!$role || !$role['status']) {
            return false;
        }
        $permissions = json_decode($role['permissions'], true);
        if ($this->inArray($item['params']['href'], $permissions)) {
            return true;
        }
        return false;
    }

    public $TitleIndexs = array(
        'index' => 0,
        'scenic' => 1,
        'agency' => 2,
        'ticket' => 3,
        'finance' => 4,
        'system' => 5,
    );

    public function getIndex($nav) {
        return $index = isset($this->TitleIndexs[$nav]) ? $this->TitleIndexs[$nav] : 0;
    }

    public $titles = array(
        array('params' => array('class' => 'fa fa-fw fa-home'), 'content' => ''),
        array('params' => array('class' => 'fa fa-picture-o'), 'content' => '景区管理'),
        array('params' => array('class' => 'fa fa-code-fork'), 'content' => '分销系统'),
        array('params' => array('class' => 'fa fa-ticket'), 'content' => '电子票务'),
        array('params' => array('class' => 'fa fa-credit-card'), 'content' => '机构管理'),
        array('params' => array('class' => 'fa fa-credit-card'), 'content' => '财务管理'),
        array('params' => array('class' => 'fa fa-exchange'), 'content' => '渠道对接'),
        array('params' => array('class' => 'fa fa-cogs'), 'content' => '系统管理'),
        array('params' => array('class' => 'fa fa-picture-o'), 'content' => '消息管理'),
    );
    public $lists = array(
        array(
            array('params' => array('href' => '/dashboard'), 'content' => '工作台'),
        ),
        array(
            array('auth' => '', 'paramIcos' => array("class" => "fa fa-user"), 'params' => array('class'=>'part','href' => '/scenic/scenic/'), 'content' => '景区列表'),
            array('auth' => '', 'paramIcos' => array("class" => "fa fa-user"), 'params' => array('class'=>'part','href' => '/scenic/addequip/'), 'content' => '添加设备'),
            array('auth' => '', 'paramIcos' => array("class" => "fa fa-user"), 'params' => array('class'=>'part','href' => '/scenic/managequip/'), 'content' => '设备管理'),
        ),
        array(
            array('params' => array('class'=>'part','href' => '/agency/orders/'), 'content' => '订单管理'),
            array('params' => array('class'=>'part','href' => '/agency/sms/'), 'content' => '短信日志'),
            array('params' => array('class'=>'part','href' => '/agency/product/'), 'content' => '供应商产品'),
            array('params' => array('class'=>'part','href' => '/supply/sales/'), 'content' => '供应商销量'),
        	array('params' => array('class'=>'part','href' => '/agency/verification/'), 'content' => '验票记录'),
        ),
        array(
            array('params' => array('class'=>'part','href' => '/ticket/ticket/'), 'content' => '景区门票'),
            array('params' => array('class'=>'part','href' => '/ticket/stats/'), 'content' => '景区统计'),
            array('params' => array('class'=>'part','href' => '/ticket/template/'), 'content' => '打印模板')
        ),
        array(
            array('params' => array('class'=>'part','href' => '/org/supply/'), 'content' => '供应商管理'),
            array('params' => array('class'=>'part','href' => '/org/agency/'), 'content' => '分销商管理'),
            array('params' => array('class'=>'part','href' => '/org/ownagency/'), 'content' => '分销商归属'),
            array('params' => array('class'=>'part','href' => '/org/regsupply/'), 'content' => '注册供应商'),
            array('params' => array('class'=>'part','href' => '/org/regagency/'), 'content' => '注册分销商'),
        ),
        array(
            array('params' => array('class'=>'part','href' => '/finance/payrate/'), 'content' => '费率设置'),
            array('params' => array('class'=>'part','href' => '/finance/payable/'), 'content' => '应付账款'),
            array('params' => array('class'=>'part','href' => '/finance/receivable/'), 'content' => '供应商应收账款查询'),
            array('params' => array('class'=>'part','href' => '/finance/config/'), 'content' => '结算配置'),
            array('params' => array('class'=>'part','href' => '/finance/report/'), 'content' => '交易报表'),
            array('params' => array('class'=>'part','href' => '/finance/rebate/'), 'content' => '充值优惠'),
            array('params' => array('class'=>'part','href' => '/finance/fund/'), 'content' => '资产管理'),
        ),
        array(
            array('params' => array('class'=>'part','href' => '/channel/account/'), 'content' => '渠道对接'),
        	array('params' => array('class'=>'part','href' => '/channel/config/'), 'content' => '属性配置'),
        ),
        array(
            array('params' => array('class'=>'part','href' => '/system/staff/'), 'content' => '员工管理'),
            array('params' => array('class'=>'part','href' => '/system/role/'), 'content' => '角色权限'),
            array('params' => array('class'=>'part','href' => '/system/account/'), 'content' => '密码修改'),
            array('params' => array('class'=>'part','href' => '/system/homerec/'), 'content' => '首页推荐'),
            array('params' => array('class'=>'part','href' => '/system/notice/'), 'content' => '公告管理'),
        ),
        array(
            array('params' => array('class'=>'part','href' => '/message/notice/'), 'content' => '用户提醒'),
        ),
    );

    public function checkAccess($access) {
        if (!is_array($access)) {
            $access = array($access);
        }
        foreach ($access as $_access) {
            $item = array();
            $item['params']['href'] = $_access;
            if ($this->checkAuth($item)) {
                return true;
            }
        }
        return false;
    }

    public function getChildNav($controllerId) {
        $setting = array(
            '/finance/detail/' => '/finance/bill/',
            '/supply/detail/' => '/supply/sales/',
        );


        if (isset($setting[$controllerId])) {
            return $setting[$controllerId];
        }
        return null;
    }

    //自己定义控制器对比，去掉大小定，和路径问题
    private function inArray($b, $a) {
        foreach ($a as $value) {
            if (trim(strtolower($value), '/') == trim(strtolower($b), '/')) {
                return true;
                break;
            }
        }
        return false;
    }

}
