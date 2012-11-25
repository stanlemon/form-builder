<?php
namespace Lemon\FormBuilder;

class ValidatorResolver implements ResolverInterface {

	public function resolve($id) {
		$className = "Lemon\\FormBuilder\\Validator\\" . ucfirst(strtolower($id));
		return $className;
	}
}

