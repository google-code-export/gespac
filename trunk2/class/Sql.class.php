<?php

	class Sql {
		
		# Propriétés
		
		private $host;
		private $user;
		private $pass;
		private $db;
		private $link;
		
		
		
		# Magiques
		
		
		/*
		* @name: Constructeur
		* @param : paramètres de connexion : hote, utilisateur, mot de passe et base de données sur laquelle se connecter
		* @return : rien
		* @description : se connecte à l'hote puis à la base
		*/
		public function __construct ($host = 'localhost', $user = 'root', $pass = '', $db) {
			
			$this->host = $host;
			$this->user = $user;
			$this->pass = $pass;
			$this->db 	= $db;
			
			$this->link	= mysql_pconnect($this->host, $this->user, $this->pass);
			$Base = mysql_select_db($this->db, $this->link);
			
		}
		
				
		/*
		* @name: Destructeur
		* @param : rien
		* @return : rien
		* @description : ferme la connexion
		*/		
		public function __destruct () {	
			//mysql_close($this->link);
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
		public function Exists () {
			if($basetest = mysql_select_db($this->db, $this->link)) return true;
			else return false;
		}
		
		
		/*
		* @name: close
		* @param : rien
		* @return : rien
		* @description : ferme la connexion manuellement
		*/	
		public function Close () {
			mysql_close($this->link);
		}
		
		
		/*
		* @name: QueryAll
		* @param : La requête à exécuter
		* @return : Le tableau des résultats
		* @description : Execute une requête SQL SELECT
		*/
		public function QueryAll ($query) {
			
			// lit les enregistrements
			$req = mysql_query($query, $this->link) or die(mysql_error());
		   
			// tout dans un tableau
			for($i = 0; $tab[$i] = mysql_fetch_assoc($req); $i++) ;
		   
			// Je vire le dernier enreg, vu qu'il est tjs vide
			array_pop($tab);
			
			return $tab;
		}
		
		
		/*
		* @name: QueryRow
		* @param : La requête à exécuter
		* @return : La ligne retournée par sql dans un tableau d'une ligne
		* @description : Execute une requête SQL SELECT
		*/
		public function QueryRow ($query) {
			
			$tab = array();
			
			// lit les enregistrements
			$req = mysql_query($query, $this->link) or die(mysql_error());
		   
			$tab = mysql_fetch_row($req);
			
			return $tab;
		}
		
		
		/*
		* @name: QueryOne
		* @param : La requête à exécuter
		* @return : La valeur retournée par sql dans une variable (pas de tableau donc)
		* @description : Execute une requête SQL SELECT
		*/
		public function QueryOne ($query) {
	
			$tab = array();
	
			$req = mysql_query($query, $this->link) or die(mysql_error());
		   
			$tab = mysql_fetch_row($req);
			
			return $tab[0];
		}
		
		
		/*
		* @name: Execute
		* @param : La requête à exécuter
		* @return : Le nombre d'enregistrements affectés par la requête
		* @description : Execute une requête SQL action (insert, update, delete ...) 
		*/
		public function Execute ($query) {
			$result = mysql_query($query, $this->link);
			
			$NbResult = mysql_affected_rows();
			return $NbResult; 
		}
		
				
		// renvoie la liste des fonctions dispo
		public function GetFunctions () {
		}
		
		
		/*
		* @name: GetLastID
		* @param : aucun
		* @return : dernier id ajouté
		* @description : renvoie l'id automatique de la dernière requête d'ajout
		*/
		public function GetLastID () {
			return mysql_insert_id($this->link);
		}
		
		
		
		# SETTERS
		
		
		public function SetDatabase ($db) {
			$this->db 	= $db;
		}
		
		
		public function SetHost ($host) {
			$this->host 	= $host;
		}
		
		
		public function SetUser ($user) {
			$this->user 	= $user;
		}
		
		
		public function SetPass ($pass) {
			$this->pass 	= $pass;
		}
		
		
		
		# GETTERS
		public function GetLink () {
			return $this->link;
		}
		
		public function GetDatabase () {
			return $this->db;
		}
		
		
		public function GetHost () {
			return $this->host;
		}
		
		
		public function GetUser () {
			return $this->user;
		}
		
		
		public function GetPass () {
			return $this->pass;
		}

	}


