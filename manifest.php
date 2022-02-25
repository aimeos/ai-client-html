<?php

return [
	'name' => 'ai-client-html',
	'depends' => [
		'aimeos-core',
		'ai-controller-frontend',
	],
	'include' => [
		'lib/custom/src',
		'client/html/src',
	],
	'i18n' => [
		'client' => 'client/i18n',
		'client/code' => 'client/i18n/code',
	],
	'config' => [
		'config',
	],
	'template' => [
		'client/html/templates' => [
			'client/html/templates',
		],
	],
];
