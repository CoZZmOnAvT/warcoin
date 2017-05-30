<div id="roulette">
		<ul>
			<li><img src="/resource/images/weapons/1.png" height="130px" width="230px" align="right" /></li>
			<li><img src="/resource/images/weapons/2.png" height="130px" width="230px" align="right" /></li>	
			<li><img src="/resource/images/weapons/12.png" height="130px" width="230px" align="right" /></li>					
			<li><img src="/resource/images/weapons/5.png" height="130px" width="230px" align="right" /></li>	
			<li><img src="/resource/images/weapons/9.png" height="130px" width="230px" align="right" /></li>	
			<li><img src="/resource/images/weapons/7.png" height="130px" width="230px" align="right" /></li>	
			<li><img src="/resource/images/weapons/11.png" height="130px" width="230px" align="right" /></li>	
			<li><img src="/resource/images/weapons/10.png" height="130px" width="230px" align="right" /></li>	
			<li><img src="/resource/images/weapons/6.png" height="130px" width="230px" align="right" /></li>	
			<li><img src="/resource/images/weapons/13.png" height="130px" width="230px" align="right" /></li>	
			<li><img src="/resource/images/weapons/8.png" height="130px" width="230px" align="right" /></li>	
			<li><img src="/resource/images/weapons/3.png" height="130px" width="230px" align="right" /></li>
			<li><img src="/resource/images/weapons/4.png" height="130px" width="230px" align="right" /></li>
		</ul>
		<div id="loading"><img src="/resource/images/loading.gif" /></div>
		<div id="scope"></div>
		<div id="scopeBack"></div>		
</div>
<div class="playButton" onclick="askForSpin(this);"><p>Играть</p>
	<div id="hint"><p>Стоимость одной игры - <span style="color:#b56029;font-size:18px;"><?php echo rouletteSpinPrice; ?> КРЕДИТОВ</span></p></div>
</div>
<?php
if($_SESSION['user_data']['test_spin'])
echo '
<div class="playButton test" onclick="askForSpin(this);"><p style="font-size:30px">Пробная игра</p>
	<div id="hint"><p>Вы можете испытать рулетку <span style="color:#b56029;font-size:18px;">бесплатно</span> 1 раз.</p></div>
</div>';
?>
<div id="subLayotWrapper">
	<header>
		<p>Что выпадает?</p>
		<div id="hint">
			<p>Ниже предоставлен ассортимент оружия,<br> который имеет определённую вероятность стать вашим после игры.</p>
		</div>
	</header>
	<table>
		<tr>
			<td><ul>
				<li><img src="/resource/images/weapons/12.png"></li>
				<li class="gold">10 000 Кредитов</li>
			</ul></td>
			<td><ul>
				<li><img src="/resource/images/weapons/11.png"></li>
				<li class="gold">8 000  Кредитов</li>
			</ul></td>
			<td><ul>
				<li><img src="/resource/images/weapons/10.png"></li>
				<li class="gold">7 000  Кредитов</li>
			</ul></td>
			<td><ul>
				<li><img src="/resource/images/weapons/9.png"></li>
				<li class="gold">6 000 Кредитов</li>
			</ul></td>
		</tr>
		<tr>
			<td><ul>
				<li><img src="/resource/images/weapons/8.png"></li>
				<li class="silver">5 000 Кредитов</li>
			</ul></td>
			<td colspan="2" rowspan="2" id="mainPrize"><ul>
				<li><img src="/resource/images/weapons/13_mainPrize.png"></li>
				<li>20 000 кредитов</li>
			</ul></td>			
			<td><ul>
				<li><img src="/resource/images/weapons/7.png"></li>
				<li class="silver">4 000 Кредитов</li>
			</ul></td>
		</tr>
		<tr>
			<td><ul>
				<li><img src="/resource/images/weapons/6.png"></li>
				<li class="silver">3 000 Кредитов</li>
			</ul></td>			
			<td><ul>
				<li><img src="/resource/images/weapons/5.png"></li>
				<li class="silver">1 500 Кредитов</li>
			</ul></td>
		</tr>
		<tr>
			<td><ul>
				<li><img src="/resource/images/weapons/4.png"></li>
				<li>600 Кредитов</li>
			</ul></td>
			<td><ul>
				<li><img src="/resource/images/weapons/3.png"></li>
				<li>250 Кредитов</li>
			</ul></td>
			<td><ul>
				<li><img src="/resource/images/weapons/2.png"></li>
				<li>100 Кредитов</li>
			</ul></td>
			<td><ul>
				<li><img src="/resource/images/weapons/1.png"></li>
				<li>50 Кредитов</li>
			</ul></td>
		</tr>
	</table>
</div>
<div id="prize">
	
</div>