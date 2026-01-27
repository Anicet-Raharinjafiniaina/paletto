<?php

if (!function_exists('get_user_photo')) {

    /**
     * Retourne le chemin ou le data URI de la photo de l'utilisateur.
     *
     * @param string $default Chemin de l'image par défaut
     * @return string
     */
    function get_user_photo($default = 'assets/images/users/default.jpg')
    {
        $user = session()->get('utilisateur');
        $photoData = $user['thumbnailphoto'] ?? null;

        if ($photoData) {
            $base64 = base64_encode($photoData);
            return "data:image/jpeg;base64,$base64";
        }

        return $default;
    }
}
