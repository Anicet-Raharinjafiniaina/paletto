<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WMS Gestion Palette</title>
    <link href='<?= base_url("assets/css/icons.min.css") ?>' rel="stylesheet" type="text/css" />
    <link href='<?= base_url("assets/css/bootstrap.min.css") ?>' id="bootstrap-style" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="<?= base_url('assets/css/login.css') ?>">
</head>

<body>
    <div class="container">
        <div class="login-card">

            <div class="logo">
                <div class="logo-icon">📦</div>
            </div>

            <div class="text-center mb-2">
                <img src="<?= base_url('assets/images/logoBoost2.png') ?>" alt="Logo WMS" width="180" class="img-fluid mb-2">
            </div>
            <br>
            <p class="subtitle"><i>Système de traçabilité des palettes</i></p>

            <form>
                <div class="form-group">
                    <div class="input-group">
                        <!-- Icône à gauche du champ de saisie -->
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                        </div>
                        <input type="text" class="form-control" id="login" placeholder="Entrez votre login" required>
                    </div>
                    <label id="login-error" class="validation-error-label" for="login"></label>
                </div>

                <div class="form-group">
                    <div class="input-group">
                        <!-- Icône à gauche du champ de saisie -->
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        </div>
                        <input type="password" class="form-control" id="password" placeholder="Entrez votre mot de passe">
                        <div class="input-group-append">
                            <!-- Icône d'œil pour montrer/cacher le mot de passe -->
                            <span class="input-group-text" id="togglePassword">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                    </div>
                    <label id="password-error" class="validation-error-label" for="password"></label>
                </div>

                <button type="button" id="btn_connecter" class="btn-primary">
                    Se connecter
                </button>
            </form>

            <script src="https://unpkg.com/feather-icons"></script>
            <span id="info"></span>
        </div>
    </div>
    <script>
        var urlProject = "<?= base_url(); ?>";
    </script>
    <script src="<?= base_url('assets/libs/jquery/jquery.min.js') ?>"></script>
    <script src="<?= base_url('assets/libs/feather-icons/feather.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/login.js') ?>"></script>
</body>

</html>