<?php
/**
 * Super simple autoloader to find our classes
 */
function __autoload($class) {
	$file = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 
		str_replace("\\", DIRECTORY_SEPARATOR, $class) . '.php';
	
	if (file_exists($file)) {
		return include $file;
	}
}
?><!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width">
		<link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.2.1/css/bootstrap-combined.min.css" rel="stylesheet">
    </head>
    <body>
		<div class="container">
			<h1>Lemon\FormBuilder</h1>
			<p>This project's purpose is to demonstrate server side form validation using client side techniques. Specifically, this library 
			should enable a UI developer to design a form with HTML5 validation attributes and have PHP provide validation for those in the 
			event that the browser does not support HTML5 validation or in the event that the user has somehow decided to disable it.</p>
			<p><em>This library is a work in progress and only intended for research at this point.</em></p>
<?php
$form = <<<EOHTML
<div>
	<form class="form-horizontal" method="post" action="?action=submit">
		<fieldset>
			<legend>My Form</legend>

			<div class="control-group">
				<label class="control-label" for="date">Date</label>
				<div class="controls">
					<input type="text" name="date" id="date" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" placeholder="YYYY-MM-DD" />
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for="reason">Reason</label>
				<div class="controls">
					<select name="reason" id="reason">
						<option value="personal">Personal</option>
						<option value="business">Business</option>
						<option value="pleasure">Pleasure</option>
					</select>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for="name">Name</label>
				<div class="controls">
					<input type="text" name="name" id="name" required maxlength="50" minlength="4" placeholder="Name">
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for="email">Email</label>
				<div class="controls">
					<input type="email" name="email" id="email" required="required" placeholder="Email">
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for="message">Message</label>
				<div class="controls">
					<textarea name="message" id="message" required="required" placeholder="Enter your message here"></textarea>
				</div>
			</div>

			<button type="submit" class="btn btn-primary">Submit</button>
		</fieldset>
	</form>
</div>
EOHTML;

$start = microtime(true);

$formBuilder = new Lemon\FormBuilder($form);
$formBuilder->setErrorPlacement(function($element){
	$element->getDom()->parentNode->parentNode->setAttribute('class',
		$element->getDom()->parentNode->parentNode->getAttribute('class') . ' error'
	);
});
$formBuilder->build();

/**
print "<pre>";
foreach ($formBuilder->getElements() as $element) {
	print $element->getName() . PHP_EOL;
	foreach ($element->getValidators() as $validator) {
		print "  " . get_class($validator) . PHP_EOL;
	}
	print PHP_EOL;
}
print "</pre>";
**/

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$formBuilder->process($_POST);

	/**
	print "<pre>";
	foreach ($formBuilder->getInvalid() as $element) {
		print $element->getName() . PHP_EOL;
		print_r($element->getErrors());
	}
	print "</pre>";
	**/
}

print $formBuilder->render();

print '<hr /><p>Execution time: ' . (microtime(true) - $start) . '</p><br />';

?>
		</div>
		<script src="//code.jquery.com/jquery.min.js"></script>
		<script src="//ajax.aspnetcdn.com/ajax/jquery.validate/1.10.0/jquery.validate.js"></script>
		<script src="//netdna.bootstrapcdn.com/twitter-bootstrap/2.2.1/js/bootstrap.min.js"></script>
		<script type="text/javascript">
		// If console logging is not defined, set it up so we don't create errors
		if (typeof console == "undefined" || typeof console.log == "undefined") {
			var console = { log: function() {} };
		}

		// Allows us to gracefully log the results of any selector at any time
		$.fn.log = function(){
			console.log(this);
			return this;
		};

		/**
		$(function(){
			$('form').each(function(){
				$(this).validate({
					errorClass: "help-inline",
					validClass: "help-inline",
					errorElement: "span",
					highlight: function(element, errorClass, validClass) {
						$(element).parent().parent().addClass('error');
					},
					unhighlight: function(element, errorClass, validClass) {
						$(element).parent().parent().removeClass('error');
					},
					debug: true
				});
			});
		});
		**/
		</script>
    </body>
</html>
