<?php
namespace Lemon\FormBuilder\Validator;

use Lemon\FormBuilder\Validator;

class Email implements Validator {

	public function __construct() {}

	public function validate($value) {
		return filter_var($value, FILTER_VALIDATE_EMAIL);
	}
}
