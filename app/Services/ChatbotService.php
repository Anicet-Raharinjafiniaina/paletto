<?php

namespace App\Services;

use CodeIgniter\Database\BaseConnection;

class ChatbotService
{
    private string $apiKey;
    private string $apiUrl = 'https://api.groq.com/openai/v1/chat/completions';
    private string $model  = 'llama-3.3-70b-versatile';
    // private string $model  = 'openai/gpt-oss-120b';

    /* LLM local local */
    /* private string $apiUrl = 'http://ollama:11434/v1/chat/completions';
    private string $model  = 'qwen2.5:3b';*/

    private BaseConnection $db;

    public function __construct()
    {
        $this->apiKey = env('GROQ_API_KEY'); // si online
        $this->db     = \Config\Database::connect();
    }

    // -------------------------------------------------------
    // Point d'entrée principal (le mémoire de la conversation dépend du modèle utilisé)
    // -------------------------------------------------------

    public function chat(string $userMessage, array $history = []): string
    {
        $contextData  = $this->loadAllData($userMessage);
        $systemPrompt = $this->buildSystemPrompt($contextData);

        $messages = [['role' => 'system', 'content' => $systemPrompt]];

        foreach ($history as $msg) {
            $messages[] = [
                'role'    => $msg->role,
                'content' => $msg->content,
            ];
        }

        $messages[] = ['role' => 'user', 'content' => $userMessage];

        return $this->callGroq($messages);
        //return $this->callLLM($messages);   /* LLM local local */
    }

    /* Mémoire des conversation en amont (boffe bcp de token) */
    /*   public function chat(string $userMessage, array $history = []): string
    {
        // Construire le contexte complet = historique + message actuel
        $fullContext = '';
        foreach ($history as $msg) {
            $fullContext .= $msg->role . ': ' . $msg->content . "\n";
        }
        $fullContext .= 'user: ' . $userMessage;

        // Passer le contexte complet pour détecter l'intention
        $contextData  = $this->loadAllData($fullContext);
        $systemPrompt = $this->buildSystemPrompt($contextData);

        $messages = [['role' => 'system', 'content' => $systemPrompt]];

        foreach ($history as $msg) {
            $messages[] = [
                'role'    => $msg->role,
                'content' => $msg->content,
            ];
        }

        $messages[] = ['role' => 'user', 'content' => $userMessage];

        return $this->callGroq($messages);
    }*/

    // -------------------------------------------------------
    // Chargement intelligent selon l'intention
    // -------------------------------------------------------

    private function loadAllData(string $question): array
    {
        $q    = strtolower($question);
        $data = [];

        // Toujours inclure un résumé global léger
        $data['resume'] = $this->getResume();

        if ($this->contains($q, ['utilisateur', 'user', 'compte', 'personnel', 'connecté', 'connexion', 'actif', 'login'])) {
            $data['utilisateurs'] = $this->getUtilisateurs();
        }

        if ($this->contains($q, ['profil', 'rôle', 'role', 'permission', 'accès'])) {
            $data['profils'] = $this->getProfils();
        }

        if ($this->contains($q, ['entrepôt', 'entrepot', 'warehouse', 'site', 'localisation'])) {
            $data['entrepots'] = $this->getEntrepots();
        }

        if ($this->contains($q, ['emplacement', 'libre', 'occupé', 'disponible', 'place', 'capacité', 'adresse'])) {
            $data['emplacements'] = $this->getEmplacements();
        }

        if ($this->contains($q, ['allée', 'allee'])) {
            $data['allees'] = $this->getAllees();
        }

        if ($this->contains($q, ['rangée', 'rangee'])) {
            $data['rangees'] = $this->getRangees();
        }

        if ($this->contains($q, ['niveau'])) {
            $data['niveaux'] = $this->getNiveaux();
        }

        if ($this->contains($q, ['cage', 'alvéol', 'alveol'])) {
            $data['cages'] = $this->getCages();
        }

        if ($this->contains($q, ['palette', 'palettes'])) {
            $data['palettes'] = $this->getPalettes();
        }

        if ($this->contains($q, ['article', 'produit', 'stock', 'dluo', 'lot', 'quantité', 'affiche', 'liste', 'montre', 'voir'])) {
            $data['articles'] = $this->getArticles();
        }

        if ($this->contains($q, ['mouvement', 'entrée', 'sortie', 'transfert'])) {
            $data['mouvements'] = $this->getMouvements();
        }

        if ($this->contains($q, ['historique', 'journal', 'activité', 'log', 'action'])) {
            $data['historique'] = $this->getHistorique();
        }

        return $data;
    }

