<?php

return array(
	'name' => 'ai-client-html',
	'depends' => array(
		'aimeos-core',
		'ai-controller-frontend',
	),
	'include' => array(
		'client/html/src',
		'controller/jobs/src',
		'controller/common/src',
	),
	'i18n' => array(
		'client' => 'client/i18n',
		'client/code' => 'client/i18n/code',
		'client/country' => 'client/i18n/country',
		'client/currency' => 'client/i18n/currency',
		'client/language' => 'client/i18n/language',
	),
	'config' => array(
		'client/html/config',
		'config',
	),
	'custom' => array(
		'client/html/templates' => array(
			'client/html/templates',
		),
		'controller/jobs/templates' => array(
			'client/html/templates',
		),
		'controller/jobs' => array(
			'controller/jobs/src',
		),
	),
);
