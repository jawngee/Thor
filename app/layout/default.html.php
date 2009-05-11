<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
		<?if($layout->description!=""):?><meta name="description" content="{{$layout->description}}" /><?endif; ?>
		<title>{{$layout->title}}</title>
		
		<layout:includes />
		<layout:styles />
		<layout:blocks />
	</head>

	<body>
		<layout:content id="main" />

		<? Profiler::Display() ?>
	</body>
</html>
