<?php

namespace CrmSell\Common\Application\Service\Enum;

class ResponseCodeErrors
{
    const STATUS_OK = 'ok';
    const VALIDATE_ERROR = 'FieldValidateError';
    const SERVER_ERROR = 'ServerError';
    const FORBIDDEN_ERROR = 'ForbiddenError';
    const BUSINESS_LOGIC_ERROR = 'BusinessLogicError';
    const DUPLICATION_ERROR = 'DuplicationError';
}
