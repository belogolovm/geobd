<script>

<?php  
include "connect.php";

/////////////////////////// проверка пользователя

$ip =$_SERVER['REMOTE_ADDR'];

if (isset($_COOKIE['id']) and isset($_COOKIE['hash']))
{
   $query = pg_query($dbconn, "SELECT * from users WHERE user_id = '{$_COOKIE['id']}' LIMIT 1");
    $userdata = pg_fetch_assoc($query);

  if(($userdata['user_hash'] !== $_COOKIE['hash']) or ($userdata['user_id'] !== $_COOKIE['id'])
 or (($userdata['user_ip'] !== $ip)  and ($userdata['user_ip'] !== "0")))
    {
        setcookie("id", "", time() - 3600*24*30*12, "/");
        setcookie("hash", "", time() - 3600*24*30*12, "/");
        print "Хм, что-то не получилось";
    }
    else
    {
      $hello ="Пользователь: ".$userdata['user_login'].". Компания: ".$userdata['company']."";
	//   print "alert('$hello');";
	     }
}
else
{
  //  print "alert('Включите куки');";
}


/////////////////////

echo "hideMarkers();";
echo "hideGran();";


$nazv=$_POST['nazv'];
$id=$_POST['id'];
if (isset($_POST['num_schv']))
$num_schv=$_POST['num_schv'];

if (($num_schv!=0)&&(isset($num_schv)))
echo "hideScv();";


$state = pg_query($dbconn, "SELECT * from state order by id");
			$state2=pg_num_rows($state);
			$state_centr = pg_fetch_all_columns($state, 2);
			$zoom = pg_fetch_all_columns($state, 3);

