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

            <h2>WMS Gestion Palette</h2>
            <p class="subtitle">Système de traçabilité des palettes</p>

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
            <p class="info">Authentification via LDAP - 3 tentatives maximum</p>

            <hr>

            <p class="section-title">Accès rapide (Démo):</p>

            <div class="roles">
                <button class="role admin">Administrateur</button>
                <button class="role responsable">Responsable</button>
                <button class="role operateur">Opérateur</button>
                <button class="role consultation">Consultation</button>
            </div>

        </div>
    </div>
    <script>
        var urlProject = "<?= base_url(); ?>";
    </script>
    <script src="<?= base_url('assets/libs/jquery/jquery.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/login.js') ?>"></script>
</body>

</html>