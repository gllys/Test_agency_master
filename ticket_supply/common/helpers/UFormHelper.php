<?php
class UFormHelper{
	public static function hint($form, $model, $attribute){
		$error=$model->getError($attribute);
		if($error!=''){
			return $form->error($model,$attribute);
		}elseif(method_exists($model,'attributeHints')){
			$hints = $model->attributeHints();
			return isset($hints[$attribute])?"<span class='hint'>".$hints[$attribute].'</span>':'';
		}
	}
} 