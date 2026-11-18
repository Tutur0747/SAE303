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

		$this->debug = true;
	}

    private function fetch_url(String $ajout) {
        $url = self::URL_BASE . $ajout;
		$curl = curl_init($url);

		if ($curl === false) {
			throw new RuntimeException('Impossible d’initialiser cURL.');
		}

		curl_setopt_array($curl, [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER => ['Accept: application/json'],
			CURLOPT_TIMEOUT => 10,
		]);

		$response = curl_exec($curl);

		if ($response === false) {
			$error = curl_error($curl);
			throw new RuntimeException('Erreur lors de l’appel à l’API : ' . $error);
		}
		
		$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		if ($status < 200 || $status >= 300) {
			throw new RuntimeException("L’API a répondu avec le statut HTTP $status.");
		}

		return json_decode($response, true, 512, JSON_THROW_ON_ERROR);
    }

	public function getListeMentions() {
		return $this->fetch_url("/api/rest/mentions");
	}
}