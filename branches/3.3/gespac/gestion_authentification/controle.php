<?php
session_start() ;
// on v�rifie si l'utilisateur est identifi�
if (!isset( $_SESSION['login'])) {

  // la variable de session n�existe pas,
  // donc l'utilisateur n'est pas authentifi�
  // On redirige sur la page permettant de s�authentifier
  header("Location: ./../index.php");
  // on arr�te l'ex�cution
  exit();
} 
?> 