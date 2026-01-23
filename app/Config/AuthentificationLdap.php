<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class AuthentificationLdap extends BaseConfig
{

    // Serveur LDAP
    private $ldapHost = 'dc3.basan.mg';

    // LDAPS activé
    private $useLdaps = true;

    // Ports LDAPS recommandé = 636
    private $port = [
        'ldaps'   => 636,   // d'après paramétrage reçu
        // 'default' => 389    // fallback
    ];

    private $dn = 'DC=basan,DC=mg';

    private $attributes = [
        "sn",
        "samaccountname",
        "title",
        "physicaldeliveryofficename",
        "givenName",
        "department",
        "mail",
        "manager",
        "name",
        "cn",
        "thumbnailPhoto"
    ];

    // Domaine NetBIOS (AD)
    private $domain = "BASAN\\";

    public function getLdapUrl()
    {
        if ($this->useLdaps) {
            return sprintf("ldaps://%s:%d", $this->ldapHost, $this->port['ldaps']);
        }
        // return sprintf("ldap://%s:%d", $this->ldapHost, $this->port['default']);
    }

    public function getBaseDn()
    {
        return $this->dn;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function getDomain()
    {
        return $this->domain;
    }
}
