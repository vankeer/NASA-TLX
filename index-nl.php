<?php
session_start();
$token = md5(uniqid(rand(), TRUE));
$_SESSION['token'] = $token;
$_SESSION['token_time'] = time();
?>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
    <title>NASA Task Load Index</title>

    <!-- 
    
     This implements the NASA TLX via a single web page using JavaScript.
     It first collects the user's rating for 6 scale, the user can
     click on one of 20 different positions equating to a rating of 5-100
     in increments of 5. The user then selects the more important scale 
     in 15 pairings presented in random order.
    
     Copyright 2011 by Keith Vertanen
     http://www.keithv.com/software/nasa_tlx
    
    -->

    <script language="JavaScript" type="text/javascript">
<!--

      // Create a set of parallel arrays for each of the scales
      var scale = new Array();
      var left = new Array();
      var right = new Array();
      var def = new Array();
      var NUM_SCALES = 6;

      scale[0] = "Mentale Eisen";
      left[0] = "Laag";
      right[0] = "Hoog";
      def[0] = "<i class='icon-question-sign icon-2x' rel='tooltip' title='Hoeveel mentale en perceptuele activiteit was er vereist (bv. denken, beslissen, berekenen, herinneren, kijken, zoeken, enzovoort)? Was de taak gemakkelijk of veeleisend, simpel of complex, strikt of vergevend?'></i>";

      scale[1] = "Fysieke Eisen";
      left[1] = "Laag";
      right[1] = "Hoog";
      def[1] = "<i class='icon-question-sign icon-2x' rel='tooltip' title='Hoeveel fysieke activiteit was er vereist (bv. duwen, trekken, draaien, controlleren, activeren, enzovoort)? Was de taak gemakkelijk of veeleisend, traag of vlug, licht of zwaar, rustgevend of arbeidsintensief?'></i>";

      scale[2] = "Tijdseisen";
      left[2] = "Laag";
      right[2] = "Hoog";
      def[2] = "<i class='icon-question-sign icon-2x' rel='tooltip' title='Hoeveel tijdsdruk voelde je door de snelheid waarmee de taak of taken moesten gebeuren? Was het ritme traag en op het gemak, of snel en gehaast?'></i>";

      scale[3] = "Prestatie";
      left[3] = "Goed";
      right[3] = "Slecht";
      def[3] = "<i class='icon-question-sign icon-2x' rel='tooltip' title='Hoe succesvol denk je dat je was in het bereiken van de doelen vooropgesteld door de proefleider (of door jezelf)? Hoe tevreden was je met je performantie in het bereiken van deze doelen?'></i>";

      scale[4] = "Inspanning";
      left[4] = "Laag";
      right[4] = "Hoog";
      def[4] = "<i class='icon-question-sign icon-2x' rel='tooltip' title='Hoe hard moest je werken (mentaal en fysiek) om jouw niveau van performantie te bereiken?'></i>";

      scale[5] = "Frustratie";
      left[5] = "Laag";
      right[5] = "Hoog";
      def[5] = "<i class='icon-question-sign icon-2x' rel='tooltip' title='Hoe onzeker, ontmoedigd, geirriteerd, gestrest en geergerd versus zeker, bevredigd, tevreden, ontspannen en zelfvoldaan voelde je tijdens de taak?'></i>";

      // Pairs of factors in order in the original instructions, numbers
      // refer to the index in the scale, left, right, def arrays.
      var pair = new Array();
      pair[0] = "4 3";
      pair[1] = "2 5";
      pair[2] = "2 4";
      pair[3] = "1 5";
      pair[4] = "3 5";
      pair[5] = "1 2";
      pair[6] = "1 3";
      pair[7] = "2 0";
      pair[8] = "5 4";
      pair[9] = "3 0";
      pair[10] = "3 2";
      pair[11] = "0 4";
      pair[12] = "0 1";
      pair[13] = "4 1";
      pair[14] = "5 0";

      // Variable where the results end up
      var results_rating = new Array();
      var results_tally = new Array();
      var results_weight = new Array();
      var results_overall;

      var pair_num = 0;
      for (var i = 0; i < NUM_SCALES; i++)
        results_tally[i] = 0;

      // Used to randomize the pairings presented to the user
      function randOrd()
      {
        return (Math.round(Math.random()) - 0.5);
      }

      // Make sure things are good and mixed
      for (i = 0; i < 100; i++)
      {
        pair.sort(randOrd);
      }

      // They click on a scale entry
      function scaleClick(index, val)
      {
        results_rating[index] = val;

        // Turn background color to white for all cells
        for (i = 5; i <= 100; i += 5)
        {
          var top = "t_" + index + "_" + i;
          var bottom = "b_" + index + "_" + i;
          document.getElementById(top).bgColor = '#FFFFFF';
          document.getElementById(bottom).bgColor = '#FFFFFF';
        }

        var top = "t_" + index + "_" + val;
        var bottom = "b_" + index + "_" + val;
        document.getElementById(top).bgColor = '#AAAAAA';
        document.getElementById(bottom).bgColor = '#AAAAAA';
      }

      // Return the HTML that produces the table for a given scale
      function getScaleHTML(index)
      {
        var result = "";

        // Outer table with a column for scale, column for definition
        result += '<table class="center-table"><tr><td>';

        // Table that generates the scale
        result += '<table class="scale">';

        // Row 1, just the name of the scale
        result += '<tr><td colspan="20" class="heading">' + scale[index] + '</td></tr>';

        // Row 2, the top half of the scale increments, 20 total columns
        result += '<tr>';
        var num = 1;
        for (var i = 5; i <= 100; i += 5)
        {
          result += '<td id="t_' + index + '_' + i + '"   class="top' + num + '" onMouseUp="scaleClick(' + index + ', ' + i + ');"></td>';
          num++;
          if (num > 2)
            num = 1;
        }
        result += '</tr>';

        // Row 3, bottom half of the scale increments
        result += '<tr>';
        for (var i = 5; i <= 100; i += 5)
        {
          result += '<td id="b_' + index + '_' + i + '"   class="bottom" onMouseUp="scaleClick(' + index + ', ' + i + ');"></td>';
        }
        result += '</tr>';

        // Row 4, left and right of range labels
        result += '<tr>';
        result += '<td colspan="10" class="left">' + left[index] + '</td><td colspan="10" class="right">' + right[index] + '</td>';
        result += '</tr></table></td>';


        // Now for the definition of the scale
        result += '<td class="def">';
        result += def[index];
        result += '</td></tr></table>';

        return result;
      }

      function onLoad()
      {
        // Get all the scales ready
        for (var i = 0; i < NUM_SCALES; i++)
        {
          document.getElementById("scale" + i).innerHTML = getScaleHTML(i);
        }
      }

      // Users want to proceed after doing the scales
      function buttonPart1()
      {
        // Check to be sure they click on every scale
        for (var i = 0; i < NUM_SCALES; i++)
        {
          if (!results_rating[i])
          {
            alert('Een waarde moet geselecteerd worden voor elke schaal!');
            return false;
          }
        }

        // Bye bye part 1, hello part 2
        document.getElementById('div_part1').style.display = 'none';
        document.getElementById('div_part2').style.display = '';

        return true;
      }

      // User done reading the part 2 instructions
      function buttonPart2()
      {
        // Bye bye part 2, hello part 3
        document.getElementById('div_part2').style.display = 'none';
        document.getElementById('div_part3').style.display = '';

        // Set the labels for the buttons
        setPairLabels();
        return true;
      }

      // Set the button labels for the pairwise comparison stage
      function setPairLabels()
      {
        var indexes = new Array();
        indexes = pair[pair_num].split(" ");

        var pair1 = scale[indexes[0]];
        var pair2 = scale[indexes[1]];

        document.getElementById('pair1').value = pair1;
        document.getElementById('pair2').value = pair2;

        document.getElementById('pair1_def').innerHTML = def[indexes[0]];
        document.getElementById('pair2_def').innerHTML = def[indexes[1]];
      }

      // They clicked the top pair button
      function buttonPair1()
      {
        var indexes = new Array();
        indexes = pair[pair_num].split(" ");
        results_tally[indexes[0]]++;

        nextPair();
        return true;
      }

      // They clicked the bottom pair button
      function buttonPair2()
      {
        var indexes = new Array();
        indexes = pair[pair_num].split(" ");
        results_tally[indexes[1]]++;
        nextPair();
        return true;
      }

      // Compute the weights and the final score
      function calcResults()
      {
        results_overall = 0.0;

        for (var i = 0; i < NUM_SCALES; i++)
        {
          results_weight[i] = results_tally[i] / 15.0;
          results_overall += results_weight[i] * results_rating[i];
        }
      }

      // Output the table of results
      function getResultsHTML()
      {
        var result = "";

        result += "<table><tr><td></td><td>Rating</td><td>Tally</td><td>Weight</td></tr>";
        for (var i = 0; i < NUM_SCALES; i++)
        {
          result += "<tr>";

          result += "<td>";
          result += scale[i];
          result += "</td>";

          result += "<td>";
          result += results_rating[i];
          result += "</td>";

          result += "<td>";
          result += results_tally[i];
          result += "</td>";

          result += "<td>";
          result += results_weight[i];
          result += "</td>";

          result += "</tr>";
        }

        result += "</table>";
        result += "<br/>";
        result += "Overall = ";
        result += results_overall;
        result += "<br/>";

        return result;
      }

      // Move to the next pair
      function nextPair()
      {
        pair_num++;
        if (pair_num >= 15)
        {
          document.getElementById('div_part3').style.display = 'none';
          document.getElementById('div_part4').style.display = '';
          
          calcResults();
          
          var data = {
            scale: scale,
            results_rating: results_rating,
            results_tally: results_tally,
            results_weight: results_weight,
            results_overall: results_overall
          };
          
          $.ajax({
            type: 'POST',
            url: 'process.php?id=<?php echo $_GET['id']; ?>',
            data: {'data':JSON.stringify(data), 'token':'<?php echo $token; ?>'},
            dataType: 'json',
            success: dataSubmitted,
            error: dataError
          });
          
          document.getElementById('results').innerHTML = getResultsHTML();
        }
        else
        {
          setPairLabels();
        }
      }
      
      function dataSubmitted(data, status, xhr)
      {
        var status = typeof data['status'] !== 'undefined' ? data['status'] : status;
        if (status == 'success')
          $('#success').show();
        else
          $('#error').show();
      }
      
      function dataError(xhr, status, error)
      {
        $('#error').show();
      }

      // -->
    </script>

    <style>
      td.bottom
      {
        width: 0.6cm;
        height: 0.4cm;
        border-bottom: 1px solid black;
        border-left: 1px solid black;
        border-right: 1px solid black;
        margin: 0px; 
        padding: 0px 0px 0px 0px;

      }

      table.scale
      {
        margin: 0px;
        padding: 0px 0px 0px 0px;
        border-collapse: collapse
      }

      td.top1
      {
        width: 0.6cm;
        height: 0.4cm;
        border-top: 1px solid black;
        border-left: 1px solid black;
        margin: 0px; 
        padding: 0px 0px 0px 0px;
      }
      td.top2
      {
        width: 0.6cm;
        height: 0.4cm;
        border-top: 1px solid black;
        border-right: 1px solid black;
        margin: 0px; 
        padding: 0px 0px 0px 0px;
      }
      td.heading
      {
        font: Bold 14px Arial, Helvetica, sans-serif;
        text-align: center;
      }
      td.left
      {
        font: 14px Arial, Helvetica, sans-serif;
      }
      td.right
      {
        font: 14px Arial, Helvetica, sans-serif;
        text-align: right;
      }

      td.def
      {
        /*width: 12cm;*/
        padding: 0px 0px 0px 20px;
        font: 12px Arial, Helvetica, sans-serif;
      }

      input.pair
      {
        width: 5cm;
        height: 1cm;
        font: Bold 14px Arial, Helvetica, sans-serif;
      }
    </style>

    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/nasatlx.css">

    <script src="js/jquery-1.10.2.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    
    <script>
      $(document).ready(function(){
        $("[rel=tooltip]").tooltip({ placement: 'right', delay: 0});
      });
    </script>

  </head>


  <body onload="onLoad();">

    <div id="wrap">

      <div class="container">

        <div class="page-header">
          <h1>NASA-TLX</h1>
        </div>

        <div id="div_part1">

          <p class="lead">Klik voor elke schaal op het punt dat jouw ervaring met de taak het beste aangeeft.</p>
          
          <p>Sleep je cursor over de vraagtekens voor meer uitleg.</p>

          <div id="scale0"></div>
          <div id="scale1"></div>
          <div id="scale2"></div>
          <div id="scale3"></div>
          <div id="scale4"></div>
          <div id="scale5"></div>

          <br>
          <input class="next btn btn-primary pull-right" id="next" type="button" value="Continue &gt;&gt;" onclick="buttonPart1();">
        </div>

        <div id="div_part2" style="display:none">

          <p class="lead">Voor elk van de volgende 15 schermen, klik op de factor die meer bijdraagt aan de werklast van de taak.</p>
          
          <br>
          <input class="next btn btn-primary pull-right" id="next" type="button" value="Continue &gt;&gt;" onclick="buttonPart2();">
        </div>

        <div id="div_part3" style="display:none">

          <p class="lead">Klik op de factor die meer bijdraagt aan de werklast van de taak.</p>
          
          <br>
          <table>
            <tbody><tr>
                <td><input class="pair" id="pair1" type="button" value="" onclick="buttonPair1();"> </td>
                <td class="def"><div id="pair1_def"></div></td>
              </tr>
              <tr>
                <td align="center"> of </td>
                <td></td>
              </tr>
              <tr>
                <td><input class="pair" id="pair2" type="button" value="" onclick="buttonPair2();"></td>
                <td class="def"><div id="pair2_def"></div></td>
              </tr>
            </tbody></table>
        </div>

        <div id="div_part4" style="display:none">
          <p class="lead">Resultaten</p>
          <div id="success" class="alert alert-success" style="display:none">
            <strong>Klaar!</strong> Resultaten verzonden. Bedankt voor je deelname.
          </div>
          <div id="error" class="alert alert-danger" style="display:none">
            <strong>Fout!</strong> Iets ging mis bij het verzenden van de resultaten.
          </div>
          <div id="results">
          </div>
        </div>

      </div>

    </div>

    <div id="footer">
      <div class="container">
        <p class="text-muted credit"></p>
      </div>
    </div>


  </body>
</html>