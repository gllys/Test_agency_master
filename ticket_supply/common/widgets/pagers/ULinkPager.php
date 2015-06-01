<?php

/**
 * CLinkPager class file.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.yiiframework.com/
 * @copyright 2008-2013 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

/**
 * CLinkPager displays a list of hyperlinks that lead to different pages of target.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @package system.web.widgets.pagers
 * @since 1.0
 */
class ULinkPager extends CBasePager {

    const CSS_FIRST_PAGE = 'first';
    const CSS_LAST_PAGE = 'last';
    const CSS_PREVIOUS_PAGE = 'previous';
    const CSS_NEXT_PAGE = 'next';
    const CSS_BREACK_PAGE = 'break';
    const CSS_INTERNAL_PAGE = 'page';
    const CSS_HIDDEN_PAGE = 'hidden';
    const CSS_SELECTED_PAGE = 'selected';

    /**
     * @var string the CSS class for the first page button. Defaults to 'first'.
     * @since 1.1.11
     */
    public $firstPageCssClass = self::CSS_FIRST_PAGE;

    /**
     * @var string the CSS class for the last page button. Defaults to 'last'.
     * @since 1.1.11
     */
    public $lastPageCssClass = self::CSS_LAST_PAGE;

    /**
     * @var string the CSS class for the previous page button. Defaults to 'previous'.
     * @since 1.1.11
     */
    public $previousPageCssClass = self::CSS_PREVIOUS_PAGE;

    /**
     * @var string the CSS class for the next page button. Defaults to 'next'.
     * @since 1.1.11
     */
    public $nextPageCssClass = self::CSS_NEXT_PAGE;

    /**
     * @var string the CSS class for the internal page buttons. Defaults to 'page'.
     * @since 1.1.11
     */
    public $internalPageCssClass = self::CSS_INTERNAL_PAGE;

    /**
     * @var string the CSS class for the internal page buttons. Defaults to 'page'.
     * @since 1.1.11
     */
    public $breakPageCssClass = self::CSS_BREACK_PAGE;

    /**
     * @var string the CSS class for the hidden page buttons. Defaults to 'hidden'.
     * @since 1.1.11
     */
    public $hiddenPageCssClass = self::CSS_HIDDEN_PAGE;

    /**
     * @var string the CSS class for the selected page buttons. Defaults to 'selected'.
     * @since 1.1.11
     */
    public $selectedPageCssClass = self::CSS_SELECTED_PAGE;

    /**
     * @var integer maximum number of page buttons that can be displayed. Defaults to 10.
     */
    public $maxButtonCount = 10;

    /**
     * @var string the text label for the next page button. Defaults to 'Next &gt;'.
     */
    public $nextPageLabel;

    /**
     * @var string the text label for the previous page button. Defaults to '&lt; Previous'.
     */
    public $prevPageLabel;

    /**
     * @var string the text label for the first page button. Defaults to '&lt;&lt; First'.
     */
    public $firstPageLabel;

    /**
     * @var string the text label for the last page button. Defaults to 'Last &gt;&gt;'.
     */
    public $lastPageLabel;

    /**
     * @var string the text shown before page buttons. Defaults to 'Go to page: '.
     */
    public $header;

    /**
     * @var string the text shown after page buttons.
     */
    public $footer = '';

    /**
     * @var mixed the CSS file used for the widget. Defaults to null, meaning
     * using the default CSS file included together with the widget.
     * If false, no CSS file will be used. Otherwise, the specified CSS file
     * will be included when using this widget.
     */
    public $cssFile;

    /**
     * @var array HTML attributes for the pager container tag.
     */
    public $htmlOptions = array();

    /**
     * Initializes the pager by setting some default property values.
     */
    public function init() {
        if ($this->nextPageLabel === null)
            $this->nextPageLabel = Yii::t('yii', 'Next &gt;');
        if ($this->prevPageLabel === null)
            $this->prevPageLabel = Yii::t('yii', '&lt; Previous');
        if ($this->firstPageLabel === null)
            $this->firstPageLabel = Yii::t('yii', '&lt;&lt; First');
        if ($this->lastPageLabel === null)
            $this->lastPageLabel = Yii::t('yii', 'Last &gt;&gt;');
        //if ($this->header === null)
        //头部
        $this->setHeader();

        if (!isset($this->htmlOptions['id']))
            $this->htmlOptions['id'] = $this->getId();
        if (!isset($this->htmlOptions['class']))
            $this->htmlOptions['class'] = 'yiiPager';
    }

    public function setHeader() {
        $this->header = '<div class="col-xs-5">
            <div class="Tables_info" style="text-align: left;">每页有<b>' . $this->getPageSize() . '</b>条数据，共<b>' . $this->pages->getItemCount() . '</b>条数据</div>
        </div>';
    }

