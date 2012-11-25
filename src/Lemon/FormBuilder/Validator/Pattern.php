<?php
namespace Lemon\FormBuilder\Validator;

use Lemon\FormBuilder\Validator;

class Pattern implements Validator {

	protected $pattern;

	public function __construct($pattern) {
		$this->pattern = $pattern;
	}

	public function validate($value) {
		return preg_match('/' . $this->pattern . '/', $value) > 0;
	}
}
