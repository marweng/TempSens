<?php 
    
    //Open DB
    $db = new SQLite3("temp_test1.db");
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
    $db->close();
?>