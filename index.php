<?php

$host = 'localhost';
$dbname = 'bibliotheque';
$user = 'root';
$password = '';

$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des emprunts</title>
</head>

<body>

    <h1>Gestion de la bibliothèque</h1>

    <!-- --------------- SECTION LIVRES DÉBUT --------------- -->
    <div class="livre">

        <h2>- Les Livres -</h2>

        <a href="?page=allBooks">
            <h3>Voir tous les livres</h3>
        </a>

        <!-- VOIR TOUS LES LIVRES -->
        <?php

        if (isset($_GET['page']) && $_GET['page'] == 'allBooks') {

            $sqlAllBooks = "SELECT livre.id_livre, livre.titre, ecrivain.prenom_ecrivain, ecrivain.nom_ecrivain, livre.disponibilite
                    FROM livre
                    JOIN ecrivain ON livre.id_ecrivain = ecrivain.id_ecrivain
                    ORDER BY livre.titre";  // PERMET DE TRIER PAR ORDRE ALPHABETIQUE

            $stmtAllBooks = $pdo->query($sqlAllBooks);
            $livres = $stmtAllBooks->fetchAll(PDO::FETCH_ASSOC);

            foreach ($livres as $key => $value) {
                $idLivreASupprimer = $value['id_livre'];

                echo "<strong>" . htmlspecialchars($value['titre']) . "</strong><br>";
                echo "Auteur : " . htmlspecialchars($value['prenom_ecrivain']) . " " . htmlspecialchars($value['nom_ecrivain']) . "<br>";
                if ($value['disponibilite'] == 1) {
                    echo "Disponible" . "<br><br>";
                } else {
                    echo "Non disponible" . "<br><br>";
                }

                // LIEN DE MODIFICATION
                echo '<a href="?id=' . $idLivreASupprimer . '">Modifier</a><br>';

                // INITIALISATION DU BOUTON DE SUPPRESSION QUI NECESSITE UN FORM
                echo '<form method="post">';
                echo "<input type='hidden' name='idDelete' value='$idLivreASupprimer'>";

                echo "<input type='submit' name='submitDeleteBook' value='Supprimer'><br><br>";
                echo "</form>";

                if (isset($_POST['submitDeleteBook'])) {
                    $idDeleteBook = $_POST['idDelete'];

                    $sqlDeleteBook = "DELETE FROM `livre` WHERE id_livre = $idDeleteBook";
                    $stmtDeleteBook = $pdo->prepare($sqlDeleteBook);
                    $stmtDeleteBook->execute();

                    echo "Le livre a bien été supprimé.<br><br>";
                }
            }
        }

        // INITIALISATION DU FORMULAIRE DE MODIFICATION
        if (isset($_GET['id'])) {
            $idLivre = $_GET['id'];

            $sqlIdLivre = "SELECT * FROM `livre` WHERE id_livre = '$idLivre'";
            $stmtIdLivre = $pdo->prepare($sqlIdLivre);
            $stmtIdLivre->execute();

            $resultsIdLivre = $stmtIdLivre->fetchAll(PDO::FETCH_ASSOC);

            echo '<form method="POST">
            <label>Id</label>
            <br>
            <input type="text" name="idLivreUpdate" value="' . $resultsIdLivre[0]['id_livre'] . '">
            <br>
            <br>

            <label>Titre</label>
            <br>
            <input type="text" name="titreLivreUpdate" value="' . $resultsIdLivre[0]['titre'] . '">
            <br>
            <br>

    

            <label>Année</label>
            <br>
            <input type="text" name="anneeLivreUpdate" value="' . $resultsIdLivre[0]['annee'] . '">
            <br>
            <br>

            <input type="submit" name="submitLivreUpdate" value="Mettre à jour">
            </form>';
        }

        // REQUETE DE MODIFICATION EN CLIQUANT SUR LE BOUTON METTRE A JOUR
        if (isset($_POST['submitLivreUpdate'])) {
            $idLivreUpdate = $_POST['idLivreUpdate'];
            $nomLivreUpdate = $_POST['titreLivreUpdate'];
            $anneeLivreUpdate = $_POST['anneeLivreUpdate'];

            $sqlLivreUpdate = "UPDATE `livre` SET `titre`='$nomLivreUpdate', `disponibilite`='[value-3]',`annee`='[value-4]',`id_ecrivain`='[value-5]',`id_genre`='[value-6] WHERE id_livre = '$idLivreUpdate'";

            $stmtLivreUpdate = $pdo->prepare($sqlLivreUpdate);
            $stmtLivreUpdate->execute();

            echo "Livre modifié avec succès !";
        }

        ?>


        <a href="?page=addBook">
            <h3>Ajouter un livre</h3>
        </a>


        <!-- AJOUTER DES LIVRES -->
        <?php
        if (isset($_GET['page']) && $_GET['page'] == 'addBook') {
            $sqlGenre = "SELECT * FROM `genre` ORDER BY nom_genre";
            $stmtGenre = $pdo->prepare($sqlGenre);
            $stmtGenre->execute();

            $resultsGenre = $stmtGenre->fetchAll(PDO::FETCH_ASSOC);

            $sqlEcrivain = "SELECT * FROM `ecrivain` ORDER BY nom_ecrivain";
            $stmtEcrivain = $pdo->prepare($sqlEcrivain);
            $stmtEcrivain->execute();

            $resultsEcrivain = $stmtEcrivain->fetchAll(PDO::FETCH_ASSOC);

            echo '<form method="POST">
            <label for="titre">Titre :</label>
            <br>
            <input type="text" name="titre" id="titre" required>
            <br>
            <br>

            <label for="id_ecrivain">Auteur :</label>
            <br>
            <select name="id_ecrivain" required>';

            foreach ($resultsEcrivain as $key => $value) {
                echo "<option value='" . $value['id_ecrivain'] . "'>" . $value['prenom_ecrivain'] . "  " . $value['nom_ecrivain'] . "</option>";
            }

            echo '</select>
            <br>
            <br>

            <label for="annee">Année :</label>
            <br>
            <input type="number" name="annee" id="annee" required>
            <br>
            <br>

            <label for="genre">Genre :</label>
            <br>
            <select name="genre" required>';

            foreach ($resultsGenre as $key => $value) {
                echo "<option value='" . $value['id_genre'] . "'>" . $value['nom_genre'] . "</option>";
            }

            echo '</select>
            <br>
            <br>

            <label for="disponibilite">Disponible :</label>
            <input type="checkbox" name="disponibilite" id="disponibilite">
            <br>
            <br>

            <input type="submit" name="submitAdd" value="Ajouter">

        </form>';
        }

        // SI ON CLIQUE SUR SUBMIT ALORS LES DONNEES SONT ENVOYEES EN BDD
        if (isset($_POST['submitAdd'])) {
            $titreAdd = $_POST['titre'];
            $auteurAdd = $_POST['id_ecrivain'];
            $anneeAdd = $_POST['annee'];
            $genreAdd = $_POST['genre'];

            if (isset($_POST['disponibilite'])) {
                $disponibiliteAdd = 1; // true
            } else {
                $disponibiliteAdd = 0; // false
            }

            $sqlAdd = "INSERT INTO `livre` (`titre`, `disponibilite`, `annee`, `id_ecrivain`, `id_genre`) VALUES (?,?,?,?,?)";

            $stmtAdd = $pdo->prepare($sqlAdd);
            $stmtAdd->execute([$titreAdd, $disponibiliteAdd, $anneeAdd, $auteurAdd, $genreAdd]);

            echo "Livre ajouté avec succès !";
        }

        ?>

    </div>
    <!-- --------------- SECTION LIVRES FIN --------------- -->

    <hr>

    <!-- --------------- SECTION ÉCRIVAINS DÉBUT --------------- -->
    <div class="ecrivains">
        <h2>- Les Écrivains -</h2>

        <a href="?page=allAuthors">
            <h3>Voir tous les auteurs</h3>
        </a>

        <!-- VOIR TOUS LES ECRIVAINS -->
        <?php

        if (isset($_GET['page']) && $_GET['page'] == 'allAuthors') {

            // TRAITEMENT DU FORMULAIRE DE SUPPRESSION
            if (isset($_POST['submitDeleteEcrivain'])) {
                $idDeleteEcrivain = $_POST['idDeleteEcrivain'];

                $sqlDeleteEcrivain = "DELETE FROM `ecrivain` WHERE id_ecrivain = $idDeleteEcrivain";
                $stmtDeleteEcrivain = $pdo->prepare($sqlDeleteEcrivain);
                $stmtDeleteEcrivain->execute();

                echo "L'auteur a bien été supprimé.<br><br>";
            }

            // ON RÉCUPÈRE TOUS LES ÉCRIVAINS AVEC LEURS LIVRES (S'ILS EN ONT)
            $sqlAllAuthors = "SELECT 
        ecrivain.id_ecrivain, 
        ecrivain.nom_ecrivain, 
        ecrivain.prenom_ecrivain, 
        livre.titre
        FROM ecrivain
        LEFT JOIN livre ON ecrivain.id_ecrivain = livre.id_ecrivain 
        ORDER BY ecrivain.nom_ecrivain, livre.titre";

            $stmtAllAuthors = $pdo->query($sqlAllAuthors);
            $resultsAllAuthors = $stmtAllAuthors->fetchAll(PDO::FETCH_ASSOC);

            $dernierIdAuteur = ""; // PERMET DE SUIVRE L'ID DE L'AUTEUR PRÉCÉDENT

            foreach ($resultsAllAuthors as $key => $value) {
                $idEcrivainActuel = $value['id_ecrivain'];


                // NOUVEL AUTEUR SI L'ID CHANGE
                if ($idEcrivainActuel !== $dernierIdAuteur) {

                    // SI CE N'EST PAS LE PREMIER AUTEUR, ON AFFICHE SON BOUTON DE SUPPRESSION
                    if ($dernierIdAuteur !== "") {

                        // LIEN DE MODIFICATION
                        echo '<a href="?id=' . $idEcrivainActuel . '">Modifier</a><br>';

                        // BOUTON SUPPRIMER
                        echo '<form method="post">';
                        echo "<input type='hidden' name='idDeleteEcrivain' value='$dernierIdAuteur'>";
                        echo "<input type='submit' name='submitDeleteEcrivain' value='Supprimer'><br>";
                        echo "</form><br><br>";
                    }

                    // AFFICHAGE DU NOM DE L'AUTEUR
                    echo "<strong>" . htmlspecialchars($value['prenom_ecrivain']) . " " . htmlspecialchars($value['nom_ecrivain']) . "</strong><br>";

                    // AFFICHAGE DU PREMIER LIVRE OU MESSAGE SI AUCUN
                    if (!empty($value['titre'])) {
                        echo "- " . htmlspecialchars($value['titre']) . "<br>";
                    } else {
                        echo "Aucun livre associé" . "<br>";
                    }

                    $dernierIdAuteur = $idEcrivainActuel;
                } else {
                    // AUTEUR DÉJÀ AFFICHÉ, ON AFFICHE JUSTE LES LIVRES SUIVANTS
                    if (!empty($value['titre'])) {
                        echo "- " . htmlspecialchars($value['titre']) . "<br>";
                    }
                }
            }

            // APRÈS LA BOUCLE, ON AFFICHE LE FORMULAIRE POUR LE DERNIER AUTEUR
            if ($dernierIdAuteur !== "") {

                // LIEN DE MODIFICATION
                echo '<a href="?id=' . $idEcrivainActuel . '">Modifier</a><br>';

                // BOUTON SUPPRIMER
                echo '<form method="post">';
                echo "<input type='hidden' name='idDeleteEcrivain' value='$dernierIdAuteur'>";
                echo "<input type='submit' name='submitDeleteEcrivain' value='Supprimer'><br>";
                echo "</form><br><br>";
            }
        }

        // INITIALISATION DU FORMULAIRE DE MODIFICATION
        if (isset($_GET['id'])) {
            $idEcrivain = $_GET['id'];

            $sqlIdEcrivain = "SELECT * FROM `ecrivain` WHERE id_ecrivain = '$idEcrivain'";
            $stmtIdEcrivain = $pdo->prepare($sqlIdEcrivain);
            $stmtIdEcrivain->execute();

            $resultsIdEcrivain = $stmtIdEcrivain->fetchAll(PDO::FETCH_ASSOC);

            echo '<form method="POST">
            <label>Id</label>
            <br>
            <input type="text" name="idEcrivainUpdate" value="' . $resultsIdEcrivain[0]['id_ecrivain'] . '">
            <br>
            <br>

            <label>Nom</label>
            <br>
            <input type="text" name="nomEcrivainUpdate" value="' . $resultsIdEcrivain[0]['nom_ecrivain'] . '">
            <br>
            <br>

            <label>Préom</label>
            <br>
            <input type="text" name="prenomEcrivainUpdate" value="' . $resultsIdEcrivain[0]['prenom_ecrivain'] . '">
            <br>
            <br>

            <input type="submit" name="submitEcrivainUpdate" value="Mettre à jour">
            </form>';
        }

        // REQUETE DE MODIFICATION EN CLIQUANT SUR LE BOUTON METTRE A JOUR
        if (isset($_POST['submitEcrivainUpdate'])) {
            $idEcrivainUpdate = $_POST['idEcrivainUpdate'];
            $nomEcrivainUpdate = $_POST['nomEcrivainUpdate'];
            $prenomEcrivainUpdate = $_POST['prenomEcrivainUpdate'];

            $sqlEcrivainUpdate = "UPDATE `ecrivain` SET `nom_ecrivain`='$nomEcrivainUpdate' WHERE id_ecrivain = '$idEcrivainUpdate'";

            $stmtEcrivainUpdate = $pdo->prepare($sqlEcrivainUpdate);
            $stmtEcrivainUpdate->execute();

            echo "Ecrivain modifié avec succès !";
        }

        ?>

        <a href="?page=addAuthor">
            <h3>Ajouter un auteur</h3>
        </a>

        <?php

        if (isset($_GET['page']) && $_GET['page'] == 'addAuthor') {
            echo '<form method="POST">
            <label>Prénom :</label>
            <br>
            <input type="text" name="prenomEcrivain" id="prenomEcrivain" required>
            <br>
            <br>

            <label>Nom :</label>
            <br>
            <input type="text" name="nomEcrivain" id="nomEcrivain" required>
            <br>
            <br>

            <input type="submit" name="submitAddEcrivain" value="Ajouter">

        </form>';
        }

        if (isset($_POST['submitAddEcrivain'])) {
            $prenomAddEcrivain = $_POST['prenomEcrivain'];
            $nomAddEcrivain = $_POST['nomEcrivain'];

            $sqlAddEcrivain = "INSERT INTO `ecrivain`(`nom_ecrivain`, `prenom_ecrivain`) VALUES (?,?)";

            $stmtAddEcrivain = $pdo->prepare($sqlAddEcrivain);
            $stmtAddEcrivain->execute([$nomAddEcrivain, $prenomAddEcrivain]);

            echo "Auteur ajouté avec succès !";
        }

        ?>

    </div>
    <!-- --------------- SECTION ÉCRIVAINS FIN --------------- -->

    <hr>

    <!-- --------------- SECTION GENRES DÉBUT --------------- -->
    <div class="genres">
        <h2>- Les Genres -</h2>

        <a href="?page=allGenres">
            <h3>Voir tous les genres</h3>
        </a>

        <!-- VOIR TOUS LES GENRES -->
        <?php

        // CREATION FORMULAIRE POUR AFFICHER TOUS LES LORSQUE LA PAGE CORRESPOND A ALLGENRES
        if (isset($_GET['page']) && $_GET['page'] == 'allGenres') {

            $sqlAllGenres = "SELECT `id_genre`, `nom_genre` 
            FROM `genre`  
            ORDER BY nom_genre";

            $stmtAllGenres = $pdo->query($sqlAllGenres);
            $genres = $stmtAllGenres->fetchAll(PDO::FETCH_ASSOC);

            // BOUCLE POUR QUE CHAQUE GENRE SOIT AFFICHE AVEC SON NOM EN GRAS (STRONG) SUIVI DU BOUTON SUPPRIMER
            foreach ($genres as $key => $value) {
                $idGenreASupprimer = $value['id_genre'];

                echo "<strong>" . htmlspecialchars($value['nom_genre']) . "</strong><br>";

                // LIEN DE MODIFICATION
                echo '<a href="?id=' . $idGenreASupprimer . '">Modifier</a>';

                // CREATION DU BOUTON DE SUPPRESSION QUI NECESSITE UN FORM
                echo '<form method="post">';
                echo "<input type='hidden' name='idDeleteGenre' value='$idGenreASupprimer'>";

                echo "<input type='submit' name='submitDeleteGenre' value='Supprimer'><br>";
                echo "</form>";

                // REQUETE POUR SUPPRESSION DU GENRE EN CLIQUANT SUR SUPPRIMER
                if (isset($_POST['submitDeleteGenre'])) {
                    $idDeleteGenre = $_POST['idDeleteGenre'];

                    $sqlDeleteGenre = "DELETE FROM `genre` WHERE id_genre = $idDeleteGenre";
                    $stmtDeleteGenre = $pdo->prepare($sqlDeleteGenre);
                    $stmtDeleteGenre->execute();

                    echo "Le genre a bien été supprimé.<br><br>";
                }
                echo '</form><br>';
            }
        }

        // INITIALISATION DU FORMULAIRE DE MODIFICATION
        if (isset($_GET['id'])) {
            $idGenre = $_GET['id'];

            $sqlIdGenre = "SELECT * FROM `genre` WHERE id_genre = '$idGenre'";
            $stmtIdGenre = $pdo->prepare($sqlIdGenre);
            $stmtIdGenre->execute();

            $resultsIdGenre = $stmtIdGenre->fetchAll(PDO::FETCH_ASSOC);

            echo '<form method="POST">
            <label>Id</label>
            <br>
            <input type="text" name="idGenreUpdate" value="' . $resultsIdGenre[0]['id_genre'] . '">
            <br>
            <br>

            <label>Genre</label>
            <br>
            <input type="text" name="nomGenreUpdate" value="' . $resultsIdGenre[0]['nom_genre'] . '">
            <br>
            <br>

            <input type="submit" name="submitGenreUpdate" value="Mettre à jour">
            </form>';
        }

        // REQUETE DE MODIFICATION EN CLIQUANT SUR LE BOUTON METTRE A JOUR
        if (isset($_POST['submitGenreUpdate'])) {
            $idGenreUpdate = $_POST['idGenreUpdate'];
            $nomGenreUpdate = $_POST['nomGenreUpdate'];

            $sqlGenreUpdate = "UPDATE `genre` SET `nom_genre`='$nomGenreUpdate' WHERE id_genre = '$idGenreUpdate'";

            $stmtGenreUpdate = $pdo->prepare($sqlGenreUpdate);
            $stmtGenreUpdate->execute();

            echo "Genre modifié avec succès !";
        }

        ?>


        <a href="?page=addGenre">
            <h3>Ajouter un genre</h3>
        </a>

        <?php

        // CREATION FORMULAIRE POUR AJOUT DU GENRE LORSQUE LA PAGE CORRESPOND A ADDGENRE
        if (isset($_GET['page']) && $_GET['page'] == 'addGenre') {
            echo '<form method="POST">
            <label>Genre :</label>
            <br>
            <input type="text" name="addGenre" id="addGenre" required>
            <br>
            <br>

            <input type="submit" name="submitAddGenre" value="Ajouter">

            </form>';
        }

        // REQUETE POUR AJOUT DU GENRE EN CLIQUANT SUR AJOUTER 
        if (isset($_POST['submitAddGenre'])) {
            $addGenre = $_POST['addGenre'];

            $sqlAddGenre = "INSERT INTO `genre`(`nom_genre`) VALUES (?)";

            $stmtAddGenre = $pdo->prepare($sqlAddGenre);
            $stmtAddGenre->execute([$addGenre]);

            echo "Genre ajouté avec succès !";
        }
        ?>


    </div>
    <!-- --------------- SECTION GENRES FIN --------------- -->

    <hr>

    <!-- --------------- SECTION UTILISATEURS DÉBUT --------------- -->
    <div class="utilisateurs">
        <h2>- Les Utilisateurs -</h2>

        <a href="?page=allUsers">
            <h3>Voir tous les utilisateurs</h3>
        </a>

        <!-- VOIR TOUS LES UTILISATEURS -->
        <?php
        if (isset($_GET['page']) && $_GET['page'] == 'allUsers') {

            // REQUETE DE SUPPRESSION D'UN UTILISATEUR AU CLIC SUR LE BOUTON SUPPRIMER
            if (isset($_POST['submitDeleteUser'])) {
                $idDeleteUser = $_POST['idDeleteUser'];

                $sqlDeleteUser = "DELETE FROM `utilisateur` WHERE id_utilisateur = $idDeleteUser";
                $stmtDeleteUser = $pdo->prepare($sqlDeleteUser);
                $stmtDeleteUser->execute();

                echo "L'utilisateur a bien été supprimé.<br><br>";
            }

            // AFFICHAGE DE TOUS LES UTILISATEURS
            $sqlAllUsers = "SELECT id_utilisateur, nom_utilisateur, prenom_utilisateur, mail_utilisateur 
                    FROM Utilisateur 
                    ORDER BY nom_utilisateur"; // PERMET DE TRIER PAR ORDRE ALPHABETIQUE

            $stmtAllUsers = $pdo->query($sqlAllUsers);
            $utilisateurs = $stmtAllUsers->fetchAll(PDO::FETCH_ASSOC);

            foreach ($utilisateurs as $value) {
                $idUserASupprimer = $value['id_utilisateur'];

                // AFFICHAGE DU NOM - PRENOM EN GRAS (STRONG) ET DE L'EMAIL
                echo "<strong>" . htmlspecialchars($value['nom_utilisateur']) . " " . htmlspecialchars($value['prenom_utilisateur']) . "</strong><br>";
                echo "Email : " . htmlspecialchars($value['mail_utilisateur']) . "<br><br>";

                // LIEN DE MODIFICATION
                echo '<a href="?id=' . $idUserASupprimer . '">Modifier</a><br>';

                // FORMULAIRE DE SUPPRESSION
                echo '<form method="post">';
                echo "<input type='hidden' name='idDeleteUser' value='$idUserASupprimer'>";
                echo "<input type='submit' name='submitDeleteUser' value='Supprimer'>";
                echo "</form><br><br>";
            }
        }

        if (isset($_GET['id'])) {
            $idUser = $_GET['id'];

            $sqlIdUser = "SELECT * FROM `utilisateur` WHERE id_utilisateur = '$idUser'";
            $stmtIdUser = $pdo->prepare($sqlIdUser);
            $stmtIdUser->execute();

            $resultsIdUser = $stmtIdUser->fetchAll(PDO::FETCH_ASSOC);

            echo '<form method="POST">
            <label>Id</label>
            <br>
            <input type="text" name="idUserUpdate" value="' . $resultsIdUser[0]['id_utilisateur'] . '">
            <br>
            <br>

            <label>Nom</label>
            <br>
            <input type="text" name="nomUserUpdate" value="' . $resultsIdUser[0]['nom_utilisateur'] . '">
            <br>
            <br>

            <label>Prénom</label>
            <br>
            <input type="text" name="prenomUserUpdate" value="' . $resultsIdUser[0]['prenom_utilisateur'] . '">
            <br>
            <br>

            <label>Mail</label>
            <br>
            <input type="text" name="mailUserUpdate" value="' . $resultsIdUser[0]['mail_utilisateur'] . '">
            <br>
            <br>

            <input type="submit" name="submitUserUpdate" value="Mettre à jour">
            </form>';
        }

        // REQUETE DE MODIFICATION EN CLIQUANT SUR LE BOUTON METTRE A JOUR
        if (isset($_POST['submitUserUpdate'])) {
            $idUserUpdate = $_POST['idUserUpdate'];
            $nomUserUpdate = $_POST['nomUserUpdate'];
            $prenomUserUpdate = $_POST['prenomUserUpdate'];
            $mailUserUpdate = $_POST['mailUserUpdate'];

            $sqlUserUpdate = "UPDATE `utilisateur` SET `nom_utilisateur`='$nomUserUpdate', `prenom_utilisateur`='$prenomUserUpdate',`mail_utilisateur`='$mailUserUpdate' WHERE id_utilisateur = '$idUserUpdate'";

            $stmtUserUpdate = $pdo->prepare($sqlUserUpdate);
            $stmtUserUpdate->execute();

            echo "Utilisateur modifié avec succès !";
        }

        ?>

        <a href="?page=addUser">
            <h3>Ajouter un utilisateur</h3>
        </a>

        <?php

        if (isset($_GET['page']) && $_GET['page'] == 'addUser') {
            echo '<form method="POST">

            <label>Nom :</label>
            <br>
            <input type="text" name="nomUser" id="nomUser" required>
            <br>
            <br>

            <label>Prénom :</label>
            <br>
            <input type="text" name="prenomUser" id="prenomUser" required>
            <br>
            <br>

            <label>Email :</label>
            <br>
            <input type="text" name="mailUser" id="mailUser" required>
            <br>
            <br>

            <input type="submit" name="submitAddUser" value="Ajouter">

        </form>';
        }

        if (isset($_POST['submitAddUser'])) {
            $prenomUser = $_POST['prenomUser'];
            $nomUser = $_POST['nomUser'];
            $mailUser = $_POST['mailUser'];

            $sqlAddUser = "INSERT INTO `utilisateur`(`nom_utilisateur`, `prenom_utilisateur`, `mail_utilisateur`) VALUES (?,?,?)";

            $stmtAddUser = $pdo->prepare($sqlAddUser);
            $stmtAddUser->execute([$nomUser, $prenomUser, $mailUser]);

            echo "Utilisateur ajouté avec succès !";
        }

        ?>
    </div>
    <!-- --------------- SECTION UTILISATEURS FIN --------------- -->

    <hr>

    <!-- --------------- SECTION EMPRUNTS DEBUT --------------- -->
    <div class="emprunts">
        <h2>- Les Emprunts -</h2>
    </div>
    <!-- --------------- SECTION EMPRUNTS FIN --------------- -->

</body>

</html>