<?php
class FavoritesModel extends Base_Model_Abstract
{
    protected $dbname = 'itourism';
    protected $tblname = 'favorites';
    protected $pkKey = 'id';
    //protected $preCacheKey = 'cache|favoritesModel|';
    protected $preCacheKey='';
    
    public function getTable() {
        return $this->tblname;
    }
}
