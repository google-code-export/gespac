<?PHP


	class Log {
	
		# Propriétés
		
		private $path;
		private $handle;
		
		
		# Magiques
	
	
		/*
		* @name: Constructeur
		* @param : le chemin du fichier à créer/modifier. Si on ne colle pas de paramètres, le fichier est créé dans le même dossier sous le nom log.txt
		* @return : rien
		* @description : Créer une ressource pour modifier ou créer un fichier.
		*/
		public function __construct ($path = "./log.txt") {
			
			$this->path = $path;
			
			// Ouverture du fichier
			$this->handle = fopen($this->path, 'a+');
		}
		
		
		/*
		* @name: Destructeur
		* @param : rien
		* @return : rien
		* @description : ferme le fichier.
		*/		
		public function __destruct () {
			fclose($this->handle);
		}
		
		
		
		# Méthodes
		
		
		/*
		* @name: Close
		* @param : rien
		* @return : rien
		* @description : ferme manuellement le fichier
		*/
		public function Close () {
			fclose($this->handle);
		}
		
		/*
		* @name: Insert
		* @param : le texte à insérer
		* @return : TRUE si on a pu écrire, FALSE sinon
		* @description : insert un texte dans un fichier avec date et mise en forme utf8
		*/
		public function Insert ($text) {
			if ( !fwrite($this->handle, date("Ymd His") . " " . utf8_decode($text) ."\n") ) {
				return false; // On arrive pas à écrire
			}
			else {
				return true; // On arrive à écrire
			}
		}
		
		/*
		* @name: Delete
		* @param : rien
		* @return : rien
		* @description : efface le fichier
		*/
		public function Delete () {
			// Je ferme le fichier
			fclose($this->handle);
			
			// J'efface le fichier
			unlink ($this->path);
		}
		
		
		/*
		* @name: Help
		* @param : rien
		* @return : rien
		* @description : affiche la liste des fonctions et leur description
		*/
		public function Help () {
			
			echo "<b>Close()</b> : ferme manuellement le fichier.<br>";
			echo "<b>Insert(string)</b> : insert un texte dans un fichier avec date et mise en forme utf8.<br>";
			echo "<b>Delete()</b> : efface le fichier.<br>";
			echo "<b>Help()</b> : affiche la liste des fonctions et leur description.<br>";

		}
		
		
		

		# GETTERS

		public function GetPath () {
			return $this->path;	
		}
		
		
		# SETTERS
		
		public function SetPath ($path) {
			$this->path = $path;	
		}
		
	
	}
