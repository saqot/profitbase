<?php

namespace App\Extensions;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\DefaultQuoteStrategy;
use Doctrine\ORM\Mapping\QuoteStrategy;

/**
 * class:  MappingDoctrineQuoteStrategy
 * -----------------------------------------------------
 * @author  Saqot (Mihail Shirnin) <saqott@gmail.com>
 * @package  App\Extensions
 * -----------------------------------------------------
 */
class MappingDoctrineQuoteStrategy extends DefaultQuoteStrategy implements QuoteStrategy
{

	/**
	 * Включаем quoted для имен полей
	 * @param $fieldName
	 * @param ClassMetadata $class
	 * @param AbstractPlatform $platform
	 * @return mixed|string
	 */
	public function getColumnName($fieldName, ClassMetadata $class, AbstractPlatform $platform)
	{
		$class->fieldMappings[$fieldName]['quoted'] = true;

		return parent::getColumnName($fieldName, $class, $platform);
	}

	/**
	 * Включаем quoted для имен таблиц
	 * @param ClassMetadata $class
	 * @param AbstractPlatform $platform
	 * @return mixed|string
	 */
	public function getTableName(ClassMetadata $class, AbstractPlatform $platform)
	{
		$class->table['quoted'] = true;

		return parent::getTableName($class, $platform);
	}
}