<?php require_once('include/siteScripts/topPlayersData.php'); ?>
<div id="topPlayersBlockWrapper">
	<header>
		<p>ЛУЧШИЕ ИГРОКИ</p>
		<p>Топ 100 игроков</p>
	</header>
	<div id="bestPlayers">
		<div id="firstPlace">
			<img src="<?php echo $topPlayersData[0]['photo_200']; ?>" />
			<p><?php echo $topPlayersData[0]['user_name']; ?></p>
			<p><?php echo $topPlayersData[0]['games_count']; ?> ИГР</p>
			<a href="<?php echo '/profile/'.$topPlayersData[0]['user_id']; ?>"></a>
		</div>
		<div id="secondPlace">
			<img src="<?php echo $topPlayersData[1]['photo_200']; ?>" />
			<p><?php echo $topPlayersData[1]['user_name']; ?></p>
			<p><?php echo $topPlayersData[1]['games_count']; ?> ИГР</p>
			<a href="<?php echo '/profile/'.$topPlayersData[1]['user_id']; ?>"></a>
		</div>
		<div id="thirdPlace">
			<img src="<?php echo $topPlayersData[2]['photo_200']; ?>"/>
			<p><?php echo $topPlayersData[2]['user_name']; ?></p>
			<p><?php echo $topPlayersData[2]['games_count']; ?> ИГР</p>
			<a href="<?php echo '/profile/'.$topPlayersData[2]['user_id']; ?>"></a>
		</div>
	</div>
	<table id="playersList">
		<tr>
			<th>№</th>
			<th>ПОЛЬЗОВАТЕЛЬ</th>
			<th>ИГР СЫГРАНО</th>
		</tr>
<?php

if($user_pos > 9)
{
	echo '	
		<tr id="user">
			<td>'.($user_pos + 1).'</td>
			<td>
				<div>
					<img src="'.$topPlayersData[$user_pos]['photo_50'].'" />
					<span>'.$topPlayersData[$user_pos]['user_name'].'</span>
					<a href="/profile/'.$topPlayersData[$user_pos]['user_id'].'"></a>
				</div>				
			</td>
			<td>'.$topPlayersData[$user_pos]['games_count'].'</td>
		</tr>
		<tr><td id="dottedCell" colspan="4"></td></tr>';
	unset($topPlayersData[$user_pos]);
}
for($i = 0; $i<3;$i++)
	unset($topPlayersData[$i]);
foreach ($topPlayersData as $numm => $data){
	echo '
		<tr>
			<td>'.($numm + 1).'</td>
			<td>
				<div>
					<img src="'.$data['photo_50'].'" />
					<span>'.$data['user_name'].'</span>
					<a href="/profile/'.$data['user_id'].'"></a>
				</div>
			</td>
			<td>'.$data['games_count'].'</td>
		</tr>';
}
?>		
	</table>
</div>