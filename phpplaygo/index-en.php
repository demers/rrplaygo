<?

###########################################
# PHPPlayGo 0.1.0-en is a program to play Go by the Web.
# Copyright (C) 2002 François-Nicola Demers FN_Demers@yahoo.ca
###########################################

# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.

# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.

# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
# http://www.gnu.org/copyleft/gpl.html
###########################################

# ----------
# PREREQUIS:
# ----------

# Ce logiciel fonctionne avec PHP 3 et 4.  Il génère du HTML.

# ------------
# INSTALLATION
# ------------

# Il suffit de créer un répertoire et de désarchiver tous les fichiers dans le répertoire.

# ------------
# INFORMATION:
# ------------

# ----------------
# NOTES DE VERSION
# ----------------

# La version 0.0.1 affiche le jeu et permet de cliquer pour ajouter une pierre
# de couleur en alternance.  Les coups sont enregistrés dans un fichier texte.  Permet
# d'enlever des pierres en cliquant dessus par deux fois.

# La version 0.0.2 offre un rafraichissement adéquat pour avoir la joute à jour.
# Elle permet de savoir quand le joueur précédent a joué.

# La version 0.0.3 offre des corrections et la date du dernier accès au jeu.

# La version 0.1.0 une nouvelle version d'enregistrement des données.
# Elle offre un formulaire de messages pour le joueur précédent.
# Les commentaires s'ajoutent à la fin du goban.  Le coup précédent s'affiche
# sur le goban de façon distinctive.  Il y a moyen de voir les coups précédents par un bouton
# suivant.  Les pierres capturées sont comptées et affichées.


# ---------------
# A FAIRE (TODO):
# ---------------

# La version 0.2.0 va lire un fichier XML pour y extraire les coups et les afficher.

# La version 0.3.0 sera une version complète qui offrera toutes les fonctionnalités nécessaires
# pour jouer.

# La version 0.3.0 va utiliser le format XML standard SGF pour exprimer les coups de Go.  Il
# y aura moyen d'ajouter des commentaires aux parties

# La version 0.4.0 va vérifier la validité des coups avant de les jouer.

# La version 0.5.0 va permettre le moyen de voir les coups précédents.

# La version 0.6.0 va permettre de faire rejouer la joute du début comme sur http://goproblems.com

# La version 0.7.0 va permettre de voir les numéros sur chaque coup pour savoir quand ils ont
# été joués.

# La version 1.0 va permettre de jouer contre l'ordinateur en appelant un programme Python
# qui analyse les coups et détermine le meilleur coup.  Peut-être que ça pourrait permettre de
# jouer contre GnuGo...


###########################################
# Définitions de certaines variables globales.

# Nom du fichier des coups précédents.
$goban = "go.txt";

$fichier_commentaires = "commentaires.txt";

# Variable de confirmation du coup.
$conf = 0;

#$taille = 9;
$taille = 19;

# Prochain coup: pierre noire.
$coup_prochain = 2;

$Titre = "Game of go: test";

# Nom du joueur de pierres noires.
$joueur_noire_p = "Player1";
$joueur_noire = "<i>$joueur_noire_p</i>";

# Nom du joueur de pierres blanches.
$joueur_blanche_p = "Player2";
$joueur_blanche = "<i>$joueur_blanche_p</i>";

# Création du tableau des pierres.
$pierres = array();

# Initialisation du tableaux des pierres.
#for ($i = 1; $i <= $taille; $i++)
#{
#  $pierres[$i] = array();
#}

# Initialisation du goban.
  for ($i = 1; $i <= $taille; $i++)
    {
      $pierres[$i] = array();
      for ($j = 1; $j <= $taille; $j++)
	{
	  $pierres[$i][$j] = 0;
	  
	}
    }

# Initialisation de la liste des coups.
$liste_coups = "";

# Le moment du dernier coup...
$derniereModif = filectime($goban);
$date_derniereModif = date("j/m H:i", $derniereModif);
  
# Le moment du dernier accès au Goban...
$dernierAcces = fileatime($goban);
$date_dernierAcces = date("j/m H:i", $dernierAcces);


###########################################
# Définition des fonctions.

