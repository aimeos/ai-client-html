<?php

return [
	'name' => 'ai-client-html',
	'depends' => [
		'aimeos-core',
		'ai-controller-frontend',
	],
	'include' => [
		'src',
	],
	'i18n' => [
		'client' => 'i18n',
		'client/code' => 'i18n/code',
	],
	'config' => [
		'config',
	],
	'template' => [
		'client/html/templates' => [
			'templates/client/html',
		],
	],
];
