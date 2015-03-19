<?php

return CMap::mergeArray(
	require(dirname(__FILE__).'/main.php'),
	require(dirname(__FILE__).'/components_dev.php')
);
