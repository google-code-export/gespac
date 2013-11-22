<?php

	class Sql extends PDO  {
		
		# Propriétés
		
		private $host;
		private $user;
		private $pass;
		private $db;
		private $link;	// Le handle pour le SQL
		private $log;	// Le handle pour les logs
		private $path;	// Le chemin d'accès pour le logs
		
		public $exists;	// La connexion existe
		
	
		
		
		# Magiques
		
		
		/*
		* @name: Constructeur
		* @param : paramètres de connexion : hote, utilisateur, mot de passe et base de données sur laquelle se connecter + logfile qui spécifie le chemin du fichier où stocker la trace des requête d'execution
		* @return : rien
		* @description : se connecte à l'hote puis à la base
		*/
		public function __construct ($host = 'localhost', $user = 'root', $pass = '', $db, $logfile = '') {
			
			$this->host = $host;
			$this->user = $user;
			$this->pass = $pass;
			$this->db 	= $db;
			$this->path	= $logfile;
									
			try {
				$this->link = parent::__construct('mysql:host=' . $this->host . ';dbname=' . $this->db, $this->user, $this->pass);
				$this->exists = true;
				
				// Si logfile est spécifié, alors on log
				if ( $logfile <> "" ) {
					$this->log = fopen($this->path, 'a+');
				}
				
			}
			catch (PDOException $e) {
				//echo "Connexion à MySQL impossible : ", $e->getMessage();
				//die();
				$this->exists = false;
			}
			

			
		}
		
				
		/*
		* @name: Destructeur
		* @param : rien
		* @return : rien
		* @description : ferme la connexion
		* @amelioration : ben ca marche pas : Warning: mysql_close(): 7 is not a valid MySQL-Link resource in D:\DATA\DEV\php\gespac4\class\Sql.class.php on line 50
		*/		
		public function __destruct () {	
			$this->link = null;
		}
				
		
		/*
		* @name: toString
		* @param : rien
		* @return : rien
		* @description : Retourne une chaine si l'objet est sérialisé
		*/	
		public function __toString () {	
			return utf8_decode("L'utilisateur <b>" . $this->user . "</b> est connecté à la base <b>" . $this->db . "</b> sur l'hote <b>" . $this->host . "</b>.");
		}
		
		
		
		# Méthodes
		
				
		
		/*
		* @name: Close
		* @param : rien
		* @return : rien
		* @description : ferme manuellement le fichier
		*/
		public function CloseLog () {
			fclose($this->log);
		}
		
		/*
		* @name: InsertLog
		* @param : le texte à insérer
		* @return : TRUE si on a pu écrire, FALSE sinon
		* @description : insert un texte dans un fichier avec date et mise en forme utf8
		*/
		public function InsertLog ($text) {
			
			if ( !fwrite($this->log, date("Ymd His") . " " . utf8_decode($text) ."\n") ) {
				return false; // On arrive pas à écrire
			}
			else {
				return true; // On arrive à écrire
			}
		}

		
		/*
		* @name: close
		* @param : rien
		* @return : rien
		* @description : ferme la connexion manuellement
		*/	
		public function Close () {
			//mysql_close($this->link);
		}
		
		
		/*
		* @name: QueryAll
		* @param : La requête à exécuter
		* @return : Le tableau des résultats
		* @description : Execute une requête SQL SELECT
		*/
		public function QueryAll ($query) {
				
			try {
				$req = parent::query($query);
				$result = $req->fetchAll();
				
				if ($this->log)	$this->InsertLog($query);
			  			  
			  return $result;
			}
			catch (PDOException $e) {
			  print $e->getMessage();
			  return false;
			}
			
		}
		
		
		/*
		* @name: QueryRow
		* @param : La requête à exécuter
		* @return : La ligne retournée par sql dans un tableau d'une ligne
		* @description : Execute une requête SQL SELECT
		*/
		public function QueryRow ($query) {

			try {
				$req = parent::query($query);
				$result = $req->fetch();
				
				if ($this->log)	$this->InsertLog($query);
			  			  
				return $result;
			}
			catch (PDOException $e) {
				print $e->getMessage() . "<br> Request : " . $query;
				return false;
			}
			
		}
		
		
		/*
		* @name: QueryOne
		* @param : La requête à exécuter
		* @return : La valeur retournée par sql dans une variable (pas de tableau donc)
		* @description : Execute une requête SQL SELECT
		*/
		public function QueryOne ($query) {
	
			try {
				$req = parent::query($query);
				$result = $req->fetchColumn();

				if ($this->log)	$this->InsertLog($query);
			  			  
				return $result;
			}
			catch (PDOException $e) {
				print $e->getMessage() . "<br> Request : " . $query;
				return false;
			}
		}
		
		
		/*
		* @name: Execute
		* @param : La requête à exécuter
		* @return : Le nombre d'enregistrements affectés par la requête
		* @description : Execute une requête SQL action (insert, update, delete ...) 
		*/
		public function Execute ($query) {
			
			try {

				$result = parent::exec($query);
				
				if ($this->log)	$this->InsertLog($query);
			  			  
				return $result;
			}
			catch (PDOException $e) {
				print $e->getMessage() . "<br> Request : " . $query;
				return false;
			}	
		}
		
				
		/*
		* @name: Help
		* @param : nada
		* @return : nada
		* @description : Affiche la liste des méthodes disponibles pour la classe SQL 
		*/
		public function Help () {
		}
		
		
		/*
		* @name: GetLastID
		* @param : aucun
		* @return : dernier id ajouté
		* @description : renvoie l'id automatique de la dernière requête d'ajout
		*/
		public function GetLastID () {
			return $this->link->lastInsertId();
		}
		

	}


