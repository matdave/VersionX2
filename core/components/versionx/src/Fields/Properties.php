<?php

namespace modmore\VersionX\Fields;

class Properties extends Field
{
    public function parse()
    {
        if (is_array($this->value)) {
            $this->value = self::splitPropertyValues($this->value, $this->fieldName);
        }
    }

    /**
     *
     * @param array $arrayField
     * @param string $name
     * @param array $fields
     * @return array
     */
    public static function splitPropertyValues(array $arrayField, string $name = '', array &$fields = []): array
    {
        $arrays = [];
        foreach ($arrayField as $field => $value) {
            if (empty($name)) {
                $fields[$field] = $value;
                continue;
            }
            if (is_numeric($field)) {
                $fields[$name][$field] = $value;
                continue;
            }
            if (!is_array($value)) {
                $fields["{$name}.{$field}"] = $value;
                continue;
            }

            $arrays[$field] = $value;
        }

        foreach ($arrays as $field => $value) {
            self::splitPropertyValues($value, "{$name}.{$field}", $fields);
        }

        return $fields;
    }

    /**
     * @param \vxDeltaField $field
     * @param mixed $data
     * @return mixed
     */
    public static function revertPropertyValue(\vxDeltaField $field, &$data)
    {
        if (!is_array($data)) {
            return $data;
        }

        $pieces = explode('.', $field->get('field'));
        $last = end($pieces);
        foreach ($pieces as $piece) {
            if (!array_key_exists($piece, $data)) {
                continue;
            }

            // The last 'piece' will be the key we're after.
            if ($piece === $last) {
                $beforeValue = $field->get('before');
                $beforeType = $field->get('before_type');

                if (in_array($beforeType, ['array', 'object'])) {
                    // Decode if it's meant to be an array/object
                    $decoded = json_decode($beforeValue, true);
                    $data[$piece] = $decoded ?: $field->get('before');
                }
                else if (in_array(strtolower($beforeType), self::ACCEPTED_VALUE_TYPES)) {
                    // Cast as the set type
                    $data[$piece] = settype($beforeValue, $beforeType);
                }
                else {
                    // If we're here treat as a string
                    $data[$piece] = $field->get('before');
                }

            }
            else {
                $data = &$data[$piece];
            }
        }

        return $data;
    }

}