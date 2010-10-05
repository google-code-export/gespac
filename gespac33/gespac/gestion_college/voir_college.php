<script type="text/javascript">	
	// init de la couleur de fond
	$('conteneur').style.backgroundColor = "#fff";
</script>

	
<?PHP

	header("Content-Type:text/html; charset=iso-8859-1" ); 	// r�gle le probl�me d'encodage des caract�res

	/* 
		fichier de creation / modif / du college

	*/
	
	
	include ('../includes.php');	// fichier contenant les fonctions, la config pear, les mdp databases ...

	
	// adresse de connexion � la base de donn�es	
	$dsn_gespac	= 'mysql://'. $user .':' . $pass . '@localhost/gespac';	
	
	// cnx � la base de donn�es GESPAC
	$db_gespac 	= & MDB2::factory($dsn_gespac);
	
	// stockage des lignes retourn�es par sql dans un tableau nomm� avec originalit� "array" (mais "tableau" peut aussi marcher)
	$college_info = $db_gespac->queryAll ( "SELECT clg_uai, clg_nom, clg_ati, clg_ati_mail, clg_adresse, clg_cp, clg_ville, clg_tel, clg_fax, clg_site_web, clg_site_grr FROM college;" );

	$clg_uai 		= $college_info [0][0];
	$clg_nom 		= $college_info [0][1];
	$clg_ati 		= $college_info [0][2];
	$clg_ati_mail 	= $college_info [0][3];
	$clg_adresse 	= $college_info [0][4];
	$clg_cp 		= $college_info [0][5];
	$clg_ville 		= $college_info [0][6];
	$clg_tel 		= $college_info [0][7];
	$clg_fax 		= $college_info [0][8];
	$clg_site_web 	= $college_info [0][9];
	$clg_site_grr 	= $college_info [0][10];
	
	
echo "<h3>Fiche d'informations du coll�ge $clg_nom</h3><br>
	<center>
			<table class='tablehover' width=450>

				<tr class='tr1'>
					<TD><B>UAI</B></TD>
					<TD>$clg_uai</TD>
				</tr>
			
				<tr class='tr2'>
					<TD><B>Nom du coll�ge</B></TD>
					<TD>$clg_nom</TD>
				</tr>
			
				<tr class='tr1'>
					<TD><B>Nom de l'ATI</B></TD>
					<TD>$clg_ati</TD>
				</tr>				
			
				<tr class='tr2'>
					<TD><B>Mail de l'ATI</B></TD>
					<TD><a href='mailto:$clg_ati_mail'>$clg_ati_mail</a></TD>
				</tr>
			
				<tr class='tr1'>
					<TD><B>Adresse du boll�ge</B></TD>
					<TD>$clg_adresse</TD>
				</tr>
			
				<tr class='tr2'>
					<TD><B>Code Postal</B></TD>
					<TD>$clg_cp</TD>
				</tr>
			
				<tr class='tr1'>
					<TD><B>Ville</B></TD>
					<TD>$clg_ville</TD>
				</tr>
				
				<tr class='tr2'>
					<TD><B>T�l�phone</B></TD>
					<TD>$clg_tel</TD>
				</tr>
			
				<tr class='tr1'>
					<TD><B>Fax</B></TD>
					<TD>$clg_fax</TD>
				</tr>
			
				<tr class='tr2'>
					<TD><B>Site web du coll�ge</B></TD>
					<TD><a href='http://$clg_site_web' target=_blank>http://$clg_site_web</a></TD>
				</tr>				
			
				<tr class='tr1'>
					<TD><B>Acc�s GRR</B></TD>
					<TD><a href='http://$clg_site_grr' target=_blank>http://$clg_site_grr</a></TD>
				</tr>	
				
			</table>
		</center>";
			
		
echo "<br><center><a href='#&id=$clg_uai' onclick=\"$('conteneur').load('gestion_college/form_college.php?id=$clg_uai');\" ><img src='img/modif_college.png' title='Modifier les informations du coll�ge'></a></center>";

?>