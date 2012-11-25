<?php
namespace Lemon\FormBuilder;

interface Element {

	public function validate($value);

	public function setDom(\DOMElement $dom);

	public function getDom();

	public function getName();

	public function getValue();

	public function setValue($value);
}

