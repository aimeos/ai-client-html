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
		'controller/jobs/src',
		'controller/common/src',
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
		'controller/jobs/templates' => [
			'client/html/templates',
		],
	],
	'custom' => [
		'controller/jobs' => [
			'controller/jobs/src',
		],
	],
];
