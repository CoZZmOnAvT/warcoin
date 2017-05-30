<?php
	define('rouletteCells', 10000);
							//Price
	$weaponsPrices = array(	array( 50, 100, 250, 600 ),
							array( 1500, 3000, 4000, 5000 ),
							array( 6000, 7000, 8000, 10000 ),
							array( 20000));
							//Img Url's
	$weaponsImgs = array(	50 		=> '/resource/images/weapons/1.png',
							100 	=> '/resource/images/weapons/2.png',
							250		=> '/resource/images/weapons/3.png',
							600		=> '/resource/images/weapons/4.png',
							1500 	=> '/resource/images/weapons/5.png',
							3000	=> '/resource/images/weapons/6.png',
							4000 	=> '/resource/images/weapons/7.png',
							5000 	=> '/resource/images/weapons/8.png',
							6000 	=> '/resource/images/weapons/9.png',
							7000 	=> '/resource/images/weapons/10.png',
							8000 	=> '/resource/images/weapons/11.png',
							10000 	=> '/resource/images/weapons/12.png',
							20000 	=> '/resource/images/weapons/13.png');
	//Percents of (def)rouletteCells
	$weaponsInterestRate = array(
		//percents for COMMON user
		'common'	=>	array(	array(53, 34, 8, 4.55),
								array(0.09, 0.08, 0.07, 0.06),
								array(0.05, 0.04, 0.03, 0.02),
								array(0.01)),
		//percents for TEST spin
		'test'		=>	array(	array(0, 0, 0, 25),
								array(25, 25, 24.8, 0),
								array(0, 0, 0, 0),
								array(0.2)),
		//percents for GOD user
		'god'		=> 	array(	array(14, 13, 12, 11),
								array(10, 9, 8, 7),
								array(5, 4, 3.5, 3),
								array(0.5)),
		//percents for YOUTUBE user
		'youtube'	=> 	array(	array(20, 35, 27, 17),
								array(0.3, 0.19, 0.18, 0.17),
								array(0.06, 0.04, 0.03, 0.02),
								array(0.01)));