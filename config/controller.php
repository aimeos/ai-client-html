<?php

return [
	'common' => [
		'subscription' => [
			'process' => [
				'processors' => [
					'Email' => 'Email',
				],
			],
		],
	],
	'frontend' => [
		'catalog' => [
			'levels-always' => 4, // show always four category levels for megamenu
			'levels-only' => 4, // don't load more then four category levels for megamenu
		]
	]
];
