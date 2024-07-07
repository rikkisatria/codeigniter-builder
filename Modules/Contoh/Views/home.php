<?= $this->extend('layout/template') ?>

<?= $this->section('title_page'); ?>
<title>Laman Contoh</title>
<?= $this->endSection(); ?>

<?= $this->section('css_ex'); ?>
<link href="<?= site_url(); ?>assets/css/sticky-footer-navbar.css" rel="stylesheet">
<?= $this->endSection(); ?>

<?= $this->section('css_in'); ?>
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>
<!-- CONTAINER -->
<main role="main" class="container">
    <h1 class="mt-5">Sticky footer with fixed navbar</h1>
    <p class="lead">Pin a fixed-height footer to the bottom of the viewport in desktop browsers with this custom HTML and CSS. A fixed navbar has been added with <code>padding-top: 60px;</code> on the <code>body &gt; .container</code>.</p>
    <p>Back to <a href="">the default sticky footer</a> minus the navbar.</p>
</main>
<?= $this->endSection(); ?>

<?= $this->section('modal'); ?>
<?= $this->endSection(); ?>

<?= $this->section('js_ex'); ?>
<?= $this->endSection(); ?>

<?= $this->section('js_in'); ?>
<?= $this->endSection(); ?>