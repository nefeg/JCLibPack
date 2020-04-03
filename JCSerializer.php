<?php

namespace JCLibPack;

use JMS\Serializer\Expression\ExpressionEvaluator;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * Class JCSerializer
 *
 * @package JCLibPack
 */
class JCSerializer
{
	/**
	 * @param       $data
	 * @param bool  $serializeNull
	 * @param array $groups
	 * @param bool  $useExpressions
	 * @return string
	 */
	static public function serializeToJSON($data, array $groups = [], bool $serializeNull = true, bool $useExpressions = true) :string{

		return static::serialize($data, 'json', $serializeNull, $groups, $useExpressions);
	}

	/**
	 * @param       $data
	 * @param array $groups
	 * @param bool  $serializeNull
	 * @param bool  $useExpressions
	 * @return array
	 */
	static public function serializeToArray($data, array $groups = [], bool $serializeNull = true, bool $useExpressions = true) :array{
		return is_array($data) ? $data : static::deserialize(
			static::serialize($data, 'json', $serializeNull, $groups, $useExpressions)
		);
	}

	/**
	 * @param        $data
	 * @param string $format
	 * @param bool   $serializeNull
	 * @param array  $groups
	 * @param bool   $useExpressions
	 * @return mixed
	 */
	static public function serialize($data, $format = 'json', bool $serializeNull = true, array $groups = [], bool $useExpressions = true) {

		$groups = count($groups) ? $groups : ['Default'];

		$Serializer = SerializerBuilder::create();
		if ($useExpressions)
			$Serializer->setExpressionEvaluator(new ExpressionEvaluator(new ExpressionLanguage()));

		return $Serializer->build()->serialize(
			$data,
			$format,
			(new SerializationContext())->setSerializeNull($serializeNull)->setGroups($groups)
		);
	}



	/**
	 * @param $content
	 * @param string $type
	 * @param string $format
	 * @return array|\JMS\Serializer\|mixed|object
	 */
	static function deserialize($content, $type = 'array', $format = 'json'){
		$serializer = SerializerBuilder::create()->build();

		return $serializer->deserialize(
			$content,
			$type,
			$format
		);
	}



}