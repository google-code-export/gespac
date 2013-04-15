<?PHP
session_start();

	#fichier de creation / modif / du college

	
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

?>


<div class="entetes" id="entete-college">	

	<span class="entetes-titre">FICHE COLLEGE<img class="help-button" src="<?PHP echo ICONSPATH . "info.png";?>"></span>
	<div class="helpbox">C'est la fiche d'identité du collège.</div>

	<span class="entetes-options">
		<span class="option"><?PHP if ( $E_chk ) echo "<a href='gestion_college/form_college.php?height=450&width=640&id=$clg_uai' rel='slb_college' title=\"Modifier la fiche collège\"><img src='" . ICONSPATH . "modif1.png'></a>";?></span>
	</span>

</div>

<div class="spacer"></div>

	
<?PHP
		
echo "
	<center>
			<table class='tablehover'>

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
			
?>


<script type="text/javascript">
	
	window.addEvent('domready', function(){
		SexyLightbox = new SexyLightBox({color:'black', dir: 'img/sexyimages', find:'slb_college'});
	});
</script>
