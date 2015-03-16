<?php

class SettingController extends CController {

    public $layout = '/layouts/column1';

    /**
     * This is the default 'index' action that is invoked
     * 
     * when an action is not explicitly requested by users.
     */
    public function actionIndex() {
        header('Content-Type:text/html; charset=utf-8');
        $fields = array();
        $tableName = '';
        $dir = 'application.models';
        $arr = array();
        if (Yii::app()->request->isPostRequest) {
            preg_match('/dbname=([a-z0-9_-]*)/i', Yii::app()->db->connectionString, $dbInfo);
            $db = $dbInfo[1];
            $tableName = $table = $_POST['tableName'];
            $dir = $_POST['dir'];
            $sql = "SELECT column_name
                    FROM information_schema.columns
                    WHERE TABLE_SCHEMA = '{$db}'AND table_name =  '{$table}'";
            $fields = Yii::app()->db->createCommand($sql)->queryColumn();

            $sql = "SELECT * FROM {$table}";
            $rs = Yii::app()->db->createCommand($sql)->queryAll();
            $rs = $this->arrayByKeys($rs, 'id');
            $arr = var_export($rs, true);

            header('Content-type: text/html; charset=UTF-8');
            $templatePath = 'common.igii.generators.setting.templates.default';
            #生成model
            $modelName = $this->underline2camel($tableName) ;
            $_index = $this->renderPartial($templatePath . '.model', compact('arr','modelName'), true);
            @mkdir(YiiBase::getPathOfAlias($dir), 0755, true);
            file_put_contents(YiiBase::getPathOfAlias($dir) . '/'.$modelName.'.php', $_index);

//            echo "<pre>";
//            echo $tableName;
//            print_r($fields);
//            Yii::app()->end();
        }
        $this->render('index', compact('tableName', 'dir', 'arr'));
    }

    public function actionCreate() {
        header('Content-type: text/html; charset=UTF-8');
    }

    public function arrayByKeys($model, $key) {
        $_model = array();
        foreach ($model as $val) {
            $_model[$val[$key]] = $val;
        }
        return $_model;
    }

    public function underline2camel($s) {
        return preg_replace('/(?:^|_)([a-z])/e', "strtoupper('\\1')", $s);
    }

}
