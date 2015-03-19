<?php

/**
 * Class LanguageModel
 */
class LanguageModel extends Base_Model_Abstract
{
    protected $dbname = 'itourism';
    protected $tblname = 'language_config';
    protected $pkKey = 'id';
    protected $preCacheKey = 'cache|LanguageModel|';
    
    public function getTable() {
        return $this->tblname;
    }
}
