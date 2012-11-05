<!DOCTYPE html>
<html>
<head>
<meta content="text/html; charset=utf-8" http-equiv="content-type">
<title>Система голосования стоимостью в $10</title>
</head>
<body>
	<h1>Модель системы голосования стоимостью в $10. <span class='descr'>Потому что все просто.</span></h1>
    <a href="javascript:setdisplay('descr','none');setdisplay('anchor2','inline');" class="descr">Скрыть пояснения</a>
    <a href="javascript:setdisplay('anchor2','none');setdisplay('descr','inline');" style="display:none;" class="anchor2">Показать пояснения</a>
    <hr />
	<h2>1. Регистрация</h2>
    <span class='descr'>Регистрация подобна тому как вы идентифицируете себя на избирательном участке, когда приходите голосовать. Вы показываете паспорт и напротив вашей фамилии ставят галочку, что вы пришли, и дают бюллетень для голосования.</span>
    <?
		$numInGroup=7;
		$pauseTime=0;
		$messages='';
		if(!is_null($_POST['action']) && $_POST['action']=='makegroup'){
			sleep($pauseTime);
			if(is_null($_POST['PublicName']) || !strlen($_POST['PublicName'])){
				$messages='Укажите Ваше имя';
			}
			else{
				$_POST['PublicName']=strtolower($_POST['PublicName']);
				$SQLPublicName=mysql_escape_string($_POST['PublicName']);
				include "connect.php";
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
					
					$q="INSERT INTO users SET IncorrectFlag=0, PublicName='".$SQLPublicName."', GroupCode=IFNULL((SELECT GroupCode FROM (SELECT GroupCode, COUNT(*) as num FROM users WHERE IncorrectFlag=0 GROUP BY GroupCode) as t1 WHERE num<".$numInGroup." LIMIT 1),'".$GroupCode."')";
					mysql_query($q);
					$rez=mysql_query('SELECT GroupCode FROM users WHERE ID="'.mysql_insert_id().'"');
					if(mysql_num_rows($rez)>0){
						$goodmessages='Ваша группа: '.mysql_result($rez,0).'</br>ОБЯЗАТЕЛНО! запишите этот код, так как повторно его нельзя будет создать.';
					}
				}
			}
		}
	?>
	<form method="POST">
    	<input type="hidden" name="action" value="makegroup" />
    	Итак, В идеале нужно ввести через запятую страну, область, город, улицу, номер дома, номер квартиры, ФИО.<br />
        (Например: "украина,киевская,киев,ленина,1,1,иванов иван иванович"). Но если честно, то проверки формата нет. <br />
        <input type="text" style="width:500px;" name="PublicName"/><br />
        <span class='descr'>Этим вы подтверждаете, что вы <b>реальный человек с реальным адресом</b>, а не выдуманный персонаж. В официальной системе, конечно нужно сделать, чтобы человек выбирал себя из списка. Но так как у автора системы не было списка всех жителей страны, то приходится эту информацию вводить руками.<br /></span>
        <input type="submit" value="Получить секретный код группы" /><br />
        <span class='descr'>Нажав эту кнопку вы регистрируетесь в системе и вам присваивается уникальный код группы. Можете считать что это номер вашего избирательного участка, только он виртуальный и его знаете только вы и система голосования. Не зная этого кода, проголосовать невозможно.<br />
        Сейчас система настроена так, что в группе должно быть <?= $numInGroup ?> человек. Отсюда следует, что никто кроме вас <b>(даже система)</b> не будет знать за кого Вы проголосовали. Этим обеспечивается <b>анонимность</b>. <br /></span>
    </form>
    <p><span style="color:red;"><?= $messages ?></span><span style="color:green;"><?= $goodmessages ?></span></p>
    <span class='descr'>Кроме этого можно вывести список всех участников, чтобы все знали кто принял участие в голосовании:<br /></span>
    <a href="allusers.php" target="_blank" >Список участников</a><br />
    <span class='descr'>Но пока еще это спорный вопрос.</span>
    <hr />
	<h2>2. Голосование</h2>
    <span class='descr'>Голосование подобно тому, как вы идете в кабинку и ставите в бюллетене плюсик напротив кандидата.
    Но с некоторыми бонусами:
    <ul>
        <li>Во первых, <b>может я не прав</b>, но в стандартной системе голосования Ваша фамилия сопоставляется с номером бюллетеня, на котором вы будете голосовать, чем снижается анонимность. В предлагаемой же системе никто не будет знать за кого вы проголосовали, так как нет точной связи регистрации с голосованием.</li>
        <li>Во вторых, вы всегда можете проверить ваш голос в итоговом списке голосования и убедится что ваш голос учтен.</li>
        <li>В третьих, результаты голосования открыты. Поэтому вы можете сами посчитать результат выборов.</li>
    </ul></span>
    <?
			$messages2='';
		if(!is_null($_POST['action']) && $_POST['action']=='vote'){
			sleep($pauseTime);
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
				$SQLSecretCode=mysql_escape_string($_POST['SecretCode']);
			
				include "connect.php";
				$rez=mysql_query('SELECT COUNT(GroupCode) FROM users WHERE IncorrectFlag=0 AND GroupCode="'.$SQLGroupCode.'"');
				if(mysql_result($rez,0)==0){
					$messages2='Эта группа не зарегестрированна';
				}
				else if(mysql_result(mysql_query($q='
					SELECT COUNT(GroupCode) FROM votes WHERE SecretCode="'.$SQLSecretCode.'" AND GroupCode IN (SELECT DISTINCT GroupCode FROM users WHERE IncorrectFlag=0 AND GroupCode!="'.$SQLGroupCode.'")'
				),0)>0){
						
						$messages2='Этот секретный код уже есть в другой группе';
				}
				else{
					if(mysql_query("INSERT INTO votes SET
						GroupCode='".$SQLGroupCode."',
						SecretCode='".$SQLSecretCode."',
						Vote='".mysql_escape_string($_POST['Vote'])."'
					"))
						$goodmessages2='Голос принят';
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
	<form method="POST">
    	<input type="hidden" name="action" value="vote" />
    <div>
    	Код группы (выданный в первом шаге) <input type="text" name="GroupCode" value='<?= $_POST['GroupCode'] ?>'/><br />
        <span class='descr'>С помощью группы проверяется корректность голосов. Если количество проголосовавших больше чем количество людей в группе, то результат этой группы считается некорректным, и всем из этой группы нужно заново пройти первый шаг </span>
    </div>
    <div>
    	Секретный код <input type="text" name="SecretCode" value='<?= $_POST['SecretCode'] ?>' /><br />
    	<span class='descr'>Код нужен для того чтобы вы потом смогли проверить достоверность выборов. Его знаете только вы. Этим обеспечивается анонимность.</span>
    </div>
    <div>Ваш голос <input type="text" name="Vote" value='<?= $_POST['Vote'] ?>' /> </div>
    <input type="submit" value="Голосовать" />
   	<span class='descr'><br />Вы можете изменить свой голос. Для этого нужно повторно проголосовать, но обязательно с таким же кодом группы и с таким же секретным кодом.</span>
    </form>
    <p><span style="color:red;"><?= $messages2 ?></span><span style="color:green;"><?= $goodmessages2 ?></span></p>
    <span class='descr'>После окончания выборов всем можно будет скачать результат голосования, где каждый сможет проверить свой голос и самостоятельно посчитать результаты:<br /></span>
    <a href="allvotes.php" target="_blank" >Список голосов</a>
    <script type="text/javascript">
		function setdisplay(classname,display){
			var els=document.getElementsByTagName('*');
			for(var i=0,n=els.length;i<n;i++){
				var el=els[i];
				if(el.className==classname)
					el.style.display=display;
			}
		}
	</script>
</body>
</html>