<?php
#### 
# 
#
# Anzeige von Temperatur und Luftfeuchtigkeit aus einer Datenbank in einem Graphen
#
# Version 2.1
####


#### Set variables for time period to be visualized (default: yesterday till today)
####  From HTML form into SQL Queries
if (!empty($_POST["fromDate"])) {
        $fromDate = date(htmlspecialchars($_POST['fromDate']));
        $fromDate = strtotime($fromDate);
}else{
        $fromDate = strtotime("-1 day");
     }

if (!empty($_POST["toDate"])) {
        $toDate = date(htmlspecialchars($_POST['toDate']));
        $toDate = strtotime($toDate);
}else{  
        $toDate = strtotime("now");
     }

#### Connection to Database (DB=temphumid)
    try {
        $db = new SQLite3("temp_test1.db");
        //echo "Verbindundsaufbau erfolgreich.";
        $sql = "SELECT * FROM logging WHERE Timestamp >= '$fromDate' and Timestamp <= '$toDate'";
        $ergebnis = $db->query($sql);
        while ($zeile = $ergebnis->fetchArray()) {
          $timeA[] = $zeile["Timestamp"];
          $tempA[] = $zeile["temperature"];
          $humA[] = $zeile["humidity"];
          $countI = count($tempA);
        }
        $db->close();
      } catch (Exception $ex) {
        echo "Fehler: " . $ex->getMessage();
      }
?>


<!-- Embedding the d3.js/nv.d3.js JavaScript-Framework -->
<html>
	<head>
		<title>Temp & Feuchteauskunft</title>
		<script src="js/d3.min.js" charset="utf-8"></script>
		<script src="js/nv.d3.min.js" charset="utf-8"></script>
		<link href="js/nv.d3.css" rel="stylesheet">
	</head>

<!-- HTML body and displayed page elements -->

<body>
<center><h1>Temperatur- & Feuchteauskunft</h1>

<!-- Current weather information -->
<?php
  $j = $countI-1;
  $datum = date("d.m.Y",$timeA[$j]);
  $uhrzeit = date("H:i:s",$timeA[$j]);
	echo "<h2>Aktuelle Messdaten Wohnzimmer</h2>";
  echo "<table>";
  echo "<tr><td>Temperatur: </td><td><b>" .$tempA[$j], "</b> C°</td></tr>";
  echo "<tr><td>Luftfeuchtigkeit:  </td><td><b>". $humA[$j], "</b> %</td></tr>";
  echo "<tr></tr>";
  echo "<tr><td>Gemessen am: </td><td><b>". $datum, "</b> um <b>".$uhrzeit, "</b> Uhr</td></tr>";
  echo "</table>";
?>

<!-- HTML form to choose the time period (default: yesterday till today) -->
<div style="text-align:center; padding:20px; border:thin solid blue; margin:25px">
<h2>Auswahl des Zeitraums</h2>
<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="POST">
        <input type="datetime-local" name="fromDate" value="<?php 
		if ( !empty ($_POST["fromDate"] ) ) {
			echo date("Y-m-d\TH:i:s", strtotime($fromDate));
		} else {
			echo date("Y-m-d\TH:i:s",strtotime("-1 day"));
		} ?>"> bis zum
        <input type="datetime-local" name="toDate" value="<?php 
                if ( !empty ( $_POST['toDate'] ) ) {
			echo date("Y-m-d\TH:i:s", strtotime($toDate));
                } else {
			echo date('Y-m-d\TH:i:s'); 
		} ?>">
	<br>
        <input type="submit" value="Wetter-Graph abrufen" />
</form>
</div>

<!-- Chart element to be controlled by the JavaScript framework d3.js/nvd.d3.js -->
<div id='chart'>
  <svg style='height:500px'> </svg>
</div>

<!-- JavaScript code to create the graph -->
<script>
nv.addGraph(function() {
// For other chart types see: https://nvd3.org/examples/index.html
// API documentation: https://github.com/novus/nvd3/wiki/API-Documentation
	var chart = nv.models.lineChart()
               .options({
                duration: 300,
                useInteractiveGuideline: true 
               })
              ; 

	// Define xAxis (date)
	chart.xAxis    
      		.axisLabel('Datum')
      		.rotateLabels(-45)
      		.tickFormat(function(d) { 
			// 01/12/2013 12:00 Uhr
	  		return d3.time.format('%d/%m/%y %H:%m Uhr')(new Date(d))
      	});

	// Define yAxis (temp)
	chart.yAxis     
      		.axisLabel('Temperatur und Luftfeuchtigkeit')
      		.tickFormat(d3.format('.01f'))
	;

  // Fetch the data (dates + values from: tempInside/Outside, humidInside/Outside)
	var tempHumidData = getTempHumidData();
  // Render the chart (reference to SVG element in the HTML code)
	d3.select('#chart svg') 
      		.datum(tempHumidData) 
      		.call(chart) 
	;

  	// Update the chart when window resizes.
  	nv.utils.windowResize(function() { chart.update() });
  	return chart;
});


// Convert SQL fetched Data to d3.js-compatible data
function getTempHumidData() {
  	var temp 	= [], hum = []

  // Data is represented as an array of {x,y} pairs
	// Hint: The UNIX timestamp from the DB must be multiplied by 1000 
	//       as the JS date datatype works with milliseconds
  	<?php  
        for($i=0; $i < $countI; $i++)
        {
                $timeA[$i]=(int) $timeA[$i];
                echo "temp.push({x: ($timeA[$i] * 1000), y: $tempA[$i]});\n";
                #echo "<tr><td>Temperatur Time</td><td>$timeA</td></tr>";
                #echo "<tr><td>Temperatur Temp:</td><td>$tempA</td></tr>";
        }

        for($i=0; $i < $countI; $i++)
        {
                $timeA[$i]=(int) $timeA[$i]; 
                echo "hum.push({x: ($timeA[$i] * 1000), y: $humA[$i]});\n";
        }
  	?>

  	// Line chart data should be sent as an array of series objects.
  	return [
    		{
      			values: temp,
      			key: 'Temperatur',
      			area: true
      			//color: '#BBa02c'
    		},
    		{
      			values: hum,
 	     		  key: 'Luftfeuchtigkeit',
      			area: true
      			//color: '#FFa02c'
    		},
  	];
}
</script>
</p><hr><center>(c) wm 2019</center>
</body>
</html>
