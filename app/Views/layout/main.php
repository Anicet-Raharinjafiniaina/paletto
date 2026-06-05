<?= $this->include('layout/header') ?>

<?= $this->include('layout/navbar') ?>

<?= $this->include('chatbot/chatbot') ?>

<div class="main-content" id="main">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 id="titre_page" class="mb-sm-0 font-size-18"><?= esc($titre ?? '') ?></h4>
                    </div>
                </div>
                <div id="content-page" class="container-fluid bg-ligth ">
                    <?= $this->renderSection('content') ?>
                </div>
            </div>

        </div> <!-- container-fluid -->
    </div>
</div>
<script>
    var urlProject = "<?= base_url(); ?>";
</script>
<?= $this->include('layout/footer') ?>