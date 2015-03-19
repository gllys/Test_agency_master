<?php

/* * ***
 * 模拟景区发送日志
 */

class CollectLogCommand extends CConsoleCommand {

    //默认所有景区每天的平均游客数
    private $_defaultScenicSetting = array('total_num' => 1000, 'overseas_num' => 100, 'income' => 150000);

    public function run($args) {
        $orgs = $this->getOrgs();
        $secCount = intval(count($orgs)/60) ; #一秒钟执行几个景区
        if($secCount<1){
            $orgSames = $orgs ; 
        }  else {
            $orgSames = array_rand($orgs,$secCount);#出现几个
        }
        
        #插入数据
        foreach ($orgSames as $_org) {
            $rs = ScenicSetting::model()->findByPk($_org['id']);
            $_scenicSetting = $rs ? $rs : $this->_defaultScenicSetting;

            
            //得到国家id
            $countryId = self::randProbability(
                             array(0 => $_scenicSetting['total_num'] - $_scenicSetting['overseas_num'],
                                1 => $_scenicSetting['overseas_num'])
            );

            //得到省id
            $provinceId = 0 ;
            if (!$countryId) {
                $provinceId = $this->getRandProvince();
            }
            
            $num = ceil(($_scenicSetting['total_num'] - $_scenicSetting['overseas_num'])/500) ;
            $price = $num * ceil($_scenicSetting['income']/$_scenicSetting['total_num']) ;
            $savaData = array(
                'organization_id'=>$_org['id'],
                'country_id'=>$countryId,
                'province_id'=>$provinceId,
                'order_id'=>0,
                'num'=>$num ,
                'price'=> $price,
                'dateline'=>time(),
            );
            
            $model = new CollectLog();
            $model->setAttributes($savaData);
            $model->isNewRecord = true;
            $model->save() ;
            print_r($model->errors);
            if($secCount<1){
               sleep(intval(60/count($orgs))) ;
            }
        }
    }

    //得到机构
    public function getOrgs() {
        $rs = Yii::app()->cache->get('organizations');
        if ($rs !== false) {
            return CJSON::decode($rs);
        }

        //得到所有机构
        $criteria = new CDbCriteria;
        $criteria->compare('type', 'landscape');
        $criteria->compare('status', 'normal');
        $criteria->compare('deleted_at', NULL);
        $orgs = Organizations::api()->findAll($criteria);
        Yii::app()->cache->set('organizations', CJSON::encode($orgs), 3600 * 24);
        return $orgs;
    }

    //得到省
    public function getProvinces() {
        $rs = Yii::app()->cache->get('provinces');
        if ($rs !== false) {
            return CJSON::decode($rs);
        }

        //得到所有机构
        $criteria = new CDbCriteria;
        $criteria->compare('level', 1);
        $provinces = Districts::model()->findAll($criteria);
        Yii::app()->cache->set('provinces', CJSON::encode($provinces), 3600 * 24);
        return $provinces;
    }

    /*     * ***
     * 随机得到一个省
     */

    public static $_provinces = null;
    //概率分配
    public static $provincesProbability = array(110000 => 50, //北京市
        120000 => 10, //天津市
        130000 => 1, //河北省
        140000 => 1, //山西省
        150000 => 1, //内蒙古自治区
        210000 => 1, //辽宁省
        220000 => 1, //吉林省
        230000 => 1, //黑龙江省
        310000 => 100, //上海市
        320000 => 5, //江苏省
        330000 => 4, //浙江省
        340000 => 1, //安徽省
        350000 => 3, //福建省
        360000 => 3, //江西省
        370000 => 3, //山东省
        410000 => 3, //河南省
        420000 => 3, //湖北省
        430000 => 3, //湖南省
        440000 => 5, //广东省
        450000 => 1, //广西壮族自治区
        460000 => 1, //海南省
        500000 => 1, //重庆市
        510000 => 1, //四川省
        520000 => 1, //贵州省
        530000 => 1, //云南省
        540000 => 1, //西藏自治区
        610000 => 1, //陕西省
        620000 => 1, //甘肃省
        630000 => 1, //青海省
        640000 => 0, //宁夏回族自治区
        650000 => 0, //新疆维吾尔自治区
        710000 => 0, //台湾省
        810000 => 0, //香港特别行政区
        820000 => 0, //澳门特别行政区
    );

    public function getRandProvince() {
//        if (!self::$_provinces) {
//            self::$_provinces = $this->getProvinces();
//        }
        return self::randProbability(self::$provincesProbability);
    }

    public static function randProbability($array) {
        $nextSum = 0;
        $sum = array_sum($array);
        $rand = mt_rand(1, $sum);
        foreach ($array as $key => $val) {
            $nextSum += $val;
            if ($rand <= $nextSum) {
                return $key;
            }
        }
    }

}
