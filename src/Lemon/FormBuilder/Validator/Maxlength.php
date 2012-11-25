<?php
namespace Lemon\FormBuilder\Validator;

use Lemon\FormBuilder\Validator;

class Maxlength implements Validator {

	protected $maxlength;

	public function __construct($maxlength) {
		$this->maxlength = $maxlength;
	}

	public function validate($value) {
		return strlen(trim($value)) <= $this->maxlength;
	}

	public function getParams() {
		return array($this->maxlength);
	}
}
