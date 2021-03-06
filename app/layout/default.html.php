<?
	$loc=(isset($_SERVER['PATH_INFO'])) ? array_shift(explode('/',trim($_SERVER['PATH_INFO'],'/'))) : '';
?>

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
		<div id="container">
			<div id="header">
				<img src="/i/logo.png" />
				<a href="https://manage.slicehost.com" target="_blank">Slicehost.com</a>
			</div>
			<div id="nav">
				<ul>
					<li {{ ($loc=='') ? 'class="selected"' : '' }}><a href="/">Dashboard</a></li>
					<li {{ ($loc=='slices') ? 'class="selected"' : '' }}><a href="/slices">Clouds</a></li>
					<li {{ ($loc=='configs') ? 'class="selected"' : '' }}><a href="/configs">Configurations</a></li>
					<li {{ ($loc=='inventories') ? 'class="selected"' : '' }}><a href="/inventory">Inventory</a></li>
					<li {{ ($loc=='accounts') ? 'class="selected"' : '' }}><a href="/accounts">Accounts</a></li>
				</ul>
			</div>
			<layout:content id="main" wrap="div" use_id="true" />
		</div>
		
		<? if (!isset($_GET['noprofiler'])) Profiler::Display(); ?>
	</body>
</html>