    /**
     * Executes the widget.
     * This overrides the parent implementation by displaying the generated page buttons.
     */
    public function run() {
        $this->registerClientScript();
        $buttons = $this->createPageButtons();

        echo '<div class="row">';
        echo $this->header;
        echo '<div class="col-xs-7"><div class="pagenumQu">';
        if (!empty($buttons)) {
            $request_url = !empty($_SERVER['SCRIPT_URI']) ? $_SERVER['SCRIPT_URI'] : (!empty($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $_SERVER['SCRIPT_NAME']);
            //点击“go”时，带get参数跳转
            $url = $buttons[count($buttons)-1];
            $url = substr($url,strpos($url,'href="')+strlen('href="'));
            $url = substr($url,0,strpos($url,'/page/'));
            $url = substr($url,0,strpos($url,'/mod/'));
            
            //如果ajax加载分页
            if(!empty($_GET['mod'])&&$_GET['mod']=='part'){
                $url = '/#'.$url ;
            }
            echo CHtml::tag('ul', $this->htmlOptions, implode("\n", $buttons));
//            $url = (strpos($request_url, '/page/') ? substr($request_url, 0, strpos($request_url, '/page/')) : $request_url);
            echo ' 跳转到<input id="go" class="form-control" type="text" value="">' .
                '<button id="goButton"  class="btn btn-primary btn-sm" type="button" onclick="window.location.href=\'' . $url .'/page/\'+(isNaN(parseInt($(\'#go\').val()))?\'\':parseInt($(\'#go\').val()))">GO</button>';
        }
        echo '</div></div>';
        echo '</div>';

        echo '<script language="javascript" type="text/javascript">
    $(function(){
        $(\'#go\').bind(\'keypress\',function(event){
            if(event.keyCode == "13")    
            {
                $(\'#goButton\').trigger(\'click\');
            }
        });
    });
</script>';
        //echo $this->footer;
    }

    /**
     * Creates the page buttons.
     * @return array a list of page buttons (in HTML code).
     */
    protected function createPageButtons() {
        if (($pageCount = $this->getPageCount()) <= 1)
            return array();

        list($beginPage, $endPage) = $this->getPageRange();
        $currentPage = $this->getCurrentPage(false); // currentPage is calculated in getPageRange()
        $buttons = array();

        // first page
        $buttons[] = $this->createPageButton($this->firstPageLabel, 0, $this->firstPageCssClass, $currentPage <= 0, false);

        // prev page
        if (($page = $currentPage - 1) < 0)
            $page = 0;
        $buttons[] = $this->createPageButton($this->prevPageLabel, $page, $this->previousPageCssClass, $currentPage <= 0, false);

        //断点
        $countPage = $this->getPageCount() - 1;

        if ($beginPage > 1) {
            $buttons[] = $this->createPageButton(1, 0, $this->internalPageCssClass, false, false);
            $buttons[] = $this->createBreakButton($countPage + 1, $countPage, $this->breakPageCssClass, false, false);
        } else if ($beginPage == 1) {
            $buttons[] = $this->createPageButton(1, 0, $this->internalPageCssClass, false, false);
        }

        // internal pages
        for ($i = $beginPage; $i <= $endPage; ++$i)
            $buttons[] = $this->createPageButton($i + 1, $i, $this->internalPageCssClass, false, $i == $currentPage);

        //断点
        $countPage = $this->getPageCount() - 1;
        if ($endPage < $countPage - 1) {
            $buttons[] = $this->createBreakButton($countPage + 1, $countPage, $this->breakPageCssClass, false, false);
            $buttons[] = $this->createPageButton($countPage + 1, $countPage, $this->internalPageCssClass, false, false);
        } else if ($endPage < $countPage) {
            $buttons[] = $this->createPageButton($countPage + 1, $countPage, $this->internalPageCssClass, false, false);
        }

        // next page
        if (($page = $currentPage + 1) >= $pageCount - 1)
            $page = $pageCount - 1;
        $buttons[] = $this->createPageButton($this->nextPageLabel, $page, $this->nextPageCssClass, $currentPage >= $pageCount - 1, false);

        // last page
        $buttons[] = $this->createPageButton($this->lastPageLabel, $pageCount - 1, $this->lastPageCssClass, $currentPage >= $pageCount - 1, false);

        return $buttons;
    }

    /**
     * Creates a page button.
     * You may override this method to customize the page buttons.
     * @param string $label the text label for the button
     * @param integer $page the page number
     * @param string $class the CSS class for the page button.
     * @param boolean $hidden whether this page button is visible
     * @param boolean $selected whether this page button is selected
     * @return string the generated button
     */
    protected function createPageButton($label, $page, $class, $hidden, $selected) {
        if ($hidden || $selected)
            $class.=' ' . ($hidden ? $this->hiddenPageCssClass : $this->selectedPageCssClass);
        return '<li class="' . $class . '">' . CHtml::link($label, $this->createPageUrl($page)) . '</li>';
    }

    //得到break
    protected function createBreakButton($label, $page, $class, $hidden, $selected) {
        return '<li class="' . $class . '">. . .</li>';
    }

    /**
     * @return array the begin and end pages that need to be displayed.
     */
    protected function getPageRange() {
        $currentPage = $this->getCurrentPage();
        $pageCount = $this->getPageCount();

        $beginPage = max(0, $currentPage - (int) ($this->maxButtonCount / 2));
        if (($endPage = $beginPage + $this->maxButtonCount - 1) >= $pageCount) {
            $endPage = $pageCount - 1;
            $beginPage = max(0, $endPage - $this->maxButtonCount + 1);
        }
        return array($beginPage, $endPage);
    }

    /**
     * Registers the needed client scripts (mainly CSS file).
     */
    public function registerClientScript() {
        if ($this->cssFile !== false)
            self::registerCssFile($this->cssFile);
    }

    /**
     * Registers the needed CSS file.
     * @param string $url the CSS URL. If null, a default CSS URL will be used.
     */
    public static function registerCssFile($url = null) {
        if ($url === null)
            $url = CHtml::asset(Yii::getPathOfAlias('system.web.widgets.pagers.pager') . '.css');
        Yii::app()->getClientScript()->registerCssFile($url);
    }

}
