<?php
namespace Lemon\FormBuilder\Validator;

use Lemon\FormBuilder\Validator;

class Required implements Validator {

	public function __construct() {}

	public function validate($value) {
		$value = trim($value);
		return !empty($value);
	}
}
