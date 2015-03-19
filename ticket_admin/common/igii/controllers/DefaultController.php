<?php

class DefaultController extends CController {

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
        if (Yii::app()->request->isPostRequest) {
            preg_match('/dbname=([a-z0-9_-]*)/i', Yii::app()->db->connectionString, $dbInfo);
            $db = $dbInfo[1];
            $table = $_POST['model']::model()->tableName();
            $sql = "SELECT column_name,column_comment,column_key,column_default
                    FROM information_schema.columns
                    WHERE TABLE_SCHEMA = '{$db}'AND table_name =  '{$table}'";
            $fields = Yii::app()->db->createCommand($sql)->queryAll();

            $sql = "SELECT table_comment 
                    FROM information_schema.TABLES
                    WHERE TABLE_SCHEMA = '{$db}'AND table_name =  '{$table}'";
            $rs = Yii::app()->db->createCommand($sql)->queryRow();
            $tableName = $rs['table_comment'];
//            echo "<pre>";
//            echo $tableName;
//            print_r($fields);
//            Yii::app()->end();
        }
        $this->render('index', compact('tableName', 'fields'));
    }

    public function actionCreate() {
        header('Content-type: text/html; charset=UTF-8');
        #得到表信息
        preg_match('/dbname=([a-z0-9_-]*)/i', Yii::app()->db->connectionString, $dbInfo);
        $db = $dbInfo[1];
        $table = $_POST['model']::model()->tableName();
        $sql = "SELECT column_name,column_comment,column_key,column_default,is_nullable
                    FROM information_schema.columns
                    WHERE TABLE_SCHEMA = '{$db}'AND table_name =  '{$table}'";
        $_POST['fields'] = Yii::app()->db->createCommand($sql)->queryAll();

        $sql = "SELECT table_comment 
                    FROM information_schema.TABLES
                    WHERE TABLE_SCHEMA = '{$db}'AND table_name =  '{$table}'";
        $rs = Yii::app()->db->createCommand($sql)->queryRow();
        $_POST['tableName'] = $rs['table_comment'];
        print_r($_POST);
        $templatePath = 'common.igii.generators.crud.templates.default';
        $code = new CrudCode();
        $code->controller = $_POST['controller_id'];
        $code->baseControllerClass = $_POST['base_controller'];
        $_POST['code'] = $code;

        $_POST['controller_id'] = strtolower($_POST['controller_id']);
        #生成controller
        $_controller = $this->renderPartial($templatePath . '.controller', $_POST, true);
        @mkdir(dirname($code->getControllerFile()), 0755, true);
        file_put_contents($code->getControllerFile(), $_controller);

        #生成index
        $_index = $this->renderPartial($templatePath . '.index', $_POST, true);
        @mkdir($code->getViewPath(), 0755, true);
        file_put_contents($code->getViewPath() . '/index.php', $_index);

        #生成create
        $_index = $this->renderPartial($templatePath . '.create', $_POST, true);
        @mkdir($code->getViewPath(), 0755, true);
        file_put_contents($code->getViewPath() . '/create.php', $_index);

        #生成update
        $_index = $this->renderPartial($templatePath . '.update', $_POST, true);
        @mkdir($code->getViewPath(), 0755, true);
        file_put_contents($code->getViewPath() . '/update.php', $_index);
        $this->redirect('/' . $_POST['controller_id']);
    }

    public function getName($tableName, $columnComment) {
        $name = $columnComment == '' ? $tableName . 'Id' : mb_substr($columnComment, 0, mb_strpos($columnComment, ':'));
        if (!$name)
            $name = $columnComment;
        return $name;
    }

    //模糊查询
    public function searchLike($columnComment) {
        return !strpos($columnComment, ':') || mb_substr($columnComment, mb_strpos($columnComment, ':') + 1) == 'log';
    }

    //redio
    public function searchRadio($columnComment) {
        return strpos($columnComment, ':') && strpos($columnComment, '{') && strpos($columnComment, '}');
    }

    //下拉查询
    public function searchSelect($columnComment) {
        return strpos($columnComment, ':') && mb_substr($columnComment, mb_strpos($columnComment, ':') + 1) != 'time' && mb_substr($columnComment, mb_strpos($columnComment, ':') + 1) != 'log'&& mb_substr($columnComment, mb_strpos($columnComment, ':') + 1) != 'file';
    }

    //时间查询
    public function searchTime($columnComment) {
        return mb_substr($columnComment, mb_strpos($columnComment, ':') + 1) == 'time';
    }

    //日志
    public function searchLog($columnComment) {
        return mb_substr($columnComment, mb_strpos($columnComment, ':') + 1) == 'log';
    }
    
    //图片
    public function searchFile($columnComment) {
        return mb_substr($columnComment, mb_strpos($columnComment, ':') + 1) == 'file';
    }
    
    public function getTable($columnComment) {
        $str = str_replace(array('{', '}'), '', mb_substr($columnComment, mb_strpos($columnComment, ':') + 1));
        if ($this->searchSelect($columnComment)) {
            $str = $this->underline2camel($str);
        }
        return $str;
    }

    public function underline2camel($s) {
        return preg_replace('/(?:^|_)([a-z])/e', "strtoupper('\\1')", $s);
    }

}
