<?PHP	header("Content-Type:text/html; charset=iso-8859-15" ); 	// r�gle le probl�me d'encodage des caract�res	?>

<script type="text/javascript">	
	// init de la couleur de fond
	document.getElementById('conteneur').style.backgroundColor = "#fff";
</script>
	

<pre>

- il faut que marque_model soit unique
- il faudrait que le champ serial soit unique (ou nul). A bloquer soit au niveau de la db, 
	soit au niveau de l'injection dans la table via post_db_ocs.php

+ dump de la base de donn�es 
	- je songe � zipper le fichier
	- je dois recoder le dump en pear::mdb2
	- le dump de la base donn�es ocs chie � cause du time out � 30s

	
- faire un systeme pour la cr�ation du coll�ge la premi�re fois 
	- on demande l'insertion des donn�es puis apr�s validation, on cr�� automatiquement les salles obligatoires (stock + D3E)

	
- par defaut le champ salle_id passe � 1 et plus � null dans la table des materiels. 
	Ca permet de ranger directement le nouveau matos dans la salle stock cr��e lors du remplissage de la table de coll�ge

	


</pre>