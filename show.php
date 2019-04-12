<head>
  <link href="style.css" rel="stylesheet">
</head>

<script>

<?php  
include "connect.php";
echo "hideMarkers();";
echo "hideGran();";

$state = pg_query($dbconn, "SELECT * from state order by id");
			$state2=pg_num_rows($state);
			$state_centr = pg_fetch_all_columns($state, 2);
			$zoom = pg_fetch_all_columns($state, 3);

if ( $_REQUEST['id']==0) echo "map.setView([49.999433, 35.16557990000001],3);"; else for ($j=1;$j<=$state2;$j++) if ( (int)$_POST['id']==$j) {
	echo "map.setView([";
	if ($state_centr[$j-1]!==null)
	echo $state_centr[$j-1];
    else
	{		
	echo "49.999433, 35.16557990000001";
	echo "3";
	}
	
	echo "],";
	if ($zoom[$j-1]!==null)
	echo $zoom[$j-1];
    else echo "3";
	
	echo ");";
}

if ( $_REQUEST['id']==0) $result = pg_query($dbconn, "SELECT * from neft where koord is not null order by id");  
else
for ($j=1;$j<=$state2;$j++) { if ( (int)$_POST['id']==$j)
				$result = pg_query($dbconn, "select t1.name,t1.koord,t1.year,t1.zapas,t1.opisanie, t1.id from neft t1 inner join neft_prin t2 on t1.id=t2.id 
			inner join state s1 on s1.country=t2.country
			where s1.id='{$j}' order by t1.id;"); 
									}
									
								if ( (int)$_POST['id']==0) $name = pg_query($dbconn, "SELECT name from neft where koord is not null order by name;");  
									else
for ($j=1;$j<=$state2;$j++) { if ( (int)$_POST['id']==$j)
				$name = pg_query($dbconn, "select t1.name from neft t1 inner join neft_prin t2 on t1.id=t2.id 
			inner join state s1 on s1.country=t2.country
			where s1.id='{$j}' order by t1.id;"); 
									}

									
									

/*
if ( (int)$_POST['id']==69)
$result = pg_query($dbconn, " select t1.name,t1.koord,t1.year,t1.zapas,t1.opisanie, t1.id from neft t1 inner join neft_prin t2 on t1.id=t2.id where
t2.country='Казахстан' order by t1.id;");*/





if ( (int)$_POST['id']==0) $qcountry = pg_query($dbconn, "SELECT id, string_agg(country, ', ') as country FROM neft_prin GROUP BY id order by id");
else
for ($j=1;$j<=$state2;$j++) { if ( (int)$_POST['id']==$j)
				$qcountry = pg_query($dbconn, "SELECT t2.id, string_agg(t2.country, ', ') as country FROM neft_prin t2 inner join state s1 on s1.country=t2.country 
			where s1.id='{$j}' GROUP BY t2.id order by t2.id;");
									}
									
	if ( (int)$_POST['id']==0) $qcompany = pg_query($dbconn," SELECT t2.id, string_agg(t2.company, ', ') as country FROM neft_prin_company t2 
inner join neft t1 on t2.id=t1.id GROUP BY t2.id order by t2.id;");		
else 
for ($j=1;$j<=$state2;$j++) { if ( (int)$_POST['id']==$j)
				$qcompany = pg_query($dbconn, "SELECT t2.id, string_agg(t3.company, ', ') as company FROM neft_prin t2 inner join state s1 on s1.country=t2.country 
 inner join neft_prin_company t3 on t2.id=t3.id
 where s1.id='{$j}'
 GROUP BY t2.id order by t2.id;");}											
									
//if ( (int)$_POST['id']==69) $qcountry = pg_query($dbconn, "SELECT id, string_agg(country, ', ') as country FROM neft_prin where country='Казахстан' GROUP BY id order by id");
$arr = pg_fetch_all_columns($result, 1);
$arrname = pg_fetch_all_columns($result, 0);
$arrcountry = pg_fetch_all_columns($qcountry, 1);
$arryear = pg_fetch_all_columns($result, 2);
$arrzapas = pg_fetch_all_columns($result, 3);
$namer = pg_fetch_all_columns($name, 0);
$arrcompany=pg_fetch_all_columns($qcompany, 1);
			//маркеры
			for ($i=0;$i<count($arr);$i++)
			{ 
			echo "addmarker({coordinates:[";
			   echo $arr[$i];
			   echo "],";
			   echo "name:'";
			   echo $arrname[$i];
			   echo "',";
			   echo "country:'";
			   echo $arrcountry[$i];
			   echo "',";
			   echo "year:'";
			   echo $arryear[$i];
			   echo "',";
			   echo "company:'";
			   echo $arrcompany[$i];
			   echo "',";
			   echo "zapas:'";
			   echo $arrzapas[$i];
			   echo "'";
			echo "});";
			}
			
			
			/// границы
			$gr = pg_query($dbconn, "SELECT * from neft");
			$gr2=pg_num_rows($gr);

			for ($i=1;$i<=$gr2;$i++)
			{
				 
				if ( (int)$_POST['id']==0) $res1 = pg_query($dbconn, "select t1.id,t.gran from neft_granits t inner join neft t1 on t1.name=t.name where t1.id='{$i}' order by t1.id;"); 
				else
				for ($j=1;$j<=$state2;$j++) { if ( (int)$_POST['id']==$j)
				$res1 = pg_query($dbconn, "select t1.id,t.gran from neft_granits t inner join neft t1 on t1.name=t.name inner join neft_prin t2 on t2.id=t1.id 
			inner join state s1 on s1.country=t2.country where s1.id='{$j}' and t1.id='{$i}' order by t1.id;"); 
									}
							
				
                $arr1 = pg_fetch_all_columns($res1, 1);  
				if ($arr1!=null){
				   echo "addgranits({coordinates1:[";
                   for($j=0;$j<count($arr1);$j++)
			   {
			   echo "[";
			   echo $arr1[$j];
			   if ( ($j+1)!=count($arr1) ) 
			   {
				   echo "],";
			   }
			   else  { echo "]"; }
			   }
				   echo "]});";
			
			
			}
			}
			
			
			
										 for($i=0;$i<count($arrname);$i++)
if (!empty($_POST['nazv']))
if( (string)$_POST['nazv']==$arrname[$i])  
{
	           echo "find2({coordinates:[";
			   echo $arr[$i];
			   echo "],";
			   echo "name:'";
			   echo $arrname[$i];
			   echo "',";
			   echo "country:'";
			   echo $arrcountry[$i];
			   echo "',";
			   echo "year:'";
			   echo $arryear[$i];
			   echo "',";
			   echo "company:'";
			   echo $arrcompany[$i];
			   echo "',";
			   echo "zapas:'";
			   echo $arrzapas[$i];
			   echo "'";
			echo "});";
			echo "setTimeout(find2, 500);";
	
}	
									

?>


			function find2(properties)
{
map.setView(properties.coordinates,8);

if (typeof div !=="undefined" && div!=="0"){ div.parentNode.removeChild(div);}		
	if ($(".find").is(":hidden")) {
	null  } else {  $(".find").hide("slow"); }
	if ($(".overlay2").is(":hidden")) {
	null  } else {  $(".overlay2").hide("slow"); }	
	
							 div = document.createElement('div');
                             div.className = "overlay";
                             div.innerHTML =
							 '<b><font size=4>Название месторождения:</b><ins><i> '+properties.name+'</i></ins></font>'+					 
							 '<br><b><font size=4>Страна:</b><ins><i>  '+properties.country+'</i></ins></font>'+
							 '<br><b><font size=4>Год открытия: </b><ins><i> '+properties.year+'</i></ins></font>'+
							 '<br><b><font size=4>Операторы: </b><ins><i> '+properties.company+'</i></ins></font>'+
							 '<br><b><font size=4>Запасы: </b><ins><i>  '+properties.zapas+' млрд. тонн нефти</i>'+'</ins></font>'+
						     '<br><br><center><input type="button" value="Скрыть информацию" onclick="removediv()"></center>';
                             document.body.appendChild(div);	

	}

	function addmarker(properties)
			{
				i++;
           arrMark[i]=DG.marker(properties.coordinates,{icon: myIcon}).addTo(map); 
		        group[i] = DG.featureGroup([arrMark[i]]);
                group[i].addTo(map);
				// при клике центр и зум по маркеру, вывод информации
                group[i].on('click', function(e) {
				            map.setView([e.latlng.lat, e.latlng.lng],8);
							if (typeof div !=="undefined" && div!=="0"){ div.parentNode.removeChild(div);}	
	if ($(".overlay2").is(":hidden")) {
	null  } else {  $(".overlay2").hide("slow"); }									
							 div = document.createElement('div');
                             div.className = "overlay";
                             div.innerHTML =
							 '<b><font size=4>Название месторождения:</b><ins><i> '+properties.name+'</i></ins></font>'+					 
							 '<br><b><font size=4>Страна:</b><ins><i>  '+properties.country+'</i></ins></font>'+
							 '<br><b><font size=4>Год открытия: </b><ins><i> '+properties.year+'</i></ins></font>'+
							 '<br><b><font size=4>Операторы: </b><ins><i> '+properties.company+'</i></ins></font>'+
							 '<br><b><font size=4>Запасы: </b><ins><i>  '+properties.zapas+' млрд. тонн нефти</i>'+'</ins></font>'+
						     '<br><br><center><input type="button" value="Скрыть информацию" onclick="removediv()"></center>';
                             document.body.appendChild(div);	
							  });						  
			}
			
			
			function addgranits(properties)
			{
				q++;
			arrGran[q]=DG.polygon([properties.coordinates1],{color: 'black'}).addTo(map);
			group2[q] = DG.featureGroup([arrGran[q]]);
			group2[q].addTo(map);
            }
			
			function removediv()
{
	if (typeof div !=="undefined"){ div.parentNode.removeChild(div); div="0";}	
}	
			
						function hideMarkers() {
							$(document).ready(function(){
				for (var ii = 1; ii <=i; ii++)
				{
                group[ii].removeFrom(map);
				}
							});
            };
			
									function hideGran() {
										$(document).ready(function(){
				for (var iii = 1; iii <=q; iii++)
                group2[iii].removeFrom(map);
										});
            };
			
			

</script>
