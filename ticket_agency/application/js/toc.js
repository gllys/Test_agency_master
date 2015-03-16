var Toc = {
	tocNode: "#toc",
	toc:null,
	menuNode: "#toc-list",
	menu:null,
	items: [],
	min: 0,
	count: 0,
    createToc: function() { 
    	var _this = this;
    	_this.toc = $(_this.tocNode);
    	_this.menu = $(_this.menuNode);
        _this.toc.nextAll().each(function(){
            switch(this.tagName) {
                case 'H1':
                case 'H2':
                case 'H3':
                case 'H4':
                case 'H5':
                    var level = parseInt(this.tagName[1]);
                    if (_this.min == 0 || _this.min > level) {
                        _this.min = level;
                    }
                    _this.items.push(new Array(level, ++_this.count, this.innerHTML));
                    $(this).before('<a name="maodian'+_this.count+'"></a>');
                    break;
                case 'TABLE':
                	$(this).attr('cellpadding', 0);
                	$(this).attr('cellspacing', 1);
                	break;
            }
        });
        for(var i=0; i<_this.items.length; i++) {
        	var item = _this.items[i];
            _this.menu.append('<li><a style="padding-left:'+((item[0]-_this.min)*25)+'px" href="#maodian'+item[1]+'">'+item[2]+'</a></li>');
        }
	},
}; 
$(function(){
    Toc.createToc();
});