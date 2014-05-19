<?php

if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
{
	$ssi = true;
	require_once(dirname(__FILE__) . '/SSI.php');
}
elseif (!defined('SMF'))
	exit('<strong>Error:</strong> Cannot install - please verify you put this in the same place as SMF\'s index.php.');

global $smcFunc, $db_prefix;

if (!array_key_exists('db_add_column', $smcFunc))
	db_extend('packages');

$columns = $smcFunc['db_list_columns']('{db_prefix}topics');

if (!in_array('is_global', $columns))
	$smcFunc['db_add_column']('{db_prefix}topics', array(
													'name' => 'is_global',
													'type' => 'tinyint',
													'size' => 4,
													'default' => 0,
													'null' => false,
													)
												);

$columns = $smcFunc['db_list_columns']('{db_prefix}boards');

if (!in_array('global_topics', $columns))
	$smcFunc['db_add_column']('{db_prefix}boards', array(
													'name' => 'global_topics',
													'type' => 'tinyint',
													'size' => 4,
													'default' => 0,
													'null' => false,
													)
												);

$result = $smcFunc['db_query']('', '
	SELECT COUNT(*)
	FROM {db_prefix}boards
	WHERE global_topics = {int:enabled}',
	array(
		'enabled' => 1,
	)
);
list ($current_boards) = $smcFunc['db_fetch_row']($result);
$smcFunc['db_free_result']($result);

if (empty($current_boards))
{
	$smcFunc['db_query']('', '
		UPDATE {db_prefix}boards
		SET global_topics = {int:enabled}
		WHERE global_topics != {int:enabled}',
		array(
			'enabled' => 1,
		)
	);
}

if (!empty($ssi))
	echo 'Database installation complete!';

?>