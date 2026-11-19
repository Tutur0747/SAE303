<?php 

declare(strict_types=1);

// use PDO;
// use SplFileObject;
// use Exception;

/**
 * Administrer
 */
class Administrer
{
    private const URL_BASE = "https://la-lab4ce.univ-lemans.fr/masters-stats";
    private $liste_mentions_memoire;

	private $myHost;

	private $myDb;

	private $myUser;

	private $myPass;

	private $debug;

	/**
	 * Administrer
	 *
	 * @param string $myHost
	 * @param string $myDb
	 * @param string $myUser
	 * @param string $myPass
	 *
	 * @return Administrer
	 */
	function __construct($myHost = null, $myDb = null, $myUser = null, $myPass = null)
	{
		$this->myHost = $myHost;
		$this->myDb = $myDb;
		$this->myUser = $myUser;
		$this->myPass = $myPass;
        $this->liste_mentions_memoire = null;

		$this->debug = true;
	}

    private function fetch_url(string $ajout, ?array $json = null): array {
        $url = self::URL_BASE . $ajout;
        $headers = ['Accept: application/json'];
        $httpOptions = [
            'method' => $json === null ? 'GET' : 'POST',
            'timeout' => 10,
            'ignore_errors' => true,
        ];
        if ($json !== null) {
            $headers[] = 'Content-Type: application/json';
            $httpOptions['content'] = json_encode($json, JSON_THROW_ON_ERROR);
        }
        $httpOptions['header'] = implode("\r\n", $headers) . "\r\n";
        $context = stream_context_create(['http' => $httpOptions]);

        $streamError = null;
        set_error_handler(static function (int $severity, string $message) use (&$streamError): bool {
            $streamError = $message;
            return true;
        }, E_WARNING);

        try {
            $response = file_get_contents($url, false, $context);
        } finally {
            restore_error_handler();
        }

        if ($response === false) {
            throw new RuntimeException(
                'Impossible de récupérer les données de l’API.' .
                ($streamError !== null ? ' ' . $streamError : '')
            );
        }

        $status = null;
        foreach ($http_response_header ?? [] as $header) {
            if (preg_match('/^HTTP\/\S+\s+(\d{3})\b/i', $header, $matches)) {
                $status = (int) $matches[1];
            }
        }

        if ($status === null || $status < 200 || $status >= 300) {
            throw new RuntimeException(
                'L’API a répondu avec le statut HTTP ' . ($status ?? 'inconnu') . '.'
            );
        }

        $data = json_decode($response, true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($data)) {
            throw new UnexpectedValueException('La réponse de l’API doit être un objet ou un tableau JSON.');
        }

        return $data;
    }

	public function getListeMentions() {
		return $this->fetch_url("/api/rest/mentions");
	}

	public function getListeEtablissements () {
		return $this->fetch_url("/api/rest/etablissements");
	}

    public function getListeFormationsDuSecteurDisciplinaire() {
        return $this->fetch_url("/api/rest/formations?sdid=2");
    }

    public function getMentionById(int $idMention) {
        if ($this->liste_mentions_memoire == null) {
            $this->liste_mentions_memoire = $this->getListeMentions();
        }
        $liste_mentions = $this->liste_mentions_memoire;
        
        foreach ($liste_mentions as $mention) {
            if ($mention["id"] == $idMention) {
                return $mention;
            }
        }
        return null;
    }

    public function getFormationById(String $ifc) {
        return $this->fetch_url("/api/rest/formations/" . rawurlencode($ifc) . "?full-details=1");
    }

    public function getNbCan(string $formationIfc, int $annee): ?int {
        $response = $this->fetch_url('/api/rest/stats/search', [
            'filters' => [
                'formationIfcs' => [$formationIfc],
                'annees' => $annee,
            ],
            'harvest' => [
                'typeStats' => 'candidatures',
                'candidatureDetails' => 'general',
            ],
        ]);

        if (!isset($response['candidatures']) || !is_array($response['candidatures'])) {
            throw new UnexpectedValueException('La réponse de l’API ne contient pas la liste des candidatures.');
        }
        if ($response['candidatures'] === []) {
            return null;
        }

        $total = 0;
        foreach ($response['candidatures'] as $candidature) {
            $nb = $candidature['general']['nb'] ?? null;
            if (!is_numeric($nb)) {
                throw new UnexpectedValueException('Une statistique de candidature ne contient pas de nombre valide.');
            }
            $total += (int) $nb;
        }

        return $total;
    }

}