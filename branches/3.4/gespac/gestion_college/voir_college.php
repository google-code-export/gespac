<?PHP
session_start();

	/* 
		fichier de creation / modif / du college

	*/


?>

<!--	DIV target pour Ajax	-->
<div id="target"></div>

<script type="text/javascript">	
	// init de la couleur de fond
	$('conteneur').style.backgroundColor = "#fff";
	
</script>

	
<?PHP

	header("Content-Type:text/html; charset=iso-8859-1" ); 	// règle le problème d'encodage des caractères
	
	include ('../includes.php');	// fichier contenant les fonctions, la config pear, les mdp databases ...

	// si le grade du compte est root, on donne automatiquement les droits d'accès en écriture. Sinon, on teste si le compte a accès à la page.
	$E_chk = ($_SESSION['grade'] == 'root') ? true : preg_match ("#E-08-01#", $_SESSION['droits']);
	
	
	// cnx à la base de données GESPAC
	$con_gespac 	= new Sql ($host, $user, $pass, $gespac);
	
	// stockage des lignes retournées par sql dans un tableau nommé avec originalité "array" (mais "tableau" peut aussi marcher)
	$college_info = $con_gespac->QueryRow ( "SELECT clg_uai, clg_nom, clg_ati, clg_ati_mail, clg_adresse, clg_cp, clg_ville, clg_tel, clg_fax, clg_site_web, clg_site_grr FROM college;" );

	$clg_uai 		= stripslashes($college_info [0]);
	$clg_nom 		= stripslashes($college_info [1]);
	$clg_ati 		= stripslashes($college_info [2]);
	$clg_ati_mail 	= stripslashes($college_info [3]);
	$clg_adresse 	= stripslashes($college_info [4]);
	$clg_cp 		= $college_info [5];
	$clg_ville 		= stripslashes($college_info [6]);
	$clg_tel 		= $college_info [7];
	$clg_fax 		= $college_info [8];
	$clg_site_web 	= stripslashes($college_info [9]);
	$clg_site_grr 	= stripslashes($college_info [10]);
	
	
echo "<h3>Fiche d'informations du collège $clg_nom</h3><br>
	<center>
			<table class='tablehover' width=450>

				<tr class='tr1'>
					<TD><B>UAI</B></TD>
					<TD>$clg_uai</TD>
				</tr>
			
				<tr class='tr2'>
					<TD><B>Nom du collège</B></TD>
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
					<TD><B>Adresse du collège</B></TD>
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
					<TD><B>Téléphone</B></TD>
					<TD>$clg_tel</TD>
				</tr>
			
				<tr class='tr1'>
					<TD><B>Fax</B></TD>
					<TD>$clg_fax</TD>
				</tr>
			
				<tr class='tr2'>
					<TD><B>Site web du collège</B></TD>
					<TD><a href='http://$clg_site_web' target=_blank>http://$clg_site_web</a></TD>
				</tr>				
			
				<tr class='tr1'>
					<TD><B>Accès GRR</B></TD>
					<TD><a href='http://$clg_site_grr' target=_blank>http://$clg_site_grr</a></TD>
				</tr>	
				
			</table>
		</center>";
			
if ($E_chk)	
	echo "<br><center><a href='#&id=$clg_uai' onclick=\"$('conteneur').load('gestion_college/form_college.php?id=$clg_uai');\" ><img src='img/write.png' title='Modifier les informations du collège'></a></center>";

?>
