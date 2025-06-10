<?php

return [
	'html' => [
		'common' => [
			'address' => [
				'delivery' => [
					'mandatory' => [
						'firstname',
						'lastname',
						'address1',
						'postal',
						'city',
						'languageid',
					],
					'optional' => [
						'salutation',
						'company',
						'vatid',
						'address2',
						'countryid',
						'state',
					]
				],
				'payment' => [
					'mandatory' => [
						'firstname',
						'lastname',
						'address1',
						'postal',
						'city',
						'languageid',
						'email',
					],
					'optional' => [
						'salutation',
						'company',
						'vatid',
						'address2',
						'countryid',
						'state',
					]
				],
				'salutations' => ['', 'mr', 'ms'],
				'validate' => []
			],
			'decorators' => [
				'default' => [
					'Exceptions' => 'Exceptions',
					'Context' => 'Context',
				]
			],
		],
		'catalog' => [
			'detail' => [
				'url' => [
					'filter' => ['path', 'd_prodid'] // Remove path and product ID from URLs, only use URL segment
				]
			],
			'filter' => [
				'subparts' => [/*'tree',*/ 'search', 'price', 'supplier', 'attribute']
			],
			'lists' => [
				'items' => [
					'template-body-list' => 'catalog/lists/items-body-list',
				],
			],
			'tree' => [
				'url' => [
					'filter' => ['path'] // Remove path from URLs by default
				]
			],
		],
		'themes' => [
			'default' => 'Default',
		],
		'theme-presets' => [
			'default' => [
				'--ai-product-image-ratio' => '3/4',
				'--ai-bg' => '#FFFFFF',
				'--ai-bg-alt' => '#F6F6F6',
				'--ai-primary' => '#282828',
				'--ai-primary-alt' => '#282828',
				'--ai-secondary' => '#555555',
				'--ai-secondary-alt' => '#555555',
				'--ai-tertiary' => '#CCCCCC',
				'--ai-tertiary-alt' => '#F6F6F6',
				'--ai-danger' => '#A00000',
				'--ai-success' => '#006000',
				'--ai-warning' => '#FFA500',
				'--ai-radius' => '0',
			]
		]
	]
];
