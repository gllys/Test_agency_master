jQuery(document).ready(function() {
        //$('#repass-form').validationEngine();
        $('input[name="bank_type"]').click(function(){
          if($(this).val()==1){
            $('.change').show()
            $('.banks').hide()
          }else{
            $('.change').hide()
            $('.banks').show()
          }
          
        }); 
        $('input[name="bank_type"]:checked').trigger('click');
       
		$('#all-btn').click(function(){
			var obj = $(this).parents('table')
			if($(this).is(':checked')){
				obj.find('input').prop('checked', true)
				$(this).text('反选')
			}else{
				obj.find('input').prop('checked', false)
				$(this).text('全选')
			}
		});
        
        function changeDate(o){
                var obj = $('.form-date'),
                d = new Date(obj.val().replace('-','/')+'/10')
                d.setMonth(o == 0?d.getMonth() + 1:d.getMonth() - 1)
                d = d.getFullYear()+'-'+(d.getMonth()+1 < 10 ? '0'+(d.getMonth()+1):d.getMonth() + 1)
                obj.val(d)
            }
            
            $('.date-prev').click(function(){
                changeDate(1)
            })
            $('.date-next').click(function(){
                changeDate(0)
            })

				jQuery("#distributor-select, #select-multi").select2();
			   jQuery('.select2').select2({
					minimumResultsForSearch: -1
				});

                // Tags Input
                jQuery('#tags').tagsInput({width:'auto'});
                 
                // Textarea Autogrow
                jQuery('#autoResizeTA').autogrow();
                
                // Spinner
                var spinner = jQuery('#spinner').spinner();
                spinner.spinner('value', 0);
                
                // Form Toggles
                jQuery('.toggle').toggles({on: true});
                
                // Time Picker
                jQuery('#timepicker').timepicker({defaultTIme: false});
                jQuery('#timepicker2').timepicker({showMeridian: false});
                jQuery('#timepicker3').timepicker({minuteStep: 15});
                
                // Date Picker
                jQuery('.datepicker').datepicker();
                jQuery('#datepicker-inline').datepicker();
                jQuery('#datepicker-multiple').datepicker({
                    numberOfMonths: 3,
                    showButtonPanel: true
                });
                
                // Input Masks
                jQuery("#date").mask("99/99/9999");
                jQuery("#phone").mask("(999) 999-9999");
                jQuery("#ssn").mask("999-99-9999");
                
                function format(item) {
                    return '<i class="fa ' + ((item.element[0].getAttribute('rel') === undefined)?"":item.element[0].getAttribute('rel') ) + ' mr10"></i>' + item.text;
                }
                
                // This will empty first option in select to enable placeholder
                jQuery('select option:first-child').text('');
                
                jQuery("#select-templating").select2({
                    formatResult: format,
                    formatSelection: format,
                    escapeMarkup: function(m) { return m; }
                });
                
                // Color Picker
                if(jQuery('#colorpicker').length > 0) {
                    jQuery('#colorSelector').ColorPicker({
			onShow: function (colpkr) {
			    jQuery(colpkr).fadeIn(500);
                            return false;
			},
			onHide: function (colpkr) {
                            jQuery(colpkr).fadeOut(500);
                            return false;
			},
			onChange: function (hsb, hex, rgb) {
			    jQuery('#colorSelector span').css('backgroundColor', '#' + hex);
			    jQuery('#colorpicker').val('#'+hex);
			}
                    });
                }
  
                // Color Picker Flat Mode
                jQuery('#colorpickerholder').ColorPicker({
                    flat: true,
                    onChange: function (hsb, hex, rgb) {
			jQuery('#colorpicker3').val('#'+hex);
                    }
                });
                
                
            });