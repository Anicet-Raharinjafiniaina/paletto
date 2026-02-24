<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WMS Gestion Palette</title>
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
                <label for="login">Login (LDAP)</label>
                <input type="text" id="login" placeholder="Entrez votre login">
                <small id="login-error" class="error"></small>

                <label for="password">Mot de passe</label>
                <input type="password" id="password" placeholder="Entrez votre mot de passe">
                <small id="password-error" class="error"></small>

                <button type="button" id="btn_connecter" class="btn-primary">Se connecter</button>
            </form>
            <span id="info"></span>
        </div>
    </div>
    <script>
        var urlProject = "<?= base_url(); ?>";
    </script>
    <script src="<?= base_url('assets/libs/jquery/jquery.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/login.js') ?>"></script>
</body>

</html>