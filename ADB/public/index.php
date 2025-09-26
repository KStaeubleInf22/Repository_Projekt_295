<?php
//Anfang vom PHP Code.
session_start();
$EditTitle ='';
$EditDescription = '';
$Log = '';
//Hier werden werte für Edit und Log als leer definiert damit ihre respektiven felder lehr sind.
if (isset($_POST["edit"])) {
        //Hier wird der Edit-Knopf gedrückt
        $_SESSION['FFFINALID'] = $_POST['hidden'];
        $_SESSION['EditTit'] = $_POST['tithidden'] ?? '';
        $_SESSION['EditDes'] = $_POST['deshidden'] ?? '';
        //Die Werte vom respektiven Eintrag in der Datenbank werden abgespeichert via $_SESSION.
        $Log = 'Bearbeitung Aktiv';
        //Log Antwort.

    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Auftrag_Daten_Bank</title>
</head>
<!-- HTML Header. -->
<body>
<form action="index.php" method="post">
<input type="text" name="title" placeholder="Title" value="<?php echo isset($_SESSION['EditTit']) ? htmlspecialchars($_SESSION['EditTit']) : ''; ?>">
<input type="text" name="description" placeholder="Description" value="<?php echo isset($_SESSION['EditDes']) ? htmlspecialchars($_SESSION['EditDes']) : ''; ?>">
<button type="submit" name="submit">Submit</button>
</form>
<!-- Hier im <form> hat man alle input felder um die einen Kompletten Eintrag in die Datenbank einzufügen.
    da es in <form> ist, wird auch alles zusammen abgeschickt. die values in title und description 
    sind dafür da, damit die Edit-Daten eingelesen werden können.-->


<?php
$servername = "mysql";
$username = "grogu";
$password = "grogu74";
$dbname = "grogu_db";

//Datenbank Nutzer-Angaben.



try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //Hier wird die verbindung gebaut.

    if (isset($_POST["submit"])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    if (strlen ($title) < 3) {
    $Log = 'Titel ist zu kurz!';}
    elseif (strlen ($description) < 10){
    $Log = 'Beschreibung ist zu kurz!';
    }
    //Hier wird die Submit funktion getestet. es wird geprüft, ob die inputs lang genug sind und der Code wird weitergeführt.
    //Was die Eingaben zu kurz sind, wird nichts abgespeichert.
    
    else{

    if (isset($_SESSION['FFFINALID'])) {
        $FFFinalEditID = $_SESSION['FFFINALID'];
        $upd = $conn->prepare("UPDATE todos SET title = :title, description = :description WHERE id = :id");
        $upd->execute([
            ':title' => $title,
            ':description' => $description,
            ':id' => $FFFinalEditID
        ]);
        //Hier wird geprüft, ob "Edit" gedrückt wurde. Wenn das der Fall ist, werden die Daten einer existierenden Eintrag ge-updated.

        $Log = 'Bearbeitung Erfolgreich';
        unset($_SESSION['FFFINALID'], $_SESSION['EditTit'], $_SESSION['EditDes']);
        //Die Lognachricht wird ausgegeben und die Felder werden geleert.
    }
        else{

                $ins = $conn->prepare("INSERT INTO todos (title, description) VALUES (:title, :description)");
        $ins->execute([
            ':title' => $title,
            ':description' => $description
        //Dies hier wird ausgeführt, wenn das "Submit" gedrückt wird, ohne davor "Edit" zu drücken. 
        //Ein neuer Eintrag wird erstellt.

        ]);
        $Log = 'Herstellung Erfolgreich';
        }
    }
    }
    if (isset($_POST["delete"])) {
        $FinalID = $_POST['hidden'];
        $DELETION = "DELETE FROM todos WHERE id = $FinalID";
        $conn->exec($DELETION);
        $Log = 'Löschen Erfolgreich';
        //Ein Eintrag wird gelöscht. Die ID wird geholt und dadurch weiss das Programm was zu löschen.


        
        
    }
    elseif (isset($_POST["conf"])) {
        $FinalStatID = $_POST['hidden'];
        $SecondLEdit = $_POST['stathidden'];
        $FinalEdit = ($SecondLEdit == '0') ? 1 : 0;
        //Hier wird der Status abgespeichert und der Wert wechselt zwischen 1 und 0 mit jedem Knopf druck.



        $sta = "UPDATE todos SET status = $FinalEdit WHERE id =$FinalStatID";
        $conn->exec($sta);
        $Log = 'Statuswechsel Erfolgreich';
        //Hier wird es ausgeführt.
    }
 

    $todos = $conn->query("SELECT * FROM todos")->fetchAll(PDO::FETCH_ASSOC);
    //Hier wird die Datenbank aufgerufen.

    foreach ($todos as $todo) {
        echo "ID: " . $todo["id"]. "<br>";
        $del_id = $todo["id"];
        echo "Title: " . $todo["title"] . "<br>";
        echo "Description: " . $todo["description"] . "<br>";
        echo "Status: " . $todo["status"] . "<br>";
        $edi_status = $todo["status"];
        $upd_title = $todo["title"];
        $upd_description = $todo["description"];
        $upd_val = false;
        //Die Echos sorgen dafür, dass sie Daten als neuen Eintrag aufgelistet werden.
        //Die $ Werte sorgen dafür, dass ich die werte nutzbar abspeichern kann.
        echo "<form action='index.php' class='formS' method='post'><input type='hidden' name='hidden' value='".$del_id."'>
        <input type='hidden' name='stathidden' value='".$edi_status."'>
        <input type='hidden' name='tithidden' value='".$upd_title."'>
        <input type='hidden' name='deshidden' value='".$upd_description."'>
        <input type='hidden' name='edithidden' value='".$upd_val."'>
        <button name='conf' class='". ($edi_status == 1 ? "confTrue" : "confS") ."'>Status</button>
        <button name='delete' class='deleteS'>Delete</button>
        <button name='edit' class='editS'>Edit</button>
        </form>";
        echo "<hr>";
        //Hier werden all die Knopfe generiert für Delete/Edit/Status.
        //Die Leeren Inputfelder sorgen dafür, dass ich die Eintragswerte aufrufen kann, via den namen.
    }

} catch (PDOException $e) {
    echo "Fehler: " . $e->getMessage();
}
//Fehler Prüfung.



?>  

<form action="index.php" method="post">
<input type="text" name="log" class="Log" placeholder="Log..." value="<?php echo $Log ? htmlspecialchars($Log) : ''; ?>">
</form>
<!-- Hier wird das Logtextfeld definiert und die value dient wieder dazu, 
 dass die Inputs gelogged werden. -->
</body>
</html>
<!-- Ende HTML. -->


