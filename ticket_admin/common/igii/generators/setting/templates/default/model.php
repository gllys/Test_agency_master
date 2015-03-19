<?php
/**
 * This is the template for generating a controller class file for CRUD feature.
 * The following variables are available in this template:
 * - $this: the CrudCode object
 */
?>
<?php echo "<?php\n"; ?>

class <?php echo $modelName; ?> <?php echo "\n"; ?>
{
    private $_data = <?php echo $arr ?>;
    /***** 单例*********/
    private static $_singleton = null;

    public static function model() {
        if (self::$_singleton) {
            return self::$_singleton;
        }
        return self::$_singleton = new self();
    }

    public function findAll() {
        return $this->_data;
    }

    public function findByPk($id) {
        return $this->_data[$id];
    }
}
