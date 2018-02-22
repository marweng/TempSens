<?php 
    
    //Open DB
        
    try {

        $db = new SQLite3("temp_test1.db");
        echo "Verbindundsaufbau erfolgreich."
        $db->close();
    }   catch (Eception $ex){
        echo "Fehler: " . $ex->getMessage();
    }  
?>
    //Weise 
    $result = $db->query("select Timestamp as  FROM logging");

    while ($row = $result->fetchArray(SQLITE3_ASSOC))
        {
        echo $dsatz["datum"];
        echo " ";
        echo $dsatz["uhrzeit"];
        echo " ";
        echo $dsatz["temperatur"];
        echo "<br>";
        }
   
?>