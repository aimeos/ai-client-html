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
			'levels-always' => 3, // show always three category levels for megamenu
			'levels-only' => 3, // don't load more then three category levels for megamenu
		]
	]
];
