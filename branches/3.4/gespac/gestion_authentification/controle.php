<?php
session_start() ;
// on vérifie si l'utilisateur est identifié
if (!isset( $_SESSION['login'])) {

  // la variable de session n’existe pas,
  // donc l'utilisateur n'est pas authentifié
  // On redirige sur la page permettant de s’authentifier
  header("Location: ./../index.php");
  // on arrête l'exécution
  exit();
} 
?> 