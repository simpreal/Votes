<!DOCTYPE html>
<html>
<head>
<meta content="text/html; charset=utf-8" http-equiv="content-type">
</head>
<body>
	<h2>Список участников</h2>
    <style>td,th{border:1px solid black;}table{border-collapse:collapse;}</style>
    <table><tr><th>Имя</th><th>Отклонен</th></tr>
 	<?
		include "connect.php";
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