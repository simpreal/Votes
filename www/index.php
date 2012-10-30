<html>
<head>
</head>
<body>
	<h2>1. Регистрация</h2>
    
    Этот шаг нужен для того чтобы:
    <ul>
    	<li>Все знали что вы приняли участие в голосовани.</li>
    	<li>Исключить имитацию голосования от вашего имени.</li>
    	<li>Обеспечить Вашу анонимности в пределах одной группы.</li>
    </ul>
    
    <?
		$numInGroup=5;
		$messages='';
		if(!is_null($_POST['action']) && $_POST['action']=='makegroup'){
			sleep(5);
			if(is_null($_POST['PublicName']) || !strlen($_POST['PublicName'])){
				$messages='Укажите Ваше имя';
			}
			else{
				$SQLPublicName=mysql_escape_string($_POST['PublicName']);
			
				mysql_connect('localhost','root','');
				mysql_select_db('votes');
				$rez=mysql_query('SELECT GroupCode FROM users WHERE IncorrectFlag=0 AND PublicName="'.$SQLPublicName.'" LIMIT 1');
				if(mysql_num_rows($rez)>0){
					$messages='Для вас уже была сгенерированна группа';
				}
				else{
					$chars="qwertyuiopasdfghjklzxcvbnm1234567890";
					$nchars=strlen($chars)-1;
					$GroupCode=str_repeat('a',20);
					for($i=0;$i<20;$i++)
						$GroupCode[$i]=$chars[rand(0,$nchars)];
					
					$q="INSERT INTO users SET IncorrectFlag=0, PublicName='".$SQLPublicName."', GroupCode=IFNULL((SELECT GroupCode FROM (SELECT GroupCode, COUNT(*) as num FROM users WHERE IncorrectFlag=0 GROUP BY GroupCode) as t1 WHERE num<".$numInGroup."),'".$GroupCode."')";
					mysql_query($q);
					$messages=$q;
					$rez=mysql_query('SELECT GroupCode FROM users WHERE ID="'.mysql_insert_id().'"');
					if(mysql_num_rows($rez)>0){
						$messages='Ваша группа: '.mysql_result($rez,0);
					}
				}
			}
		}
	?>
    <p><?= $messages ?></p>
	<form method="POST">
    	<input type="hidden" name="action" value="makegroup" />
    	Ваши публичные данные: <input type="text" name="PublicName"/> <input type="submit" value="Получить секретный код группы" />
    </form>
    <a href="allusers.php" target="_blank" >Список участников</a>
    <hr />
	<h2>2. Голосование</h2>
    <?
			$messages2='';
		if(!is_null($_POST['action']) && $_POST['action']=='vote'){
			sleep(5);
			if(is_null($_POST['GroupCode']) || !strlen($_POST['GroupCode'])){
				$messages2='Укажите код группы';
			}
			else if(is_null($_POST['SecretCode']) || !strlen($_POST['SecretCode'])){
				$messages2='Укажите Ваш секретный код';
			}
			else if(is_null($_POST['Vote']) || !strlen($_POST['Vote'])){
				$messages2='Укажите Ваш голос';
			}
			else{
				$SQLGroupCode=mysql_escape_string($_POST['GroupCode']);
			
				mysql_connect('localhost','root','');
				mysql_select_db('votes');
				$rez=mysql_query('SELECT GroupCode FROM users WHERE IncorrectFlag=0 AND GroupCode="'.$SQLGroupCode.'" LIMIT 1');
				if(mysql_num_rows($rez)==0){
					$messages2='Эта группа не зарегестрированна';
				}
				else{
					if(mysql_query("INSERT INTO votes SET
						GroupCode='".$SQLGroupCode."',
						SecretCode='".mysql_escape_string($_POST['SecretCode'])."',
						Vote='".mysql_escape_string($_POST['Vote'])."'
					"))
						$messages2='Голос принят';
					else{
						$messages2='Ошибка:'.mysql_error();
						
					}
					$rez1=mysql_query('SELECT COUNT(DISTINCT SecretCode) FROM votes WHERE GroupCode="'.$SQLGroupCode.'"');
					$rez2=mysql_query('SELECT COUNT(ID) FROM users WHERE GroupCode="'.$SQLGroupCode.'"');
					if(mysql_result($rez1,0) > mysql_result($rez2,0)){
						$messages2='Превышено количество прогролосовавших в этой группе';
						mysql_query('UPDATE users SET IncorrectFlag=1 WHERE GroupCode="'.$SQLGroupCode.'"');
					}
				}
			}
		}
	?>
	<p><?= $messages2 ?></p>
    <form method="POST">
    	<input type="hidden" name="action" value="vote" />
    <div>
    	Код группы (выданный в первом шаге) <input type="text" name="GroupCode" value='<?= $_POST['GroupCode'] ?>'/><br />
        С помощью группы проверяется корректность голосов. Если количество проголосовавших больше чем возможное количество людей в группе, то результат этой группы считается некорректным, и всем из этой группы нужно заново пройти первый шаг 
    </div>
    <div>
    	Секретный код <input type="text" name="SecretCode" value='<?= $_POST['SecretCode'] ?>' /><br />
    	Код нужен для того чтобы вы потом смогли проверить достоверность выборов. Его знаете только вы. Этим обеспечивается анонимность.
    </div>
    <div>Ваш голос <input type="text" name="Vote" value='<?= $_POST['Vote'] ?>' /> </div>
    <input type="submit" value="Голосовать" />
    </form>
    <a href="allvotes.php" target="_blank" >Список голосов</a>
</body>
</html>