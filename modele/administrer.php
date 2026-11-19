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

    private function fetch_url(string $ajout): array {
        $url = self::URL_BASE . $ajout;
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => "Accept: application/json\r\n",
                'timeout' => 10,
                'ignore_errors' => true,
            ],
        ]);

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
        return $this->fetch_url("/api/rest/formations/" . $ifc . "?full-details=1");
    }


}