function position($u, $v, $fond, $pierre)
{

  global $x, $y, $conf_x, $conf_y, $conf, $dernier_coup_x, $dernier_coup_y;

  echo "<TD><a href=\"index.php?x=$u&y=$v";

  if (isset($x) &&
      isset($y))
    {
      if (! isset($conf_x) &&
	  (! isset($conf_y)))
	{
	  echo "&conf_x=$x&conf_y=$y";
	}
    }
#  else
#    {
#      echo "&conf_x=$u&conf_y=$v";
#    }
  
  echo "\"><IMG STYLE=\"border-style: none\" ";

#  if ($pierres[$u][$v] == 0)
  if ($pierre == 0)
    {
      # Si le joueur a déjà cliqué à cet endroit...
      if (isset($x) && isset($y) && ($x == $u) && ($y == $v))
	{
	  echo "SRC=\"pierre.png\">";
	}
      else # Le joueur n'a cliqué nulle part.
	{
      	  echo "SRC=\"$fond\">";
	}
    }
  # Afficher la pierre noire.
  elseif ($pierre == 1)
    {
      if (($dernier_coup_x == $u) && ($dernier_coup_y == $v))
      {
        echo "SRC=\"noired.png\">";
      }
      else
      {
        echo "SRC=\"noire.png\">";
      }
    }
  # Afficher la pierre blanche.
  else # $pierre == 2
    {
      if (($dernier_coup_x == $u) && ($dernier_coup_y == $v))
      {
        echo "SRC=\"blanched.png\">";
      }
      else
      {
        echo "SRC=\"blanche.png\">";
      }
    }
  
  echo "</a></TD>";

}
##################################################################

# Ouverture du fichier des coups.
$fichierGo = fopen($goban, "r");

if (!$fichierGo)
{
  echo "Erreur de lecture du fichier de configuration!<p>";
}
else
{

  # Lecture du coup (noir = 1 ou blanc = 2)
#  $valeur = fgets($fichierGo, 255);
#  $coup_prochain = $valeur + 0;

  # Lecture du nombre de pierres capturés noires et blanches.
  $captures_noires = fgets($fichierGo, 255);
  $captures_noires = $captures_noires + 0;
  $captures_blanches = fgets($fichierGo, 255);
  $captures_blanches = $captures_blanches + 0;

  # Nombre de coups dans le fichier.
  $nombre_coups = fgets($fichierGo, 255);
  $nombre_coups = $nombre_coups + 0;

  # Vérification si on demande de voir un coup précédent.
  if (isset($cp))
    $nombre_coups_lus = $cp;
  else
    $nombre_coups_lus = $nombre_coups;

  for ($i = 1; $i <= $nombre_coups_lus; $i++)
#  while (!feof($fichierGo))
  {

    $ligne = fgets($fichierGo, 255);
    if (strlen($ligne) >= 3)
    {
       $lettre_x = substr($ligne, 0, 1);
       $lettre_y = substr($ligne, 1, 1);
       $couleur = substr($ligne, 2, 1);
       $coup_x = ord($lettre_x) - 64;
       $coup_y = ord($lettre_y) - 64;

       # On ajoute ce coup sur le goban.
       $pierres[$coup_x][$coup_y] = $couleur;

       # Changement de la couleur du prochain coup.
       if ($couleur != 0)
	 {
	   $coup_prochain = $couleur;

           # Trouver le dernier coup joué sur le goban.
	   $dernier_coup_x = $coup_x;
	   $dernier_coup_y = $coup_y;
	 }

    # Ajout du coup lu dans la liste des coups.
       $liste_coups = $liste_coups . "$lettre_x$lettre_y$couleur\n";
   }
  }

  fclose($fichierGo);

  # Changement de la couleur du prochain coup.
    $coup_prochain = ($coup_prochain % 2) + 1;

}