    // -------------------------------------------------------
    // Résumé global (toujours chargé)
    // -------------------------------------------------------

    private function getResume(): array
    {
        return [
            'nb_utilisateurs' => $this->db->table('utilisateur')->where('flag_suppression', 0)->countAllResults(),
            'nb_entrepots'    => $this->db->table('entrepot')->where('flag_suppression', 0)->countAllResults(),
            'nb_emplacements' => $this->db->table('emplacement')->where('flag_suppression', 0)->countAllResults(),
            'nb_palettes'     => $this->db->table('palette')->where('flag_suppression', 0)->countAllResults(),
            'nb_articles'     => $this->db->table('article')->where('flag_suppression', 0)->countAllResults(),
            'nb_mouvements'   => $this->db->table('mouvement')->where('flag_suppression', 0)->countAllResults(),
        ];
    }

    // -------------------------------------------------------
    // Méthodes de chargement par entité
    // -------------------------------------------------------

    private function getUtilisateurs(): array
    {
        $liste = $this->db->table('utilisateur')
            ->select('id, nom, login, mail, fonction, actif, derniere_connexion, date_creation')
            ->where('flag_suppression', 0)
            ->orderBy('nom', 'ASC')
            ->get()->getResultArray();

        return [
            'total'  => count($liste),
            'actifs' => count(array_filter($liste, fn($u) => $u['actif'] == 1)),
            'liste'  => $liste,
        ];
    }

    private function getProfils(): array
    {
        return $this->db->table('profil')
            ->select('id, libelle, actif')
            ->where('flag_suppression', 0)
            ->get()->getResultArray();
    }

    private function getEntrepots(): array
    {
        $entrepots = $this->db->table('entrepot')
            ->where('flag_suppression', 0)
            ->get()->getResultArray();

        $result = [];
        foreach ($entrepots as $e) {
            $total  = $this->db->table('emplacement_adresse_view')->where('entrepot_id', $e['id'])->countAllResults();
            $libres = $this->db->table('emplacement_adresse_view')->where('entrepot_id', $e['id'])->where('statut_id', 1)->countAllResults();

            $result[] = [
                'id'                   => $e['id'],
                'code'                 => $e['code'],
                'nom'                  => $e['nom'],
                'localisation'         => $e['localisation'],
                'total_emplacements'   => $total,
                'emplacements_libres'  => $libres,
                'emplacements_occupes' => $total - $libres,
            ];
        }

        return $result;
    }

    private function getEmplacements(): array
    {
        $liste = $this->db->table('emplacement_adresse_view')
            ->select('emplacement_adresse_id, entrepot_nom, entrepot_id, allee_code, rangee_code, niveau_code, cage_code, emplacement_code, statut, statut_id, qr_code_texte')
            ->get()->getResultArray();

        return [
            'total'   => count($liste),
            'libres'  => count(array_filter($liste, fn($e) => $e['statut_id'] == 1)),
            'occupes' => count(array_filter($liste, fn($e) => $e['statut_id'] != 1)),
            'liste'   => $liste,
        ];
    }

    private function getAllees(): array
    {
        return $this->db->table('allee')
            ->select('allee.id, allee.code, entrepot.nom as entrepot')
            ->join('entrepot', 'entrepot.id = allee.entrepot_id', 'left')
            ->where('allee.flag_suppression', 0)
            ->get()->getResultArray();
    }

    private function getRangees(): array
    {
        return $this->db->table('rangee')
            ->select('rangee.id, rangee.code, entrepot.nom as entrepot, allee.code as allee')
            ->join('entrepot', 'entrepot.id = rangee.entrepot_id', 'left')
            ->join('allee', 'allee.id = rangee.allee_id', 'left')
            ->where('rangee.flag_suppression', 0)
            ->get()->getResultArray();
    }

    private function getNiveaux(): array
    {
        return $this->db->table('niveau')
            ->select('niveau.id, niveau.code, entrepot.nom as entrepot, allee.code as allee, rangee.code as rangee')
            ->join('entrepot', 'entrepot.id = niveau.entrepot_id', 'left')
            ->join('allee', 'allee.id = niveau.allee_id', 'left')
            ->join('rangee', 'rangee.id = niveau.rangee_id', 'left')
            ->where('niveau.flag_suppression', 0)
            ->get()->getResultArray();
    }

    private function getCages(): array
    {
        return $this->db->table('cage')
            ->select('cage.id, cage.code, entrepot.nom as entrepot, allee.code as allee, rangee.code as rangee, niveau.code as niveau')
            ->join('entrepot', 'entrepot.id = cage.entrepot_id', 'left')
            ->join('allee', 'allee.id = cage.allee_id', 'left')
            ->join('rangee', 'rangee.id = cage.rangee_id', 'left')
            ->join('niveau', 'niveau.id = cage.niveau_id', 'left')
            ->where('cage.flag_suppression', 0)
            ->get()->getResultArray();
    }

