<html>
<head>
</head>
<body>
	<h2>Список голосов</h2>
    <table><tr><th>Секретный код</th><th>Голос</th><th>Отклонен</th></tr>
 	<?
		mysql_connect('localhost','root','');
		mysql_select_db('votes');
		$rez=mysql_query('SELECT SecretCode, Vote, IncorrectFlag FROM votes LEFT JOIN (SELECT DISTINCT GroupCode, IncorrectFlag FROM users) as t1 ON t1.GroupCode=votes.GroupCode ORDER BY votes.ID');
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