# Si l'usager a cliqué sur un coup...
if (isset($x) && isset($y) && (! isset($cp)))
{
  
  # S'il y a eu confirmation...
  if (isset($conf_x) &&
      isset($conf_y) &&
      ($x == $conf_x) &&
      ($y == $conf_y))
    {

      # Confirmation de l'enregistrement du coup.
      $conf = 1;

      $valeur = $pierres[$x][$y];
      if ($valeur == 0)
	{
	  # Le coup est à ajouter.
	  $pierres[$x][$y] = $coup;
          $liste_coups = $liste_coups . chr(64+$x) . chr(64+$y) . $coup_prochain . "\n";
	}
      else
	{
	  # Le coup est pour effacer.

          # Mettre à jour le nombre de pierres capturées.
          if ($valeur == 1)
              $captures_noires++;
          else
              $captures_blanches++;

	  $pierres[$x][$y] = 0;
          $liste_coups = $liste_coups . chr(64+$x) . chr(64+$y) . "0" . "\n";
	}

      # Incrément du nombre de coups.
      $nombre_coups = $nombre_coups + 1;

      $fichierGo = fopen($goban, "w");

      if (!$fichierGo)
      {
#        echo "Erreur d'écriture du fichier de configuration!<p>";
        echo "Writing error of the configuration file!<p>";
      }
      else
      {

	
	$valeur = $pierres[$x][$y];
	if ($valeur != 0)
	  {
            # Changement de la couleur du prochain coup.
            $coup_prochain = ($valeur % 2) + 1;
	  }


	# Enregistrement des données...
        fputs($fichierGo, "$captures_noires\n");
        fputs($fichierGo, "$captures_blanches\n");
        fputs($fichierGo, "$nombre_coups\n");
	fputs($fichierGo, $liste_coups);
	fclose($fichierGo);
      }
      
    }
	
}

?>

<html>
<head>
<STYLE TYPE="text/css">
<!--
-->
</STYLE>

<?

# Rafraichissement à venir...
if (isset($x) && isset($y) && (! isset($cp)))
{
  if (isset($conf_x) && isset($conf_y))
  {
    # Rafraichissement après 3 secondes.
    echo "<META HTTP-EQUIV=\"refresh\" CONTENT=\"3; URL=index.php\">\n";
  }
  else
  {
    # Rafraichissement après 60 secondes.
    echo "<META HTTP-EQUIV=\"refresh\" CONTENT=\"60; URL=index.php\">\n";
  }
}
else
{
  # Rafraichissement à tous les 30 minutes.
  echo "<META HTTP-EQUIV=\"refresh\" CONTENT=\"1800; URL=index.php\">\n";
}

    


# Entête du document HTML.
if ($coup_prochain == 1)
{
  print "<title>$joueur_noire_p to play!</title>";
}
else
{
  print "<title>$joueur_blanche_p to play!</title>";
}

?>

</head>
<body BGcolor=#FFFFFF TEXT=#000000>
<CENTER>

<?

# Titre au formulaire.
if (isset($cp))
     echo "<h1>Last play number $cp</h1>";
else
     echo "<h1>$Titre</h1>";

if ($conf)
{
#echo "<p>Merci pour la confirmation.  Le changement a été enregistré.<br><b>Ne pas rafraichir la page par le bouton du fureteur!</b><br>Rafraichissement automatique dans 3 secondes...</p>";
echo "<p>Thank you to confirm.  The change has been saved.<br><b>Do not refresh the page by the browser button!</b><br>Automatic refreshing in 3 seconds...</p>";
}

if (isset($x) && isset($y) && (! $conf) && (! isset($conf_x)) && (! isset($conf_y)))
{
  $valeur = $pierres[$x][$y];
  if ($valeur == 0)
    {
#      echo "<p><font color=\"green\"><b>Veuillez confirmer votre coup en cliquant sur la pièce grise.</b></font></p>";
      echo "<p><font color=\"green\"><b>Please confirm your play by clicking on the green stone.</b></font></p>";
    }
  else
    {
#      echo "<p><font color=\"red\"><b>Veuillez confirmer la suppression de la pierre en cliquant au même endroit.</b><br/>Notez qu'un des deux compteurs de pierres capturées sera mis à jour.</font></p>";
      echo "<p><font color=\"red\"><b>Please confirm the captured stone by clicking on the same stone.</font></p>";
    }

}

# Le moment du dernier coup...
$dernierAcces = filectime($goban);
$date_dernierAccess = date("j/m H:i", $dernierAcces);
$date_actuelle = date("j/m H:i");

