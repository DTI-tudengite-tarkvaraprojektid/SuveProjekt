<?php
	session_start();
    require("config.php");

	if(!isset($_SESSION["user_id"])){
		header("Location: index.php");
		exit();
	}

	if(isset($_GET["logout"])){
		session_destroy();
		header("Location: login.php");
		exit();
	}

	require_once("elements.php");
	createHeader("Raport");
    createNavbar();
    createNewRaportModal();
    
    require_once("classes/ParseCSV.class.php");
    $csvClass = new CSV("csv/tarbimisteatis.csv");
    $list = $csvClass->getMonthlyValues("2017");
    $list2 = $csvClass->getYearlyValues();
    $list3 = $csvClass->getWeeklyValues();
    $list4 = $csvClass->getDailyValues();
?>
<div class="container">
<div class="jumbotron">
    <h1>Raporti pealkiri</h1>
    <p>Raporti kirjeldus.</p>
</div>
<div id="chartContainer1" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
<hr>
<div id="chartContainer" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
<hr>
<div class="form-group">
<div class="col-md-3">
  <label for="yearSelect">Vali aasta:</label>
  <select class="form-control" id="yearSelect">
    <option>2017</option>
    <option>2018</option>
  </select>
</div>
<div class="col-md-3">
  <label for="weekSelect">Vali nädal:</label>
  <select class="form-control" id="weekSelect">
    <?php for($i=1; $i<=52; $i++){
        echo '<option>'.$i.'</option>';
    } 
    ?>
  </select>
</div> 
<div class="col-md-12">
<button type="button" id="weeklyButton" class="btn btn-primary btn-sm">Näita</button>
</div>
</div>
<div id="chartContainer2" style="min-width: 310px; height: 400px; margin: 0 auto; margin-top:100px; margin-bottom:100px;"></div>
<hr>
<label for="datepicker">Vali päev</label>
<input type="text" class="form-control" id="datepicker" name="datepicker">
<div id="chartContainer3" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
</div>

<script>
Highcharts.chart('chartContainer', {
    chart: {
        type: 'line'
    },
    title: {
        text: 'Elektrikasutus kuude lõikes'
    },
    subtitle: {
        text: 'Source: You'
    },
    xAxis: {
        categories: ['Jaanuar', 'Veebruar', 'Märts', 'Aprill', 'Mai', 'Juuni', 'Juuli', 'August', 'September', 'Oktoober', 'November', 'Detsember']
    },
    yAxis: {
        title: {
            text: 'KW'
        }
    },
    plotOptions: {
        line: {
            dataLabels: {
                enabled: false
            },
            enableMouseTracking: true
        }
    },
    series: [{
        name: 'Keskmine',
        data: [
                <?php foreach($list as $value){
                    echo $value . ", ";
                }?>
              ]
    }]
});

Highcharts.chart('chartContainer1', {
    chart: {
        zoomType: 'x'
    },
    title: {
        text: 'Elektrikasutus aastate lõikes'
    },
    xAxis: {
        categories: [                
                <?php 
                $valueArray = $list2[1];
                foreach($valueArray as $value){
                echo $value . ", ";
                }?>
                ]
    },
    yAxis: {
        title: {
            text: 'KW'
        }
    },
    plotOptions: {
        line: {
            dataLabels: {
                enabled: true
            },
            enableMouseTracking: false
        },
        area: {
                    fillColor: {
                        linearGradient: {
                            x1: 0,
                            y1: 0,
                            x2: 0,
                            y2: 1
                        },
                        stops: [
                            [0, Highcharts.getOptions().colors[0]],
                            [1, Highcharts.Color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
                        ]
                    },
                    marker: {
                        radius: 2
                    },
                    lineWidth: 1,
                    states: {
                        hover: {
                            lineWidth: 1
                        }
                    },
                    threshold: null
                }
    },
    series: [{
        name: 'Yours',
        type: 'column',
        data:   [
                <?php 
                $valueArray = $list2[0];
                foreach($valueArray as $value){
                echo $value . ", ";
                }?>
                ]
    }]
});

let weekchart = Highcharts.chart('chartContainer2', {
    chart: {
        type: 'line'
    },
    
    title: {
        text: 'Elektrikasutus nädala lõikes'
    },
    subtitle: {
        text: 'Source: CSV'
    },
    xAxis: {
        categories: ['Esmaspäev', 'Teisipäev', 'Kolmapäev', 'Neljapäev', 'Reede', 'Laupäev', 'Pühapäev']
    },
    yAxis: {
        title: {
            text: 'KW'
        }
    },
    plotOptions: {
        line: {
            dataLabels: {
                enabled: false
            },
            enableMouseTracking: true
        }
    },
    series: [{
        name: 'Keskmine',
        data:   [
                <?php 
                foreach($list3 as $value){
                echo $value . ", ";
                }?>
                ]
    }, {
        name: 'Valitud nädal',
        data: []
    }]
});

let daychart = Highcharts.chart('chartContainer3', {
    chart: {
        type: 'line'
    },
    title: {
        text: 'Elektrikasutus päeva lõikes'
    },
    subtitle: {
        text: 'Source: You'
    },
    xAxis: {
        categories: ['00', '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23']
    },
    yAxis: {
        title: {
            text: 'KW'
        }
    },
    plotOptions: {
        line: {
            dataLabels: {
                enabled: false
            },
            enableMouseTracking: true
        }
    },
    series: [{
        name: 'Keskmine',
        data: [
                <?php foreach($list4 as $value){
                    echo $value . ", ";
                }?>
              ]
            }, {
        name: 'Valitud päev',
        data: []
    }]
});
$('#weeklyButton').click(function () {
    let yearselection = $("#yearSelect :selected").text();
    let weekselection = $("#weekSelect :selected").text();
$.ajax({
      url: "getweek.php",
      type: "POST",
      data: {"year": yearselection, "week": weekselection},
      dataType : "json",
      success: function(msg){
        weekchart.series[1].setData([msg[0], msg[1], msg[2], msg[3], msg[4], msg[5], msg[6]]);
      },
    error: function() { 
        weekchart.series[1].setData();
    }
   })
});

$('#datepicker').change(function () {
    let dayselection = document.getElementById("datepicker").value;
    console.log(dayselection)
$.ajax({
      url: "getday.php",
      type: "POST",
      data: {"day": dayselection},
      dataType : "json",
      success: function(msg){
        daychart.series[1].setData([msg[0], msg[1], msg[2], msg[3], msg[4], msg[5], msg[6], msg[7], msg[8], msg[9], msg[10], msg[11], msg[12], msg[13], msg[14], msg[15], msg[16], msg[17], msg[18], msg[19], msg[20], msg[21], msg[22], msg[23]]);
      },
    error: function() { 
        daychart.series[1].setData();
    }
   })
});
</script>
