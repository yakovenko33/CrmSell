<?php

namespace CrmSell\Common\Helpers\Traits;

trait PropertyTrait
{
    /**
     * @param array $request
     * @param array $exceptionsFields
     * @return void
     */
    protected function mapField(array $request, array $exceptionsFields = []): void
    {
        foreach ($request as $fieldName => $value) {
            if (property_exists($this, $fieldName) && !in_array($fieldName, $exceptionsFields)) {
                if (gettype($this->$fieldName) === "string") {
                    $this->$fieldName = trim($value);
                } elseif (gettype($this->$fieldName) === "integer") {
                    $this->$fieldName = (int)trim($value);
                }  elseif (gettype($this->$fieldName) === "double") {
                    $this->$fieldName = (float)trim($value);
                } elseif (gettype($this->$fieldName) === "boolean") {
                    $this->$fieldName = array_key_exists($fieldName, $request) && (bool)$value;
                }else {
                    $this->$fieldName = $value;
                }
            }
        }
    }

    /**
     * @param array $translated
     * @param string|null $value
     * @return string|null
     */
    protected function getTranslate(array $translated, ?string $value): ?string
    {
        return array_key_exists($value, $translated)
            ? $translated[$value]
            : $value;
    }
}
