<?php
use common\huilian\utils\Format;

?>
<div class="contentpanel">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title"><?= $organization['type'] == 'supply' ? '供应商' : '分销商' ?>查询</h4>
		</div>
		<div class="panel-body">
			<!-- BEGIN FORM-->
			<form action="#" class="form-horizontal">
				<div class="form-body">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label col-md-3">公司名称：</label>
								<div class="col-md-9">
									<input type="text" class="form-control" placeholder="Chee Kin" readonly value="<?= $organization['name'] ?>">
								</div>
							</div>
						</div>
						<!--/span-->
						<div class="col-md-6">
							<div class="form-group has-error">
								<label class="control-label col-md-3">Last Name</label>
								<div class="col-md-9">
									<div class="select2-container select2me form-control" id="s2id_autogen1">
										<a href="javascript:void(0)" class="select2-choice" tabindex="-1"> <span class="select2-chosen" id="select2-chosen-2">Abc</span><abbr class="select2-search-choice-close"></abbr> <span class="select2-arrow" role="presentation"><b role="presentation"></b></span></a><label for="s2id_autogen2" class="select2-offscreen"></label><input class="select2-focusser select2-offscreen" type="text" aria-haspopup="true" role="button" aria-labelledby="select2-chosen-2" id="s2id_autogen2">
										<div class="select2-drop select2-display-none select2-with-searchbox">
											<div class="select2-search">
												<label for="s2id_autogen2_search" class="select2-offscreen"></label> <input type="text" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" class="select2-input" role="combobox" aria-expanded="true" aria-autocomplete="list" aria-owns="select2-results-2" id="s2id_autogen2_search" placeholder="">
											</div>
											<ul class="select2-results" role="listbox" id="select2-results-2">
											</ul>
										</div>
									</div>
									<select name="foo" class="select2me form-control select2-offscreen" tabindex="-1" title="">
										<option value="1">Abc</option>
										<option value="1">Abc</option>
										<option value="1">This is a really long value that breaks the fluid design for a select2</option>
									</select> <span class="help-block"> This field has error. </span>
								</div>
							</div>
						</div>
						<!--/span-->
					</div>
					<!--/row-->
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label col-md-3">Gender</label>
								<div class="col-md-9">
									<select class="form-control">
										<option value="">Male</option>
										<option value="">Female</option>
									</select> <span class="help-block"> Select your gender. </span>
								</div>
							</div>
						</div>
						<!--/span-->
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label col-md-3">Date of Birth</label>
								<div class="col-md-9">
									<input type="text" class="form-control" placeholder="dd/mm/yyyy">
								</div>
							</div>
						</div>
						<!--/span-->
					</div>
					<!--/row-->
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label col-md-3">Category</label>
								<div class="col-md-9">
									<div class="select2-container select2_category form-control" id="s2id_autogen13">
										<a href="javascript:void(0)" class="select2-choice" tabindex="-1"> <span class="select2-chosen" id="select2-chosen-14">Category 1</span><abbr class="select2-search-choice-close"></abbr> <span class="select2-arrow" role="presentation"><b role="presentation"></b></span></a><label for="s2id_autogen14" class="select2-offscreen"></label><input class="select2-focusser select2-offscreen" type="text" aria-haspopup="true" role="button" aria-labelledby="select2-chosen-14" id="s2id_autogen14"
											tabindex="1">
										<div class="select2-drop select2-display-none select2-with-searchbox">
											<div class="select2-search">
												<label for="s2id_autogen14_search" class="select2-offscreen"></label> <input type="text" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" class="select2-input" role="combobox" aria-expanded="true" aria-autocomplete="list" aria-owns="select2-results-14" id="s2id_autogen14_search" placeholder="">
											</div>
											<ul class="select2-results" role="listbox" id="select2-results-14">
											</ul>
										</div>
									</div>
									<select class="select2_category form-control select2-offscreen" data-placeholder="Choose a Category" tabindex="-1" title="">
										<option value="Category 1">Category 1</option>
										<option value="Category 2">Category 2</option>
										<option value="Category 3">Category 5</option>
										<option value="Category 4">Category 4</option>
									</select>
								</div>
							</div>
						</div>
						<!--/span-->
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label col-md-3">Membership</label>
								<div class="col-md-9">
									<div class="radio-list">
										<label class="radio-inline">
											<div class="radio">
												<span><input type="radio" name="optionsRadios2" value="option1"></span>
											</div> Free
										</label> <label class="radio-inline">
											<div class="radio">
												<span class="checked"><input type="radio" name="optionsRadios2" value="option2" checked=""></span>
											</div> Professional
										</label>
									</div>
								</div>
							</div>
						</div>
						<!--/span-->
					</div>
				</div>
			</form>
			<!-- END FORM-->

		</div>
	</div>
</div>