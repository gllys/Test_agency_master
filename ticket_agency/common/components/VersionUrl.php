<?php

class VersionUrl extends CApplicationComponent {

    /**
     * 根据参数生成版本号的url
     *
     * @params
     * @$v:版本号
     * @$url:链接
     */
    public $url = '';
    public $v = 1;
    public $openDirRule = true;
    public $dirRules = array('js' => 'js_min', 'css' => 'css_min');

    public function changeUrl($url) {
        if ($this->openDirRule) {
            foreach($this->dirRules as $key => $item) {
                $url = str_replace('/' . $key . '/', '/' . $item . '/', $url);
            }
        }
        return $this->url.$url . '?' . $this->v;
    }

}
