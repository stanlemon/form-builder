<?php
namespace Lemon\FormBuilder;

use Lemon\FormBuilder\ResolverInterface;

interface Element {

	public function validate($value);

	public function getErrors();

	public function setDom(\DOMElement $dom);

	public function getDom();

	public function getName();

	public function getValue();

	public function setValue($value);

	public function getValidatorResolver();

	public function setValidatorResolver(ResolverInterface $validatorResolver);
}

