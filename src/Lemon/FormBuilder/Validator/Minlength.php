<?php
namespace Lemon\FormBuilder\Validator;

use Lemon\FormBuilder\Validator;

class Minlength implements Validator {

	protected $minlength;

	public function __construct($minlength) {
		$this->minlength = $minlength;
	}

	public function validate($value) {
		return strlen(trim($value)) >= $this->minlength;
	}

	public function getParams() {
		return array($this->minlength);
	}
}
