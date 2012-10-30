<html>
<head>
</head>
<body>
	<h2>Список участников</h2>
    <table><tr><th>Имя</th><th>Отклонен</th></tr>
 	<?
		mysql_connect('localhost','root','');
		mysql_select_db('votes');
		$rez=mysql_query('SELECT PublicName, IncorrectFlag FROM users ORDER BY ID');
   		if($num=mysql_num_rows($rez)){
			for($i=0;$i<$num; $i++){
				$row=mysql_fetch_row($rez);
				echo '<tr><td>'.implode('</td><td>',$row).'</td></tr>';
			}
		}
	?>
    </table>
</body>
</html>