if (( $_REQUEST['id']==0)&&($num_schv==0)||(empty($num_schv)))
{	echo "map.setView([49.999433, 35.16557990000001],3);"; 
echo 'if (typeof div !=="undefined" && div!=="0"){ removediv(); }';
echo 'if (typeof div4!=="undefined" && div4!=="0"){ removediv4(); }';
}
else 			if (($num_schv==0)||(isset($num_schv)))
for ($j=1;$j<=$state2;$j++) if ( (int)$_POST['id']==$j) {
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


    if (!empty($userdata['company']))
    $user_company=$userdata['company'];


if ($nazv!=null) {
	
		if ( $_REQUEST['id']==0) { 
		
		/// если залогинился
		if (isset($_COOKIE['id']) and isset($_COOKIE['hash']) and  ($nazv=='0')) {
		     if ($_POST['id']==0) $result = pg_query($dbconn, "select * from neft t1 inner join neft_prin_company t2 
									     on t1.id=t2.id where t2.company='{$user_company}' order by t1.id"); 
		    if ( (int)$_POST['id']==0) $name = pg_query($dbconn, "SELECT name from neft t1 inner join neft_prin_company t2 on t2.id=t1.id
			                            where t1.koord is not null and t2.company='{$user_company}' order by t1.id"); 
			 if ( (int)$_POST['id']==0) $qcountry = pg_query($dbconn, "SELECT t1.id,string_agg(country, ', ')  
				 as country FROM neft_prin t1 inner join neft_prin_company t2 on t1.id=t2.id
                 where t2.company='{$user_company}' GROUP BY t1.id order by t1.id"); 
			if ( (int)$_POST['id']==0) $qcompany = pg_query($dbconn,"SELECT t2.id, string_agg(t2.company, ', ') 
			as company FROM v_neft_prin_comp t2 inner join neft t1 on t2.id=t1.id 
		    where t2.company LIKE '%{$user_company}%' GROUP BY t2.id order by t2.id;"); 
		}	/// если не залогинился						 
										else
										 {	 
		$result = pg_query($dbconn, "SELECT * from neft where koord is not null and name='{$nazv}' order by id"); 
		$qcountry = pg_query($dbconn, "SELECT t1.id, string_agg(country, ', ') as country FROM neft_prin t1 inner join neft t2 
		    on t1.id=t2.id where t2.name='{$nazv}' GROUP BY t1.id order by t1.id"); 
		$qcompany = pg_query($dbconn," SELECT t2.id, string_agg(t2.company, ', ') as country FROM neft_prin_company t2
inner join neft t1 on t2.id=t1.id where t1.name='{$nazv}' GROUP BY t2.id order by t2.id;");	
		}
		}
		else
		{
$result = pg_query($dbconn, "select t1.name,t1.koord,t1.year,t1.zapas,t1.opisanie, t1.id from neft t1 inner join neft_prin t2 on t1.id=t2.id 
			inner join state s1 on s1.country=t2.country
			where t1.name='{$nazv}' order by t1.id limit 1;");
			
		$qcountry = pg_query($dbconn,	"SELECT t2.id, string_agg(t2.country, ', ') as country FROM neft_prin t2 inner join state s1 on s1.country=t2.country 
inner join neft t1 on t2.id=t1.id where t1.name='{$nazv}' GROUP BY t2.id order by t2.id");

$qcompany = pg_query($dbconn, "SELECT t2.id, v1.company FROM neft_prin t2 inner join state s1 on s1.country=t2.country 
inner join v_neft_prin_comp v1 on v1.id=t2.id
inner join neft t3 on t3.id=t2.id
where t3.name='{$nazv}'	 
			 GROUP BY t2.id,v1.company order by t2.id; ");
 
}
}
else 
{ 		if ( $_REQUEST['id']==0) { $result = pg_query($dbconn, "SELECT * from neft where koord is not null order by id"); 
		$qcountry = pg_query($dbconn, "SELECT id, string_agg(country, ', ') as country FROM neft_prin GROUP BY id order by id");
		$qcompany = pg_query($dbconn," SELECT t2.id, string_agg(t2.company, ', ') as country FROM neft_prin_company t2 
inner join neft t1 on t2.id=t1.id GROUP BY t2.id order by t2.id;");}
		else
		{
$result = pg_query($dbconn, "select t1.name,t1.koord,t1.year,t1.zapas,t1.opisanie, t1.id from neft t1 inner join neft_prin t2 on t1.id=t2.id 
			inner join state s1 on s1.country=t2.country
			where s1.id='{$id}' order by t1.id;");
			
			for ($j=1;$j<=$state2;$j++) { if ( (int)$_POST['id']==$j)
			$qcountry = pg_query($dbconn, "SELECT t2.id, string_agg(t2.country, ', ') as country FROM neft_prin t2 inner join state s1 on s1.country=t2.country 
			where s1.id='{$id}' GROUP BY t2.id order by t2.id;");}
			
			for ($j=1;$j<=$state2;$j++) { if ( (int)$_POST['id']==$j)
				$qcompany = pg_query($dbconn, "SELECT t2.id, string_agg(t3.company, ', ') as company FROM neft_prin t2 inner join state s1 on s1.country=t2.country 
 inner join neft_prin_company t3 on t2.id=t3.id
 where s1.id='{$j}'
 GROUP BY t2.id order by t2.id;");}		
		}
}
 
		
	

	
	
$arr = pg_fetch_all_columns($result, 1);
$arrname = pg_fetch_all_columns($result, 0);
$arrcountry = pg_fetch_all_columns($qcountry, 1);
$arryear = pg_fetch_all_columns($result, 2);
$arrzapas = pg_fetch_all_columns($result, 3);
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
			
			
			/// границы и скважины
			
			//залогинен
				if (isset($_COOKIE['id']) and isset($_COOKIE['hash'])) 
		{
			//cкважина не выбрана
			if ($num_schv==0) 
			 $res_scv = pg_query($dbconn, "select t1.name_mest,t1.koord,t1.number_scv,t1.status,t3.name,t3.email,t1.debit,v1.company,t1.radius from scvazh t1 inner join neft t2 on t1.name_mest=t2.name 
			inner join v_neft_prin_comp v1 on v1.id=t2.id inner join users t3 on t1.employee=t3.user_id
            where v1.company LIKE '%{$user_company}%'");
			else  if ($num_schv!=0) 
				$res_scv = pg_query($dbconn, "select t1.name_mest,t1.koord,t1.number_scv,t1.status,t3.name,t3.email,t1.debit,v1.company,t1.radius from scvazh t1 inner join neft t2 on t1.name_mest=t2.name 
			inner join v_neft_prin_comp v1 on v1.id=t2.id inner join users t3 on t1.employee=t3.user_id
            where v1.company LIKE '%{$user_company}%' and t1.name_mest='{$nazv}' and t1.number_scv='{$num_schv}';");
			
			$name_mest = pg_fetch_all_columns($res_scv, 0);
			$coord_scv = pg_fetch_all_columns($res_scv, 1);
			$numb_scv= pg_fetch_all_columns($res_scv, 2);
			$status= pg_fetch_all_columns($res_scv, 3);
			$name=pg_fetch_all_columns($res_scv, 4);
			$email=pg_fetch_all_columns($res_scv, 5);
			$debit=pg_fetch_all_columns($res_scv, 6);
			$radius=pg_fetch_all_columns($res_scv, 8);
		
		
if ($num_schv==0) 
	for ($i=0;$i<count($coord_scv);$i++)
			{ 
			echo "addscv({coordinates:["; echo $coord_scv[$i]; echo "],";
			   echo "name_mest:'"; echo $name_mest[$i]; echo "',";
			   echo "numb_scv:'"; echo $numb_scv[$i]; echo "',";
			   echo "status:'"; echo $status[$i]; echo "',";
			   echo "debit:'"; echo $debit[$i]; echo "',";
			   echo "name:'"; echo $name[$i]; echo "',";
			   echo "email:'"; echo $email[$i]; echo "',";
			   echo "radius:"; echo $radius[$i];echo ",";
			   
               if ($status[$i]=='Работает')
			   {
				   $color='#00FF00';
				   $color2='brown';
			   }
			   if ($status[$i]=='Остановлена')
			   {
				   $color='#E6E6FA';
			       $color2='black';
			   }
			   if ($status[$i]=='Авария')
			   {
				   $color='#FFD700';
			       $color2='red';
			   }
			   echo "color:'"; echo $color; echo "',";
			   echo "color2:'"; echo $color2; echo "',";
			   echo "company:"; echo "'{$user_company}'"; 
			   echo "});";    
			}
			else if (($num_schv!=0)&&(isset($num_schv)))
				for ($i=0;$i<count($coord_scv);$i++)
			{ 
			echo "addscv1({coordinates:["; echo $coord_scv[$i]; echo "],";
			   echo "name_mest:'"; echo $name_mest[$i]; echo "',";
			   echo "numb_scv:'"; echo $numb_scv[$i]; echo "',";
			   echo "status:'"; echo $status[$i]; echo "',";
			   echo "debit:'"; echo $debit[$i]; echo "',";
			   echo "name:'"; echo $name[$i]; echo "',";
			   echo "email:'"; echo $email[$i]; echo "',";
			   echo "radius:"; echo $radius[$i];echo ",";
			   
			   if ($status[$i]=='Работает')
			   {
				   $color='#00FF00';
				   $color2='brown';
			   }
			   if ($status[$i]=='Остановлена')
			   {
				   $color='#E6E6FA';
			       $color2='black';
			   }
			   if ($status[$i]=='Авария')
			   {
				   $color='#FFD700';
			       $color2='red';
			   }
			   echo "color:'"; echo $color; echo "',";
			   echo "color2:'"; echo $color2; echo "',";
			   echo "company:"; echo "'{$user_company}'"; 
			   echo "});";   
			}
			
	
		}
			
		// если не залогинился 
										 else 
										 {
			
				$gr = pg_query($dbconn, "SELECT * from neft");
			$gr2=pg_num_rows($gr);
				if (($nazv!=null)&&($id!=0)){ $res1 = pg_query($dbconn, "select t1.id,t.gran from neft_granits t inner join neft t1 on t1.name=t.name where t1.name='{$nazv}' order by t1.id;"); 
                $arr1 = pg_fetch_all_columns($res1, 1);  
				if ($arr1!=null){
				   echo "addgranits({coordinates1:[";
                   for( $j=0;$j<count($arr1);$j++)
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
				else
				{
					for ($i=1;$i<=$gr2;$i++)
			{
				if ( (int)$_POST['id']==0)
if ($nazv==null) $res1 = pg_query($dbconn, "select t1.id,t.gran from neft_granits t inner join neft t1 on t1.name=t.name where t1.id='{$i}' order by t1.id;"); 
else $res1 = pg_query($dbconn, "select t1.id,t.gran from neft_granits t inner join neft t1 on t1.name=t.name where t1.id='{$i}' and t1.name='{$nazv}' order by t1.id;");
				else
for ($j=1;$j<=$state2;$j++) { if ( (int)$_POST['id']==$j)
				$res1 = pg_query($dbconn, "select t1.id,t.gran from neft_granits t inner join neft t1 on t1.name=t.name inner join neft_prin t2 on t2.id=t1.id 
			inner join state s1 on s1.country=t2.country where s1.id='{$id}' and t1.id='{$i}' order by t1.id;"); 
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
				}
										 }
if (($num_schv==0)&&(empty($num_schv)))
										 for($i=0;$i<count($arrname);$i++)
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

}

?>
			function find2(properties)
{
			var myCookie = getCookie("id");
                            if(myCookie!=null) 
map.setView(properties.coordinates,30);
						else
						map.setView(properties.coordinates,8);
					if (typeof div !=="undefined" && div!=="0"){ div.parentNode.removeChild(div);}
<?php 
if (empty($_COOKIE['id']) and empty($_COOKIE['hash']))
{	
echo 'if ($(".find").is(":hidden")) {';
echo 'null  } else {  $(".find").hide("slow"); }';
echo 'if ($(".overlay2").is(":hidden")) {';
echo 'null  } else {  $(".overlay2").hide("slow"); }';	
}
?>	
							 div = document.createElement('div');
                             div.className = "overlay";
                             div.innerHTML =
							 '<b><font size=4> Название месторождения:</b> '+properties.name+'</font>'+					 
							 '<br><b><font size=4>Страна:</b>  '+properties.country+'</font>'+
							 '<br><b><font size=4>Год открытия: </b> '+properties.year+'</font>'+
							 '<br><b><font size=4>Операторы: </b> '+properties.company+'</font>'+
							 '<br><b><font size=4>Запасы: </b>  '+properties.zapas+' млрд. тонн нефти'+'</font>'+
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
							var myCookie = getCookie("id");
                            if(myCookie!=null) 
				            map.setView([e.latlng.lat, e.latlng.lng],30);
						else
							map.setView([e.latlng.lat, e.latlng.lng],8);
						
						if (typeof div !=="undefined" && div!=="0"){ div.parentNode.removeChild(div);}
						<?php
						if (empty($_COOKIE['id']) and empty($_COOKIE['hash']))
						{
                        	echo 'if ($(".overlay2").is(":hidden")) {';
	                        echo 'null  } else {  $(".overlay2").hide("slow"); }';	
						}	?>
							 div = document.createElement('div');
                             div.className = "overlay";
                             div.innerHTML =
							 '<b><font size=4> Название месторождения:</b> '+properties.name+'</font>'+					 
							 '<br><b><font size=4>Страна:</b>  '+properties.country+'</font>'+
							 '<br><b><font size=4>Год открытия: </b> '+properties.year+'</font>'+
							 '<br><b><font size=4>Операторы: </b> '+properties.company+'</font>'+
							 '<br><b><font size=4>Запасы: </b>  '+properties.zapas+' млрд. тонн нефти'+'</font>'+
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
			
						 //добавляет скважины
						function addscv(properties)
			{
				qq++;
			arrScv[qq]=DG.circle(properties.coordinates,properties.radius, {color: properties.color2}).bindLabel('Скважина №'+properties.numb_scv).addTo(map);
			//DG.circle([properties.coordinates2], 200, {color: 'red'}).addTo(map);
			group3[qq] = DG.featureGroup([arrScv[qq]]);
			group3[qq].addTo(map);
			  group3[qq].on('click', function(e) {
				   map.setView([e.latlng.lat, e.latlng.lng],30);
				  							if (typeof div4 !=="undefined" && div4!=="0"){ div4.parentNode.removeChild(div4);}			
							 div4 = document.createElement('div4');
                             div4.className = "overlay3";
                             div4.innerHTML =
							 '<b><font size=4>Название месторождения:</b> '+properties.name_mest+'</font>'+					 
							 '<br><b><font size=4>Скважина №:</b>  '+properties.numb_scv+'</font>'+
							 '<br><b><font size=4>Статус: </b><font color="'+properties.color+'">'+properties.status+'</font>'+
							 '<br><b><font size=4>Радиус: </b> '+properties.radius+' (м)</font>'+
							 '<br><b><font size=4>Дебит за сутки: </b> '+properties.debit+'</font>'+
							 '<br><b><font size=4>Ответственное лицо: </b> '+properties.name+'</font>'+
							 '<br><b><font size=4>E-mail: </b>  '+properties.email+''+'</font>'+
							 '<br><b><font size=4>Компания: </b>  '+properties.company+''+'</font>'+
						     '<br><br><center><input type="button" value="Скрыть информацию" onclick="removediv4()"></center>';
                             document.body.appendChild(div4);	
			  });  
            }
			
						function addscv1(properties)
{

				qq++;
			arrScv[qq]=DG.circle(properties.coordinates,properties.radius, {color: properties.color2}).bindLabel('Скважина №'+properties.numb_scv).addTo(map);
			group3[qq] = DG.featureGroup([arrScv[qq]]);
			group3[qq].addTo(map);
				  map.setView(properties.coordinates,30);
				  							if (typeof div4 !=="undefined" && div4!=="0"){ div4.parentNode.removeChild(div4);}			
							 div4 = document.createElement('div4');
                             div4.className = "overlay3";
                             div4.innerHTML =
							 '<b><font size=4>Название месторождения:</b> '+properties.name_mest+'</font>'+					 
							 '<br><b><font size=4>Скважина №:</b>  '+properties.numb_scv+'</font>'+
							 '<br><b><font size=4>Статус: </b><font color="'+properties.color+'">'+properties.status+'</font>'+
							 '<br><b><font size=4>Радиус: </b> '+properties.radius+' (м)</font>'+
							 '<br><b><font size=4>Дебит за сутки: </b> '+properties.debit+'</font>'+
							 '<br><b><font size=4>Ответственное лицо: </b> '+properties.name+'</font>'+
							 '<br><b><font size=4>E-mail: </b>  '+properties.email+''+'</font>'+
							 '<br><b><font size=4>Компания: </b>  '+properties.company+''+'</font>'+
						     '<br><br><center><input type="button" value="Скрыть информацию" onclick="removediv4()"></center>';
                             document.body.appendChild(div4);	



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
						
			function getCookie(name) {
    var dc = document.cookie;
    var prefix = name + "=";
    var begin = dc.indexOf("; " + prefix);
    if (begin == -1) {
        begin = dc.indexOf(prefix);
        if (begin != 0) return null;
    }
    else
    {
        begin += 2;
        var end = document.cookie.indexOf(";", begin);
        if (end == -1) {
        end = dc.length;
        }
    }
    return decodeURI(dc.substring(begin + prefix.length, end));
} 


</script>