    private function getPalettes(): array
    {
        $liste = $this->db->table('palette')
            ->select('palette.id, palette.code, palette.client_code, palette.client_nom, palette_statut.statut, palette.date_creation')
            ->join('palette_statut', 'palette_statut.id = palette.palette_statut_id', 'left')
            ->where('palette.flag_suppression', 0)
            ->orderBy('palette.code', 'ASC')
            ->get()->getResultArray();

        $parStatut = [];
        foreach ($liste as $p) {
            $s = $p['statut'] ?? 'Inconnu';
            $parStatut[$s] = ($parStatut[$s] ?? 0) + 1;
        }

        return [
            'total'      => count($liste),
            'par_statut' => $parStatut,
            'liste'      => $liste,
        ];
    }

    private function getArticles(): array
    {
        $liste = $this->db->table('article')
            ->select('article.id, article.code, article.nom, article.client_code, article.client_nom, article.quantite, article.dluo, article.lot, article.unite_stockage, article.unite_pcb, article.palettisation, article.actif, article.affectee_emplacement, mouvement_type.type as mouvement_type')
            ->join('mouvement_type', 'mouvement_type.id = article.mouvement_type_id', 'left')
            ->where('article.flag_suppression', 0)
            ->orderBy('article.nom', 'ASC')
            ->get()->getResultArray();

        $dluoProche = array_filter($liste, function ($a) {
            if (empty($a['dluo'])) return false;
            $dluo = strtotime($a['dluo']);
            return $dluo >= strtotime('today') && $dluo <= strtotime('+30 days');
        });

        return [
            'total'       => count($liste),
            'actifs'      => count(array_filter($liste, fn($a) => $a['actif'] == 1)),
            'dluo_proche' => array_values($dluoProche),
            'liste'       => $liste,
        ];
    }

    private function getMouvements(): array
    {
        $liste = $this->db->table('mouvement')
            ->select('mouvement.id, mouvement_type.type, palette.code as palette_code, emplacement_adresse_view.qr_code_texte as emplacement, utilisateur.nom as cree_par, mouvement.date_mouvement')
            ->join('mouvement_type', 'mouvement_type.id = mouvement.mouvement_type_id', 'left')
            ->join('palette', 'palette.id = mouvement.palette_id', 'left')
            ->join('emplacement_adresse_view', 'emplacement_adresse_view.emplacement_id = mouvement.emplacement_id', 'left')
            ->join('utilisateur', 'utilisateur.id = mouvement.cree_par', 'left')
            ->where('mouvement.flag_suppression', 0)
            ->where('mouvement.actif', 1)
            ->orderBy('mouvement.date_mouvement', 'DESC')
            ->limit(50)
            ->get()->getResultArray();

        $parType = [];
        foreach ($liste as $m) {
            $t = $m['type'] ?? 'Inconnu';
            $parType[$t] = ($parType[$t] ?? 0) + 1;
        }

        return [
            'total'    => count($liste),
            'par_type' => $parType,
            'liste'    => $liste,
        ];
    }

    private function getHistorique(): array
    {
        return $this->db->table('historique')
            ->select('historique.date_creation, action.libelle as action, utilisateur.nom as utilisateur')
            ->join('action', 'action.id = historique.action_id', 'left')
            ->join('utilisateur', 'utilisateur.id = historique.utilisateur_id', 'left')
            ->orderBy('historique.date_creation', 'DESC')
            ->limit(30)
            ->get()->getResultArray();
    }

    // -------------------------------------------------------
    // Helper
    // -------------------------------------------------------

    private function contains(string $text, array $keywords): bool
    {
        foreach ($keywords as $kw) {
            if (str_contains($text, $kw)) return true;
        }
        return false;
    }

    // -------------------------------------------------------
    // Construction du system prompt
    // -------------------------------------------------------