if (! isset($cp))
{

if (! (isset($x) && isset($y)))
{

# Impression du nom du joueur.
#echo "<p><h2>Prochain coup joué par: ";
echo "<p><h2>Next play by: ";
if ($coup_prochain == 1)
{
  echo "<font color=\"green\">$joueur_noire</font>";
  echo ".</h2></p>";
#  echo "<p>$joueur_blanche a joué son dernier coup (no. $nombre_coups) le $date_derniereModif.<br>";
  echo "<p>$joueur_blanche has played the last time (number $nombre_coups) the $date_derniereModif.<br>";

  if ($date_dernierAcces != $date_actuelle)
#    echo "<font size=\"-1\">La dernière lecture du Goban du Web s'est fait le $date_dernierAcces.</font></p>";
    echo "<font size=\"-1\">The Goban has been checked the last $date_dernierAcces.</font></p>";
}
else
{
  echo "<font color=\"green\">$joueur_blanche</font>";
  echo ".</h2></p>";
#  echo "<p>$joueur_noire a joué son dernier coup (no. $nombre_coups) le $date_derniereModif.<br>";
  echo "<p>$joueur_noire has played the last time (number $nombre_coups) the $date_derniereModif.<br>";
  if ($date_dernierAcces != $date_actuelle)
#    echo "<font size=\"-1\">La dernière lecture du Goban du Web s'est fait le $date_dernierAcces.</font></p>";
    echo "<font size=\"-1\">The Goban has been checked the last $date_dernierAcces.</font></p>";
}

} # if (! (isset($x) && isset($y))

# Impression de la couleur du prochain coup.
#echo "<p><b>La couleur de la prochaine pierre est ";
echo "<p><b>The color of the next stone is  ";
if ($coup_prochain == 1)
{
  #echo "<font color=\"green\">noire</font>";
  echo "<img src=\"cnoire.png\" width=\"20\" alt=\"noire\" align=\"middle\">";
}
else
{
  #echo "<font color=\"green\">blanche</font>";
  echo "<img src=\"cblanche.png\" width=\"20\" alt=\"blanche\" align=\"middle\">";
}

echo "</b></p>";

#echo"<p>Captures pierres noires: <b><font color=\"green\">$captures_noires</font></b><br/>  Captures pierres blanches: <b><font color=\"green\">$captures_blanches</font></b></p>";
echo"<p>Black stones captured: <b><font color=\"green\">$captures_noires</font></b><br/>  White stones captured: <b><font color=\"green\">$captures_blanches</font></b></p>";

if (isset($comment))
{
  # Enregistrement du commentaire dans le fichier des commentaires.
  $fichierCom = fopen($fichier_commentaires, "w");
  fputs($fichierCom, "$comment");
  fclose($fichierCom);
}

$derniereModif_commentaires = filectime($fichier_commentaires);
$date_derniereModif_commentaires = date("j/m H:i", $derniereModif_commentaires);
# Retourne un tableau de commentaires.
$commentaires = file($fichier_commentaires);
$tout_commentaire = implode($commentaires, "");

if (strlen($tout_commentaire) > 0)
{
  # Impression des commentaires actuels.
  echo "<p><table frame=\"box\" border=\"1\" hspace\"80%\"><tr>";
#  echo "<th>Dernier commentaire écrit le $date_derniereModif_commentaires</th>";
  echo "<th>Last comment written the $date_derniereModif_commentaires</th>";
  echo "</tr><tr align=\"center\"><td>";
  echo "$tout_commentaire";
  echo "</td></tr></table></p>";
}
else
{
  echo "<p><font size=\"-1\">No comment.</font></p>";
}

?>

<font size="-1">This page will be refreshed automatically each 30 minutes.  To refresh now, <a href="index.php">click here</a>.</font>

<?

} # if (! isset($cp))
else
{
#  echo "Pour revenir au dernier coup, <a href=\"index.php\">cliquez ici</a>.</font>";
  echo "To go back to last play, <a href=\"index.php\">click here</a>.</font>";
}



if (isset($cp))
{
    $cp = $cp - 1;
}
else
{
  $cp = $nombre_coups - 1;
}

#echo "<p>Pour voir la configuration précédente (no. $cp) du Goban, <a href=\"index.php?cp=";
echo "<p>To go back to previous configuration (number $cp) of the Goban, <a href=\"index.php?cp=";
echo "$cp\">click here</a>.</p>";
    

?>

<p>

<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0>

<TR>
<?

# Impression d'une case vide.
echo "<td></td>";

# Impression des lettres du dessus.
for ($i = 1; $i <= $taille; $i++)
{
  $abcisse = chr(64+$i);
  echo "<td align=\"center\"><font size=\"-1\">$abcisse</font></td>";
}

# Fin de la ligne des lettres.
echo "</tr>\n<tr>";

echo "<td align=\"right\"><font size=\"-1\">1</font></td>";

