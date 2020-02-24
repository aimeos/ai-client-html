<?php

return array(
	'name' => 'ai-client-html',
	'depends' => array(
		'aimeos-core',
		'ai-controller-frontend',
	),
	'include' => array(
		'lib/custom/src',
		'client/html/src',
		'controller/jobs/src',
		'controller/common/src',
	),
	'i18n' => array(
		'client' => 'client/i18n',
		'client/code' => 'client/i18n/code',
	),
	'config' => array(
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
