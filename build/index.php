<?php

include_once(dirname(__FILE__).'/app/frame/core/Core.php');

Core::getInstance(array('app' => 'frontend3'
						, 'model' => 'production3'
						, 'document_root' => dirname(__FILE__).'/'
						, 'app_root' => dirname(__FILE__).'/app/'
						))->proccess();