##################################################################
# Impression de la position (1,1) du tableau.
position(1, 1, "Ghtle.png", $pierres[1][1]);
#      echo "SRC=\"Ghtle.png\">";

##################################################################


for ($i = 2; $i < $taille; $i++)
{

##################################################################
# Impression des position (1, 2), ..., (1, $taille-1).
#echo "<TD><a href=\"index.php?x=1&y=$i\"><IMG STYLE=\"border-style: none\" SRC=\"Ghte.png\"></a></TD>";
  position(1, $i, "Ghte.png", $pierres[1][$i]);
##################################################################

}

##################################################################
# Impression de la position (1, $taille).
#echo "<TD><a href=\"index.php?x=1&y=$taille\"><IMG STYLE=\"border-style: none\" SRC=\"Ghtre.png\"></a></TD>";
position(1, $taille, "Ghtre.png", $pierres[1][$taille]);
##################################################################

?>
</TR>
<TR>
<?

# Impression des chiffres à la droite du goban.
for ($i = 2; $i < $taille; $i++)
{

echo "<td align=\"right\"><font size=\"-1\">$i</font></td>";

##################################################################
# Impression des positions (2, 1), (3, 1), ..., ($taille-1, 1)
#echo "<TD><a href=\"index.php?x=$i&y=1\"><IMG STYLE=\"border-style: none\" SRC=\"Ghle.png\"></a></TD>";
  position($i, 1, "Ghle.png", $pierres[$i][1]);
##################################################################

for ($j = 2; $j < $taille; $j++)
{

##################################################################
# Impression des positions (2, 2), (2, 3), ..., ($taille -1, $taille -1)
#echo "<TD><a href=\"index.php?x=$i&y=$j\"><IMG STYLE=\"border-style: none\" SRC=\"Ghe.png\"></a></TD>";
  position($i, $j, "Ghe.png", $pierres[$i][$j]);
##################################################################

}

##################################################################
# Impression des positions (2, $taille), (3, $taille), ..., ($taille -1, $taille).
#echo "<TD><a href=\"index.php?x=$i&y=$taille\"><IMG STYLE=\"border-style: none\" SRC=\"Ghre.png\"></a></TD></TR>";
 position($i, $taille, "Ghre.png", $pierres[$i][$taille]); 
 echo "</TR>";
##################################################################

}

echo "<TR>";

# Impression du dernier chiffre.
echo "<td align=\"right\"><font size=\"-1\">$taille</font></td>";

##################################################################
# Impression de la position ($taille, 1).
#echo "<TR><TD><a href=\"index.php?x=$taille&y=1\"><IMG STYLE=\"border-style: none\" SRC=\"Ghble.png\"></a></TD>";
position($taille, 1, "Ghble.png", $pierres[$taille][1]);
##################################################################

for ($i = 2; $i < $taille; $i++)
{

##################################################################
# Impression des positions ($taille, 2), ..., ($taille, $taille).
#echo "<TD><a href=\"index.php?x=$taille&y=$i\"><IMG STYLE=\"border-style: none\" SRC=\"Ghbe.png\"></a></TD>";
  position($taille, $i, "Ghbe.png", $pierres[$taille][$i]);
##################################################################

}

##################################################################
# Impression de la position ($taille, $taille).
#echo "<TD><a href=\"index.php?x=$taille&y=$taille\"><IMG STYLE=\"border-style: none\" SRC=\"Ghbre.png\"></a></TD></TR>";
position($taille, $taille, "Ghbre.png", $pierres[$taille][$taille]);
echo "</TR>";
##################################################################

?>
</TABLE>

<form action="index.php" method="post">

New comment<br> <textarea name="comment" rows="5" cols="60" colomns="40"></textarea><p>

<INPUT TYPE="submit" VALUE="Submit">


</form>

</CENTER>
<center><p><a href="http://www.php.net"><img STYLE="border-style: none" src="php3_logo.png"></a></p>
<p><font size="-1"><a href="http://savannah.gnu.org/projects/phpplaygo/">PHPPlayGo</a>, version 0.1.0, license <a href="http://www.gnu.org/copyleft/gpl.html">GPL</a> (open source) <a href="mailto:mailto:FN_Demers@yahoo.ca">François-Nicola Demers</a> &copy; 2002.</p>
</font></center>

</body>
</html>

