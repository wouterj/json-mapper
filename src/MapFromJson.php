<?php

/*
 * This file is part of the WouterJ JSON Mapper package.
 *
 * (c) Wouter de Jong <wouter@wouterj.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WouterJ\JsonMapper;

trait MapFromJson
{
    public static function fromJson(string $json): static
    {
        $targetClass = static::class;
        $serialized = serialize(json_decode($json));
        $refl = new \ReflectionClass($targetClass);
        
        $serialized = preg_replace_callback('/(\d+):"(\w+_\w+)"/', function ($m) use ($targetClass) {
            if (property_exists($targetClass, $m[2])) {
                return $m[0];
            }

            $newPropertyName = preg_replace_callback('/(\w+?)_(\w+?)/', fn ($n) => $n[1].ucfirst($n[2]), $m[2]);

            return sprintf('%d:"%s"', strlen($newPropertyName), $newPropertyName);
        }, $serialized);
        
        $serialized = preg_replace_callback('/"(\w+)";O:8:"stdClass"/', function ($m) use ($refl) {
            $type = $refl->getProperty($m[1])->getType();
            \assert($type instanceof \ReflectionNamedType);

            $type = $type->getName();
            if ('array' === $type) {
                return sprintf('"%s";a', $m[1]);
            }

            return sprintf('"%s";O:%d:"%s"', $m[1], strlen($type), $type);
        }, $serialized);

        return unserialize(str_replace('8:"stdClass"', strlen($targetClass).':"'.$targetClass.'"', $serialized));
    }
}
