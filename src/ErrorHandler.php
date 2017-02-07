<?php

namespace Phower\TypeHint;

/**
 * Description of ErrorHandler
 *
 * @author pedro
 */
class ErrorHandler
{

    const TYPEHINT_PCRE = '/^Argument (\d)+ passed to (?:(\w+)::)?(\w+)\(\) must be an instance of (\w+), (\w+) given/';

    private static $hints = array(
        'callable' => 'is_callable',
        'boolean' => 'is_bool',
        'integer' => 'is_int',
        'float' => 'is_float',
        'string' => 'is_string',
        'resource' => 'is_resource'
    );

    private function __constrct()
    {
        // disallowing new instances
    }

    public static function initialize()
    {
        set_error_handler(array(__CLASS__, 'handle'), E_RECOVERABLE_ERROR);
        return true;
    }

    private static function getArgument($backTrace, $functionName, $argIndex, &$argValue)
    {
        foreach ($backTrace as $trace) {
            if (isset($trace['function']) && $trace['function'] == $functionName) {
                $argValue = $trace['args'][$argIndex - 1];
                return true;
            }
        }

        return false;
    }

    public static function handle($level, $message)
    {
        if (preg_match(self::TYPEHINT_PCRE, $message, $matches)) {
            list($match, $index, $className, $functionName, $hint, $type) = $matches;

            if (isset(self::$hints[$hint])) {
                $backtrace = debug_backtrace();
                $argValue = null;

                if (self::getArgument($backtrace, $functionName, $index, $argValue)) {
                    if (call_user_func(self::$hints[$hint], $argValue)) {
                        return true;
                    }
                }
            }
        }

        return true;
    }

}
