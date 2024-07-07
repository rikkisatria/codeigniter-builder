<!doctype html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="">
	<meta name="author" content="">
	<link rel="icon" href="">

	<!-- TITLE -->
	<?= $this->renderSection('title_page') ?>

	<!-- Bootstrap core CSS -->
	<link href="<?= site_url(); ?>assets/plugins/bootstrap-4.0.0/dist/css/bootstrap.min.css" rel="stylesheet">

	<!-- Custom styles for this template -->
	<?= $this->renderSection('css_ex') ?>
	<?= $this->renderSection('css_in') ?>
</head>

<body>

	<!-- app-Header -->
	<?= view_cell('\App\Libraries\Modular::header') ?>

	<!-- Begin page content -->
	<?= $this->renderSection('content') ?>

	<!-- app-Footer -->
	<?= view_cell('\App\Libraries\Modular::footer') ?>

	<!-- Bootstrap core JavaScript
    ================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->
	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
	<script>
		window.jQuery || document.write('<script src="<?= site_url(); ?>assets/plugins/bootstrap-4.0.0/assets/js/vendor/jquery-slim.min.js"><\/script>')
	</script>
	<script src="<?= site_url(); ?>assets/plugins/bootstrap-4.0.0/assets/js/vendor/popper.min.js"></script>
	<script src="<?= site_url(); ?>assets/plugins/bootstrap-4.0.0/dist/js/bootstrap.min.js"></script>
	<?= $this->renderSection('js_ex') ?>
	<?= $this->renderSection('js_in') ?>
</body>

</html>
<?= $this->renderSection('modal') ?>