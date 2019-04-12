   <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.2.3/jquery.min.js"></script>
   <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
   <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<?php  
include "connect.php";
//echo 'Пример 2 - передача завершилась успешно. Параметры: name = ' . $_POST['id'];

$state = pg_query($dbconn, "SELECT * from state");
$state2=pg_num_rows($state);
$name_mest=$_POST['nazv'];

if ( (int)$_POST['id']==0) $name = pg_query($dbconn, "SELECT name from neft where koord is not null order by name;");  
									else
for ($j=1;$j<=$state2;$j++) { if ( (int)$_POST['id']==$j)
				$name = pg_query($dbconn, "select t1.name from neft t1 inner join neft_prin t2 on t1.id=t2.id 
			inner join state s1 on s1.country=t2.country
			where s1.id='{$j}' order by name;"); 
									}
								
									$namer = pg_fetch_all_columns($name, 0);
									
								if (isset($_COOKIE['id']) and isset($_COOKIE['hash']))
								{
									// если залогинен;
									$numb_scv = pg_query($dbconn, "select number_scv from scvazh where name_mest='{$name_mest}' order by number_scv;"); 
                                    $numb_scv2 = pg_fetch_all_columns($numb_scv, 0);		
//echo '<br><input type=text placeholder="Выберите скважину" autocomplete=off  list="neft" name=nazv_scvazh id="idd2"><br><br>';

				     		echo '<select name="num_schv" id="n_schv">';
							echo '<option disabled>Выберите скважину</option>';
						    echo '<option value="0">Все скважины</option>';
									
								//	 echo "<datalist id='neft'>";									
									 for($i=0;$i<count($numb_scv2);$i++)
 {
echo "<option>"; echo $numb_scv2[$i]; echo"</option>";
 }
 echo "</select>";
// echo "</datalist>"; 
								}		
									/// если не залогинен 
									else 
									{
									 echo "<datalist id='neft'>";
									 echo "<select>";
 for($i=0;$i<count($namer);$i++)
 {
echo "<option>"; echo $namer[$i]; echo"</option>";
 }
 echo "</select>";
 echo "</datalist>";
								}
?>
<script>
$(document).ready(function(){
$('#n_schv').change(function(){
$.ajax({
  type: 'POST',
  url: 'show2.php?action=sample2',
 data:  "nazv="+$("#idd2").val()+"&num_schv="+$("#n_schv").val(),
  success: function(data){
	$('.results').html(data);
  }
  });
  
});
    });
	</script>
