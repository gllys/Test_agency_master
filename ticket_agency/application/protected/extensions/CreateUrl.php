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
      array('name'=>'li', 'params'=>array('class'=>'current')),
      array('name'=>'li', 'params'=>array('class'=>'url', 'href'=>'http://cdns.uuzuonline.com'), 'content'=>'CDNS管理系统'),
      );
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
            $html = '<a style="display:none" href="/site/header/index/' . $index . '/" id="drop' . $index . '">';
        } else {
            $html = '<a href="/site/header/index/' . $index . '/" id="drop' . $index . '">';
        }

        $html .= sprintf('%s</a>', $this->titles[$index]['content']);

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

            $html .= '<li class=""><a';
            foreach ($item['params'] as $key => $value) {
                $html .= sprintf(' %s="%s"', $key, $value);
            }
            $html .= '><i';
            foreach ($item['paramIcos'] as $key => $value) {
                $html .= sprintf(' %s="%s"', $key, $value);
            }
            $html .= sprintf('></i><span>%s</span></a></li>', $item['content']);
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
        'ticket' => 1,
        'order' => 2,
        'finance' => 3,
        'system' => 4,
    );

    public function getIndex($nav) {
        return $index = isset($this->TitleIndexs[$nav]) ? $this->TitleIndexs[$nav] : 0;
    }

    public $titles = array(
        array('params' => array('class' => 'fa fa-fw fa-home'), 'content' => ''),
        array('params' => array('class' => 'fa fa-fw fa-barcode'), 'content' => '门票'),
        array('params' => array('class' => 'fa fa-fw fa-th-list'), 'content' => '订单'),
        array('params' => array('class' => 'fa fa-fw fa-credit-card'), 'content' => '结算'),
        array('params' => array('class' => 'fa fa-fw fa-gear'), 'content' => '系统管理'),
    );
    public $lists = array(
        array(
          array('auth' => '', 'paramIcos' => array("class" => "fa fa-home"), 'params' => array('href' => '/dashboard'), 'content' => '工作台'),
          //array('auth' => '', 'paramIcos' => array("class" => "fa fa-envelope-o"), 'params' => array('href' => '/system/message/'), 'content' => '消息', 'right' => 'msg'),
        ),
        array(
            array('auth' => '', 'paramIcos' => array("class" => "fa fa-home"), 'params' => array('href' => '/ticket/sale/'), 'content' => '散客预定'),
            array('auth' => '', 'paramIcos' => array("class" => "fa fa-home"), 'params' => array('href' => '/ticket/group/'), 'content' => '团队预定'),
            array('auth' => '', 'paramIcos' => array("class" => "fa fa-home"), 'params' => array('href' => '/ticket/cart/'), 'content' => '购物车'),
            array('auth' => '', 'paramIcos' => array("class" => "fa fa-home"), 'params' => array('href' => '/ticket/favorites/'), 'content' => '我的收藏'),
        ),
        array(
            array('auth' => '', 'paramIcos' => array("class" => "fa fa-home"), 'params' => array('href' => '/order/history/'), 'content' => '订单管理'),
            //array('auth' => '', 'paramIcos' => array("class" => "fa fa-home"), 'params' => array('href' => '/order/renwu/'), 'content' => '任务单管理'),
            array('auth' => '', 'paramIcos' => array("class" => "fa fa-home"), 'params' => array('href' => '/order/refund/'), 'content' => '退票查询'),
        ),
        array(
            array('auth' => '', 'paramIcos' => array("class" => "fa fa-home"), 'params' => array('href' => '/finance/payment/'), 'content' => '应付账款'),
            array('auth' => '', 'paramIcos' => array("class" => "fa fa-home"), 'params' => array('href' => '/finance/platform/'), 'content' => '平台资产'),
            array('auth' => '', 'paramIcos' => array("class" => "fa fa-home"), 'params' => array('href' => '/finance/property/'), 'content' => '资产管理'),
            array('auth' => '', 'paramIcos' => array("class" => "fa fa-home"), 'params' => array('href' => '/finance/blotter/'), 'content' => '交易流水'),
            array('auth' => '', 'paramIcos' => array("class" => "fa fa-home"), 'params' => array('href' => '/finance/bankcard/'), 'content' => '我的银行卡'),
        ),
        array(
            array('auth' => '', 'paramIcos' => array("class" => "fa fa-user"), 'params' => array('href' => '/system/organization/'), 'content' => '用户信息'),
            array('auth' => '', 'paramIcos' => array("class" => "fa fa-group"), 'params' => array('href' => '/system/staff/'), 'content' => '员工管理'),
            array('auth' => '', 'paramIcos' => array("class" => "fa fa-edit"), 'params' => array('href' => '/system/role/'), 'content' => '角色权限'),
            array('auth' => '', 'paramIcos' => array("class" => "fa fa-envelope-o"), 'params' => array('href' => '/system/message/'), 'content' => '消息查看'),
            array('auth' => '', 'paramIcos' => array("class" => "fa fa-lock"), 'params' => array('href' => '/system/account/'), 'content' => '密码修改'),
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
            '/ticket/buy/' => array('/ticket/sale/', '/ticket/group/',),
            '/order/payments/' => '/order/history/',
            '/order/newdetail/' => '/order/renwu/',
            '/order/detail/'  => '/order/history/',
            '/finance/detail/' => '/finance/payment/',
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
