<?php

namespace App\Libraries;

use Config\AuthentificationLdap;

class LibLdap
{
    private $config;
    private $username;
    private $password;
    private $searchLogin;
    private $ldap;
    private $filter;

    public function __construct($username, $password, $loginSearch = null, $filter = null)
    {
        $this->config = new AuthentificationLdap();

        $this->username = $username;
        $this->password = $password;
        $this->searchLogin = $loginSearch ?: $username;
        $this->filter = $filter;
        // 🔥 Désactiver la vérification du certificat AVANT la connexion
        putenv("LDAPTLS_REQCERT=never");

        ldap_set_option(NULL, LDAP_OPT_X_TLS_REQUIRE_CERT, LDAP_OPT_X_TLS_NEVER);
        ldap_set_option(NULL, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option(NULL, LDAP_OPT_REFERRALS, 0);

        // 🔥 Connexion LDAPS
        $this->ldap = ldap_connect($this->config->getLdapUrl());

        if (!$this->ldap) {
            die("Impossible d'initialiser LDAP");
        }

        // 🔥 Appliquer les options sur la connexion
        ldap_set_option($this->ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($this->ldap, LDAP_OPT_REFERRALS, 0);
    }

    public function authentificate()
    {
        // 1. Bind technique (Bind DN)
        $bind = @ldap_bind(
            $this->ldap,
            "CN=adsync adsync,OU=Bot_Users,OU=Basan,DC=basan,DC=mg", // fourni par l’admin
            "Welcome1!"
        );

        if (!$bind) {
            die("Bind technique échoué : " . ldap_error($this->ldap));
        }

        if (!$bind) {
            ldap_close($this->ldap);
            return false;
        }

        // 2. Recherche utilisateur
        if ($this->filter == null) {
            $filter = "(sAMAccountName={$this->searchLogin})";
        } else {
            $filter = $this->filter;
        }

        $searchResult = ldap_search(
            $this->ldap,
            $this->config->getBaseDn(),
            $filter,
            $this->config->getAttributes()
        );

        $entries = ldap_get_entries($this->ldap, $searchResult);

        if (!isset($entries['count']) || $entries['count'] < 1) {
            ldap_close($this->ldap);
            return false;
        }

        if ($this->filter == null) {
            // DN de l’utilisateur AD
            $userDn = $entries[0]['dn'];

            // 3. Bind utilisateur final
            $authUser = @ldap_bind($this->ldap, $userDn, $this->password);

            if (!$authUser) {
                ldap_close($this->ldap);
                return false;
            }
        }

        return $entries; // succès → retourne info utilisateur
    }

    public function getInfos()
    {
        $result = $this->authentificate();
        if ($result === false) {
            return false;
        }

        ldap_close($this->ldap);
        return $result;
    }
}
