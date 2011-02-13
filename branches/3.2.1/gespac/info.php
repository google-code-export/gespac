<?PHP	header("Content-Type:text/html; charset=iso-8859-15" ); 	// règle le problème d'encodage des caractères	?>

<script type="text/javascript">	
	// init de la couleur de fond
	document.getElementById('conteneur').style.backgroundColor = "#fff";
</script>
	

<pre>

- il faut que marque_model soit unique
- il faudrait que le champ serial soit unique (ou nul). A bloquer soit au niveau de la db, 
	soit au niveau de l'injection dans la table via post_db_ocs.php

+ dump de la base de données 
	- je songe à zipper le fichier
	- je dois recoder le dump en pear::mdb2
	- le dump de la base données ocs chie à cause du time out à 30s

	
- faire un systeme pour la création du collège la première fois 
	- on demande l'insertion des données puis après validation, on créé automatiquement les salles obligatoires (stock + D3E)

	
- par defaut le champ salle_id passe à 1 et plus à null dans la table des materiels. 
	Ca permet de ranger directement le nouveau matos dans la salle stock créée lors du remplissage de la table de collège

	


</pre>