    private function buildSystemPrompt(array $data): string
    {
        $now = new \DateTime('now', new \DateTimeZone('Indian/Antananarivo'));

        $prompt  = "Tu es un assistant intelligent intégré à une application de gestion d'entrepôt.\n";
        $prompt .= "L'application gère : entrepôts, emplacements, palettes, articles, mouvements, utilisateurs et accès.\n";
        $prompt .= "Réponds toujours en français, de façon concise et professionnelle.\n";
        $prompt .= "Quand on te demande une liste, présente-la de façon claire et structurée.\n";
        $prompt .= "Propose une question de suivi pertinente à la fin si c'est utile.\n\n";

        $prompt .= "=== CONVENTION DES CHAMPS ===\n";
        $prompt .= "Pour toutes les tables, les champs suivants ont ces significations :\n";
        $prompt .= "- actif = 1 signifie que l'enregistrement est ACTIF\n";
        $prompt .= "- actif = 0 signifie que l'enregistrement est INACTIF\n";
        $prompt .= "- flag_suppression = 1 signifie que l'enregistrement est SUPPRIMÉ (corbeille)\n";
        $prompt .= "- flag_suppression = 0 signifie que l'enregistrement est NON SUPPRIMÉ (visible)\n";
        $prompt .= "Les données fournies ci-dessous excluent déjà les enregistrements supprimés (flag_suppression = 0).\n\n";

        $prompt .= "=== DATE ET HEURE ACTUELLES ===\n";
        $prompt .= "Date : " . $now->format('d/m/Y') . "\n";
        $prompt .= "Heure : " . $now->format('H:i:s') . "\n";
        $prompt .= "Jour : " . $this->jourEnFrancais($now->format('l')) . "\n\n";

        $prompt .= "=== DONNÉES DE LA BASE ===\n";
        $prompt .= json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $prompt .= "\n=== FIN DES DONNÉES ===\n\n";
        $prompt .= "Base tes réponses UNIQUEMENT sur ces données. Ne les invente jamais.";

        return $prompt;
    }

    private function jourEnFrancais(string $jour): string
    {
        $jours = [
            'Monday'    => 'Lundi',
            'Tuesday'   => 'Mardi',
            'Wednesday' => 'Mercredi',
            'Thursday'  => 'Jeudi',
            'Friday'    => 'Vendredi',
            'Saturday'  => 'Samedi',
            'Sunday'    => 'Dimanche',
        ];
        return $jours[$jour] ?? $jour;
    }

    // -------------------------------------------------------
    // Appel API Groq
    // -------------------------------------------------------

    private function callGroq(array $messages): string
    {
        $payload = json_encode([
            'model'       => $this->model,
            'messages'    => $messages,
            'max_tokens'  => 1024,
            'temperature' => 0.4,
        ]);

        $ch = curl_init($this->apiUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                /* 'Authorization: Bearer ollama', */ // valeur factice, Ollama l'ignore (  /* LLM local local */)
                'Authorization: Bearer ' . $this->apiKey, // si online
            ],
            CURLOPT_TIMEOUT        => 120,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);

        $response  = curl_exec($ch);
        $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            throw new \RuntimeException("Erreur cURL : $curlError");
        }

        if ($httpCode !== 200) {
            $err = json_decode($response, true);
            $msg = $err['error']['message'] ?? "HTTP $httpCode";
            throw new \RuntimeException("Erreur Groq : $msg");
        }

        $decoded = json_decode($response, true);
        return $decoded['choices'][0]['message']['content']
            ?? "Désolé, je n'ai pas pu générer une réponse.";
    }

    /* LLM local local */
    // private function callLLM(array $messages): string
    // {
    //     $payload = json_encode([
    //         'model'       => $this->model,
    //         'messages'    => $messages,
    //         'max_tokens'  => 1024,
    //         'temperature' => 0.4,
    //     ]);

    //     $ch = curl_init($this->apiUrl);
    //     curl_setopt_array($ch, [
    //         CURLOPT_RETURNTRANSFER => true,
    //         CURLOPT_POST           => true,
    //         CURLOPT_POSTFIELDS     => $payload,
    //         CURLOPT_HTTPHEADER     => [
    //             'Content-Type: application/json',
    //             'Authorization: Bearer ollama',
    //         ],
    //         CURLOPT_TIMEOUT        => 120,
    //         CURLOPT_CONNECTTIMEOUT => 10,
    //         CURLOPT_SSL_VERIFYPEER => false,
    //         CURLOPT_SSL_VERIFYHOST => false,
    //     ]);

    //     $response  = curl_exec($ch);
    //     $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    //     $curlError = curl_error($ch);
    //     curl_close($ch);

    //     if ($curlError) {
    //         throw new \RuntimeException("Erreur cURL : $curlError");
    //     }

    //     if ($httpCode !== 200) {
    //         $err = json_decode($response, true);
    //         $msg = $err['error']['message'] ?? "HTTP $httpCode";
    //         throw new \RuntimeException("Erreur LLM : $msg");
    //     }

    //     $decoded = json_decode($response, true);
    //     return $decoded['choices'][0]['message']['content']
    //         ?? "Désolé, je n'ai pas pu générer une réponse.";
    // }
}
