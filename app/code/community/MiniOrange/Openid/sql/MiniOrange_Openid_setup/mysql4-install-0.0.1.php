<?php

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->getConnection()->addColumn($this->getTable('admin/user'), 'miniorange_openid_email', 'varchar(128) null');

$installer->endSetup();