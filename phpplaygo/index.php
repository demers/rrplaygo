<?

###########################################
# PHPPlayGo 0.1.4 is a program to play Go by the Web.
# PHPPlayGo 0.1.4 est un programme pour jouer à Go par le Web.
# Copyright (C) 2002, 2003, 2004 François-Nicola Demers fnd@techorange.ca
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
# SITE WEB
# ----------

# http://phpplaygo.webhop.org

# ----------
# PREREQUIS:
# ----------

# Ce logiciel fonctionne avec PHP 3 et 4.  Il génère du HTML non-vérifié.

# ------------
# INSTALLATION
# ------------

# Il suffit de créer un répertoire et de désarchiver tous les fichiers dans le répertoire.

###################################################################
###################################################################
# VARIABLES DE CONFIGURATION

$version_phpplaygo = "0.1.6";

$nom_fichier_index_php = "index_cp_php";

# Mot de passe pour autoriser la création d'une nouvelle partie de Go.
#$mp = '';

$courriel_envoyeur = "fnd@techorange.ca";
$courriel_destination = "fnd@techorange.ca";

$titre_courriel = "PHPPlayGo";

$fichier_commentaires = "commentaires.txt";

$goban = "go.txt";


if (! isset($titre_goban_form))
{

?>

      <!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
      <html>

      <head>
      <title>PHPPlayGo version 
<?
	 echo " $version_phpplaygo</title>";
?>
      </head>
      <body bgcolor="#ffcc33">
      <center><h1>PHPPlayGo</h1>

	 <img src="logo.png" alt="PHPPlayGo">

	 

      <p>Voici la liste des joutes:
<?
       # Déterminer la liste des sous-répertoires.
       $repertoire_courant = dir(".");
       # Parcourir l'ensemble des dossiers représentant des parties de Go.
       while ($nom_rep = $repertoire_courant->read())
       {
         if ($nom_rep != '.' && $nom_rep != '..' && filetype($nom_rep) == 'dir')
         {
           echo "<a href=\"$nom_rep/index.php\">$nom_rep</a> ";
         }
       }

?>

</p>

	 <p>Répondre au formulaire suivant pour créer une nouvelle joute Go:

    <FORM action="index.php" ENCTYPE="multipart/form-data" method="POST">

<?
  if ($mp != '')
  {
    echo "<P>Entrez le mot de passe d'autorisation: <input type=\"text\" name=\"mp_auto_form\" size=\"10\" maxlength=\"10\"></p>";
  }

?>

	 Taille du Goban: <SELECT NAME="taille_goban">
	 <OPTION>9</OPTION>
	 <OPTION>13</OPTION>
	 <OPTION SELECTED>19</OPTION>
	 </SELECT></P>

<P>Entrez le titre de la partie: <input type="text" name="titre_goban_form" size="30" maxlength="60"></p>

<P>Entrez le nom du joueur des pierres noires: <input type="text" name="joueur_noire_form" size="15" maxlength="30"></p>

<P>Entrez l'adresse courriel du joueur des pierres noires: <input type="text" name="joueur_noire_courriel_form" size="15" maxlength="30"></p>

<P>Entrez le nom du joueur des pierres blanches: <input type="text" name="joueur_blanche_form" size="15" maxlength="30"></p>

<P>Entrez l'adresse courriel du joueur des pierres blanches: <input type="text" name="joueur_blanche_courriel_form" size="15" maxlength="30"></p>

<p>Vous voulez l'affichage de l'heure de Greenwich?
<INPUT TYPE="checkbox" NAME="greenwich_form" VALUE="greenwich"></p>

	 <p>Entrez le mot de passe commun pour pouvoir jouer: <input type="password" name="mp_form" size="7" maxlength="7"></p>


       <input type="reset" value="Réinitialiser"></input>
       <INPUT TYPE="submit" VALUE="Envoyer"><p>

       </FORM>
</CENTER>

<?php

	 }
     else # le formulaire a été rempli.
     {

	  $titre_goban_form_mod = ereg_replace("'| |-|\+|\*|\^|`|~|\.|,|=|!|\?|\#|\@|\"|/|%|&|\(|\)|\[|\]|\{|\}", "", stripslashes($titre_goban_form));

	  if (is_dir($titre_goban_form_mod) || (isset($mp) && ($mp != $mp_auto_form)))
	    {
  ?>

      <!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
      <html>
      <body>
      <center><h1>Erreur de création</h1></center>

	 <p>Cette partie de Go existe déjà!  Revenez en arrière pour modifier et reprendre.</p>

	 <?php

	    }
	  else # Le dossier n'existe pas et on peut y aller de la création.
	    { 


              # Envoie d'un mail pour dire qu'un nouveau répertoire a été créé.
	      $entete_courriel = "From: $courriel_envoyeur\n";
	      $entete_courriel .= "X-Sender: <$courriel_envoyeur>\n";
	      $entete_courriel .= "X-Mailer: PHPPlayGo\n";
	      $entete_courriel .= "Return-Path: <$courriel_envoyeur>\n";
	      
	      $sujet_courriel .= "$titre_courriel: création d'une nouvelle partie $titre_goban_form_mod";

	      $message_courriel .= "PHPPlayGo vous informe qu'une nouvelle partie de Go ";
	      
	      $tmp1 = $_SERVER['REMOTE_ADDR'];
	      $tmp2 = $_SERVER['HTTP_USER_AGENT'];
	      $message_courriel .= "a été créé par $tmp1 ($tmp2).\n\n";
	      $message_courriel .= "Veuillez vérifier la validité de cette création.\n\n";

              $message_courriel .= "Voici l'URL d'accès: http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/" . $titre_goban_form_mod . "/index.php\n\n";

	      mail($courriel_destination, $sujet_courriel, $message_courriel, $entete_courriel);

	      mkdir("$titre_goban_form_mod", 0755);
	      
#	      copy($nom_fichier_index_php, "$titre_goban_form_mod/index.php");
              # Lecture de la totalité du fichier index.php
              $contenu_findex = file("$nom_fichier_index_php");

              # Ouverture du fichier en écriture.
              $findex = fopen("$titre_goban_form_mod/index.php", "w");

     fputs($findex, "<?\n");

     fputs($findex, "\$goban = \"$goban\";\n");

     $titre_goban_form_mod_tmp = stripslashes($titre_goban_form_mod);
     fputs($findex, "\$Titre = \"$titre_goban_form_mod_tmp\";\n");
     fputs($findex, "\$taille = '$taille_goban';\n");
     $joueur_noire_tmp = stripslashes($joueur_noire_form);
     fputs($findex, "\$joueur_noire_p = '$joueur_noire_tmp';\n");
     $joueur_blanche_tmp = stripslashes($joueur_blanche_form);
     fputs($findex, "\$joueur_blanche_p = '$joueur_blanche_tmp';\n");

     $joueur_noire_courriel_tmp = stripslashes($joueur_noire_courriel_form);
     fputs($findex, "\$courriel_noir = '$joueur_noire_courriel_tmp';\n");
     $joueur_blanche_courriel_tmp = stripslashes($joueur_blanche_courriel_form);
     fputs($findex, "\$courriel_blanc = '$joueur_blanche_courriel_tmp';\n");

     if ($greenwich_form == "greenwich")
       # Activation.
       fputs($findex, "\$greenwich = 1;\n");
     else
       # Désactivation.
       fputs($findex, "\$greenwich = 0;\n");

     fputs($findex, "\$mot_passe = '$mp_form';\n");

     fputs($findex, "\$nom_cookie = 'phpplaygo$titre_goban_form_mod';\n");

      $url = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/$titre_goban_form_mod/index.php";

     fputs($findex, "\$url = '$url';\n");


     fputs($findex, "?>\n");

     for ($index = 0; $index < count($contenu_findex); $index++)
       {
	 fputs($findex, $contenu_findex[$index]);
       }
     fputs($findex, "\n");

     fclose($findex);

     $findex = fopen("$titre_goban_form_mod/$fichier_commentaires", "w");
     fclose($findex);

     copy($goban, "$titre_goban_form_mod/$goban");
	  ?>

      <!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<?php
  echo "<META HTTP-EQUIV=\"refresh\" CONTENT=\"10; URL=$url\">\n";
?>
      <html>
      <body bgcolor="#ffcc33">
      <center><h1>PHPPlayGo</h1>

	 <img src="logo.png" alt="PHPPlayGo">

      <center><h1>C'est créé!</h1></center>

      <p>Vous trouverez la partie à l'adresse suivante: 
<?php

	 echo "<b><a href=\"$url\">$url</a></b>";

?>

        </p>

	    <p>Dans 10 secondes, vous serez redirigez vers cette partie de Go et vous pourrez jouer votre premier coup et alors annoncer votre partie à votre adversaire.</p>

<?php

	    }


}



####
# PIED DE LA PAGE.

?>

<center>
<p><font size="-1"><a href="http://phpplaygo.webhop.org">PHPPlayGo</a>, version 0.1.6, licence <a href="http://www.gnu.org/copyleft/gpl.html">GPL</a> (logiciel libre) <a href="mailto:mailto:fnd@techorange.ca">François-Nicola Demers</a> &copy; 2002, 2003, 2004.</p>


Les logiciels libres comme celui-ci sont des logiciels qui garantissent quatre
<i>libertés fondamentales</i>: la liberté de les utiliser, de les
  redistribuer, de les modifier et de diffuser les versions
  modifiées. Grâce à cela, dans le monde entier, des utilisateurs
  peuvent traduire, améliorer et adapter leurs logiciels pour leurs
  propres besoins. Ainsi, le logiciel libre contribue à assurer la
  protection des cultures locales, le multilinguisme, le développement
  et la conservation de l'information.  Encouragez l'utilisation des logiciels libres.</font></center>

</body>
</html>