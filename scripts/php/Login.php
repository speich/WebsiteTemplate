<?php
/**
 * Class to manage user authentication and permissions.
 */
class Login extends Db {

	/** @var int logged in timeout in minutes before you are automatically logged out. */
	private $logTime = 30;

	/** @var array store messages */
	private $arrMsg = array();

	/** @var array store error messages */
	private $arrErrMsg = array();

	/**
	 * Set the time how long a user stays logged in.
	 * @param int $time
	 */
	public function setLogTime($time) {
		// overwrite default timespan of being logged in
		if ($time > session_cache_expire()) {
			$time = session_cache_expire();	// limit max to php session length
			echo "LogTime should not be longer than php session<br>LogTime set to $time";
		}
		$this->logTime = $time;
	}

	/**
	 * Set timestamp to keep track of logged in users.
	 * @param integer $userId
	 * @return bool
	 */
	public function setLogFlag($userId) {
	 /* start transaction
	  * Change connection mode to use transactions to prevent unrepeatable read;
	  * e.g. calculating id (SQL aggregation function MAX) on a number of records
	  * while other transactions are updating some of these records.
	  */
		$this->setCnnMode(OCI_DEFAULT);
		// get new id (autonumber)
		$sql = "SELECT MAX(appLogNr) AS newId FROM lfa.adAppLog";
		$stmt = $this->parse($sql);
		$this->execute($stmt);
		if (oci_fetch($stmt)) {
			$newId = ociresult($stmt, "NEWID") + 1;
		}
		else { $newId = 1; }
		$sql = "INSERT INTO lfa.adAppLog (appLogNr, userNr, datum) VALUES (:newId, :userNr, SYSDATE)";
		$stmt = $this->parse($sql);
		$this->bind($stmt, 'newId', $newId);
		$this->bind($stmt, 'userNr', $userId);
		$this->execute($stmt);
		return $this->commit();
	}

	/**
	 * Send the user the provided new password by email.
	 * The user object is expected to have at least the properties: lastName and email.
	 * @param object $user
	 * @param string $pwd password
	 * @return bool
	 */
	public function sendPassword($user, $pwd) {
		$subject = "NAFIDAS - Benutzername und Passwort";
		$msg = "Benutzername: ".$user->NAME."\n".
			"Passwort: ".$pwd."\n".
			"\nWICHTIG: Achten Sie auf die Klein-/Grossschreibung beim Passwort.";
		$headers = "From: lfa.wsl.ch\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/plain; charset=ISO-8859-1\r\n";
		// $sent = mail($arr[0]['EMAIL'], '=?UTF-8?B?'.base64_encode($subject).'?=', $msg, $headers);   // does not work with stupid lotus notes mailer, send as ISO
		$msg = utf8_decode($msg);
		return mail($user->EMAIL, $subject, $msg, $headers);
	}

	/**
	 * Check if user is member of a certain group(s).
	 * If you provide an array of group numbers, this method will check if user is member of at least one of the provided group ids.
	 * If you provide a single group number, the method will check if you are a member of this specific group.
	 * @param integer $userId
	 * @param integer|array $groups
	 * @return bool
	 */
	public function checkMembership($userId, $groups) {
		if (!is_array($groups)) {
			$groups = array($groups);
		}
		// to prevent SQL injection attack allow only integers as ids
		$strGroup = '';
		foreach ($groups as $id) {
			if (preg_match("/[0-9]+/", $id) > 0) {
				$strGroup.= "$id,";
			}
		}
		$strGroup = rtrim($strGroup, ',');
		$sql = "SELECT groupNr FROM lfa.adMember WHERE groupNr IN ($strGroup) AND userNr = :userId ORDER BY groupNr ASC";
		$stmt = $this->parse($sql);
		$this->bind($stmt, 'userId', $userId);
		$this->execute($stmt);
		oci_fetch_all($stmt, $arrData, null, null, OCI_ASSOC + OCI_FETCHSTATEMENT_BY_ROW);
		return count($arrData) > 0 ? true : false;
	}

	/**
	 * Returns the group ids a user is member of.
	 * @param integer $userId
	 * @return array
	 */
	public function getMembership($userId) {
		$sql = "SELECT groupNr FROM lfa.adMember WHERE userNr = :userId ORDER BY groupNr ASC";
		$stmt = $this->parse($sql);
		$this->bind($stmt, 'userId', $userId);
		$this->execute($stmt);
		oci_fetch_all($stmt, $arrData, null, null, OCI_ASSOC);
		return $arrData['GROUPNR'];
	}

	/**
	 * Stores (overwrites) the provided password hash for the specified user in the database.
	 * @param object $user user
	 * @param string $hash password hash
	 * @return bool
	 */
	public function savePasswordHash($user, $hash) {
		$sql = "UPDATE lfa.adUser SET pw = :hash WHERE name = :userId";
		$stmt = $this->parse($sql);
		$this->bind($stmt, 'hash', $hash);
		$this->bind($stmt, 'userId', $user->NAME);
		return $this->execute($stmt) && oci_num_rows($stmt) > 0;
	}

	/**
	 * Return a user object.
	 * Return a user object or false. The object has the following properties:
	 * userId, firstName, lastName, email and password hash.
	 * @param string $key
	 * @param string|integer $id
	 * @return object|boolean
	 */
	public function getUserBy($key, $id) {
		switch($key) {
			case 'userNr': $col = 'userNr'; break;
			case 'email': $col = 'email'; break;
			case 'name': $col = 'name'; break;
			default: $col = 'email';
		}
		$sql = "SELECT userNr, email, name, vorname, pw pwHash FROM lfa.adUser WHERE LOWER($col) = LOWER(:id)";
		$stmt = $this->parse($sql);
		$this->bind($stmt, 'id', $id);
		if ($this->execute($stmt)) {
			return oci_fetch_object($stmt);
		}
		else {
			return false;
		}
	}

	/**
	 * Set a message.
	 * @param string $str
	 */
	public function setMessage($str) {
		$this->arrMsg[] = $str;
	}

	/**
	 * Returns all messages as a string separated by html break tags.
	 * @return string messages
	 */
	public function getMessages() {
		$str = '';
		foreach ($this->arrMsg as $msg) {
			$str.= "$msg<br>";
		}
		return $str;
	}

	/**
	 * Set an error message.
	 * @param string $str error message
	 */
	public function setErrorMessage($str) {
		$this->arrErrMsg[] = $str;
	}

	/**
	 * Returns all error messages as a string separated by html break tags.
	 * @return string error messages
	 */
	public function getErrorMessages() {
		$str = '';
		foreach ($this->arrErrMsg as $msg) {
			$str.= "$msg<br>";
		}
		return $str;
	}

	public function getNumErrorMessages() {
		return count($this->arrErrMsg);
	}


}
