<?php
function encrypt($pwd)
{
    //$encryption_key = "Pa13Journal13r32022";
    return base64_encode(openssl_encrypt($pwd, AES_128_ECB, ENCRYPTION_KEY, 0));
}

function decrypt($pwd)
{
    // $encryption_key = "Pa13Journal13r32022";
    return openssl_decrypt(base64_decode($pwd), AES_128_ECB, ENCRYPTION_KEY, 0);
}

/**
 * Pour avoir 02 chifrres en mettant 0 devant si n�cessaire
 */
function deuxChiffre($var)
{
    if (strlen($var) <= 1 && !empty($var)) {
        return '0' . $var;
    } else {
        return $var;
    }
}

/**
 * Date et heure actuelle
 */
function getActualDate()
{
    $date = getdate();
    $arrDate['type1'] =  $dateCreation =  deuxChiffre($date['mday']) . '/' .  deuxChiffre($date['mon']) . '/' . $date['year'];
    $arrDate['type2'] =  $date['year'] . '/' . $dateCreation =  deuxChiffre($date['mon']) . '/' .  deuxChiffre($date['mday']);
    $arrDate['type3'] =  $dateCreation =  deuxChiffre($date['mday']) . '/' .  deuxChiffre($date['mon']) . '/' . $date['year'] . ' ' . deuxChiffre($date['hours']) . 'h' . deuxChiffre($date['minutes']) . 'mn' . deuxChiffre($date['seconds']) . 's';

    return $arrDate;
}

/**
 * Ajouter une ou des caract�res devant ou/et derri�re un mot
 */
function personalizeWord($mot, $nbCharAvant, $nbCharApres, $char)
{
    $charAvant = "";
    $charApres = "";
    for ($i = 0; $i < $nbCharAvant; $i++) {
        $charAvant .= $char;
    }
    for ($i = 0; $i < $nbCharApres; $i++) {
        $charApres .= $char;
    }
    return $charAvant . $mot . $charApres;
}
