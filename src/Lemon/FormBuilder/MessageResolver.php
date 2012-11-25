<?php
namespace Lemon\FormBuilder;

class MessageResolver implements ResolverInterface {

	protected $messages = array(
		'Lemon\FormBuilder\Validator\Email' 		=> 'Please enter a valid email address.',
		'Lemon\FormBuilder\Validator\Maxlength' 	=> 'Please enter no more than %s characters.',
		'Lemon\FormBuilder\Validator\Minlength' 	=> 'Please enter at least %s characters.',
		'Lemon\FormBuilder\Validator\Required' 		=> 'This field is required.',
		'Lemon\FormBuilder\Validator\Pattern' 		=> 'This field does not match the prescribed pattern.',
	);

	public function resolve($id) {
		if (is_array($id)) {
			return vsprintf($this->messages[$id['class']], $id['params']);
		} elseif (isset($this->messages[$id])) {
			return $this->messages[$id];
		}
	}
}
