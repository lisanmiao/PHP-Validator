<?php

/**
 * A validator class to validate user input parameters.
 * Usage:
 * require_once 'Validator.class.php';
 * 1. type rule usage:
 *    $err_msg = '';
 *    $schema = array(
 *        'type' => 'integer',
 *    );
 *    $ret = Validator::validate($schema, 123, $err_msg);
 *    ($ret == true and $err_msg == '')
 * 2. empty rule usage:
 *    $err_msg = '';
 *    $schema = array(
 *        'empty' => true,
 *    );
 *    $ret = Validator::validate($schema, '', $err_msg);
 *    ($ret == true and $err_msg == '')
 * 3. range rule usage:
 *    $err_msg = '';
 *    $schema = array(
 *        'range' => array(
 *            'min' => 1,
 *            'max' => 10,
 *        ),
 *    );
 *    $ret = Validator::validate($schema, 8, $err_msg);
 *    ($ret == true and $err_msg == '')
 * 4. in rule usage:
 *    $err_msg = '';
 *    $schema = array(
 *        'in' => array(
 *            123, 123.456, true, 'abc', null
 *        ),
 *    );
 *    $ret = Validator::validate($schema, 123, $err_msg);
 *    ($ret == true and $err_msg == '')
 * 5. strlen rule usage:
 *    $err_msg = '';
 *    $schema = array(
 *        'strlen' => array(
 *            'min' => 3,
 *            'max' => 6,
 *        ),
 *    );
 *    $ret = Validator::validate($schema, '123456', $err_msg);
 *    ($ret == true and $err_msg == '')
 * 6. preg_metch rule usage:
 *    $err_msg = '';
 *    $schema = array(
 *        'preg_match' => '/\d{3}/i',
 *    );
 *    $ret = Validator::validate($schema, '123', $err_msg);
 *    ($ret == true and $err_msg == '')
 * 7. array rule usage:
 *    $err_msg = '';
 *    $schema = array(
 *        'array' => array(
 *            'type' => 'integer',
 *            'range' => array(
 *                'min' => 1,
 *                'max' => 16,
 *            ),
 *        ),
 *    );
 *    $arr = array(
 *        1, 2, 3, array(
 *            4, 5, 6,
 *        ),
 *    );
 *    $ret = Validator::validate($schema, $arr, $err_msg);
 *    ($ret == true and $err_msg == '')
 * 8. array_field rule usage:
 *    $err_msg = '';
 *    $schema = array(
 *        'array_field' => array(
 *            'field' => 'user_id',
 *            'type' => 'integer',
 *            'range' => array(
 *                'min' => 1,
 *                'max' => 16,
 *            ),
 *        ),
 *    );
 *    $arr = array(
 *        'user_id' => 1, 230, 330,
 *        array(
 *            'user_id' => 4, 530, 160,
 *        ),
 *    );
 *    $ret = Validator::validate($schema, $arr, $err_msg);
 *    ($ret == true and $err_msg == '')
 * 9. array_optional_field rule usage:
 *    $err_msg = '';
 *    $schema = array(
 *        'array_optional_field' => array(
 *            'field' => 'enable',
 *            'type' => 'boolean',
 *            'value' => false, // not support value null
 *        ),
 *    );
 *    $arr = array(
 *        'switch_name' => 'close_door',
 *        'enable' => false,
 *    );
 *    $ret = Validator::validate($schema, $arr, $err_msg);
 *    ($ret == true and $err_msg == '')
 * 10. custom_function rule usage:
 *     function udf($value, &$err_msg) {
 *         if ($value < 100) {
 *             $err_msg = 'Value must be larger than 100.';
 *             return false;
 *         }
 *         return true;
 *     };
 *     $err_msg = '';
 *     $schema = array(
 *         'custom_function' => 'udf',
 *     );
 *     $ret = Validator::validate($schema, 123, $err_msg);
 *    ($ret == true and $err_msg == '')
 *  More usages can be found in ValidatorTest.php
 *  Parameter $schema introduction:
 *      array(
 *          'type' => 'integer', // int must be interger and float must be double, others the same.
 *          'empty' => false, // true or false
 *          'range' => array(
 *              'min' => 0, // must be int
 *              'max' => 20, // must be int
 *          ),
 *          'in' => array('red', 'blue', 20, false, null), // type strict mode
 *          'strlen' => array( // utf-8 mb_strlen
 *              'min' => 0, // must be int
 *              'max' => 20, // must be int
 *          ),
 *          'preg_match' => '/\w{3}\d{2}/i', // must be valid regular expression
 *          'array' => array(
 *              'type' => 'integer',
 *              'range' => array(
 *                  'min' => 0,
 *                  'max' => 20,
 *              ),
 *          ),
 *          'array_field' => array(
 *              'field' => 'user_id', // the field must be exist in every level array
 *              'type' => 'integer',
 *              'range' => array(
 *                  'min' => 0,
 *                  'max' => 20,
 *              ),
 *          ),
 *          'array_optional_field' => array( // field is optional, if exist, type and value must
 *          // be same as rule key type & value
 *              'field' => 'enable',
 *              'type' => 'boolean',
 *              'value' => false, // not support value: null
 *          ),
 *          'custom_function' => 'func_name', // function func_name must predefined and form is:
 *          // custom_function_name(mixed $value, string &$err_msg);
 *      );
 *      type: boolean, integer, double, string, array, object, resource, null;
 *      empty: true or false, check value with php build-in function: empty();
 *      range: min & max only support integer;
 *      in: array(), check witch php build-in function: in_array() with strict mode;
 *      strlen: min & max only support integer; check string length with utf-8 mb_string;
 *      array: check array value type recrusively, support value rule which require all value
 *             is equal to key value; if type is integer, suppport range rule; if rule range
 *             and rule value exist both, then only rule value takes effect; rule value cannot
 *             be equal to null
 *      array_field: just like rule array, but has another key: field, which require array has
 *                   the key in every level, and only check the field's value;
 *      array_optional_field: just like array_field, difference is that field is optional, if
 *                            field not exist in array, do not check it;
 *      custom_function: must be defined before and parameters list must be the type:
 *      custom_function_name(mixed $value, string &$err_msg);
 *
 *      All the rules can be in $schema at the same time, if exists multiply rules, then check
 *      them all.
 * @author lisanmiao(lisanmiao@baidu.com)
 */
class Validator {

    // valid variable type list
    private static $arrValidType = array('boolean', 'integer', 'double', 'string', 'array',
                                   'object', 'resource', 'NULL');

    // valid schema rule list
    private static $arrValidSchemaRule = array('type', 'empty', 'range', 'in', 'strlen',
                                        'preg_match', 'array', 'array_field',
                                        'array_optional_field', 'custom_function');
    /**
     * user interface method
     * @param array $schema specific validate rule info
     * @param mixed $value the value to be checked
     * @param string &$err_msg error msg
     * @return boolean true indicates checking success and false indicates checking failed
     */
    public static function validate($schema, $value, &$err_msg) {
        $err_msg = ''; // reset $err_msg to be empty
        if (!is_array($schema)) {
            $err_msg = 'Validator schema must be an array';
            return false;
        }
        if (empty($schema)) {
            $err_msg = 'Validator schema cannot be empty';
            return false;
        }
        foreach ($schema as $schema_key => $schema_value) {
            switch ($schema_key) {
                case 'type':
                    $ret = self::ruleType($schema_value, $value, $err_msg);
                    if (!$ret) {
                        return false;
                    }
                    break;
                case 'empty':
                    $ret = self::ruleEmpty($schema_value, $value, $err_msg);
                    if (!$ret) {
                        return false;
                    }
                    break;
                case 'range':
                    $ret = self::ruleRange($schema_value, $value, $err_msg);
                    if (!$ret) {
                        return false;
                    }
                    break;
                case 'in':
                    $ret = self::ruleIn($schema_value, $value, $err_msg);
                    if (!$ret) {
                        return false;
                    }
                    break;
                case 'strlen':
                    $ret = self::ruleStrlen($schema_value, $value, $err_msg);
                    if (!$ret) {
                        return false;
                    }
                    break;
                case 'preg_match':
                    $ret = self::rulePregMatch($schema_value, $value, $err_msg);
                    if (!$ret) {
                        return false;
                    }
                    break;
                case 'array':
                    $ret = self::ruleArray($schema_value, $value, $err_msg);
                    if (!$ret) {
                        return false;
                    }
                    break;
                case 'array_field':
                    $ret = self::ruleArrayField($schema_value, $value, $err_msg);
                    if (!$ret) {
                        return false;
                    }
                    break;
                case 'array_optional_field':
                    $ret = self::ruleArrayOptionalField($schema_value, $value, $err_msg);
                    if (!$ret) {
                        return false;
                    }
                    break;
                case 'custom_function':
                    $ret = self::ruleCustomFunction($schema_value, $value, $err_msg);
                    if (!$ret) {
                        return false;
                    }
                    break;
                default:
                    $strValidSchemaRule = implode(',', self::$arrValidSchemaRule);
                    $err_msg = "schema rule: {$schema_key} is invalid, " .
                               "valid rule list is: {$strValidSchemaRule}";
                    return false;
            }
        }
        return true;
    }

    /**
     * to check if $value type is equal to $type
     * @param string $type one type string which must be in self::$arrValidType
     * @param mixed $value the value to be checked
     * @param string &$err_msg error msg
     * @return boolean true indicates checking success and false indicates checking failed
     */
    private static function ruleType($type, $value, &$err_msg) {
        if (!in_array($type, self::$arrValidType)) {
            $strValidType = implode(',', self::$arrValidType);
            $err_msg = "Schema type: {$type} is not in valid type list: {$strValidType}";
            return false;
        }
        // if $value is float, then $valueType is equal to 'double', and int is integer
        $valueType = gettype($value);
        if ($valueType === $type) {
            return true;
        }
        $err_msg = "Value type is {$valueType}, not the schema type {$type} you need";
        return false;
    }

    /**
     * to check if $value is empty or not, specified by the boolean $requiredEmpty
     * @param boolean $requiredEmpty required $value to be empty or not
     * @param mixed $value the value to be checked
     * @param string &$err_msg error msg
     * @return boolean true indicates checking success and false indicates checking failed
     */
    private static function ruleEmpty($requiredEmpty, $value, &$err_msg) {
        // check $requiredEmpty is boolean
        $err_msg = '';
        $schema = array(
            'type' => 'boolean',
        );
        $ret = Validator::validate($schema, $requiredEmpty, $err_msg);
        if (!$ret) {
            $err_msg = "Empty rule require value to be boolean: {$err_msg}";
            return false;
        }
        $isEmpty = empty($value);
        if ($requiredEmpty !== $isEmpty) {
            if ($requiredEmpty) {
                $err_msg = 'Empty rule require value to be empty, but it is not';
            } else {
                $err_msg = 'Empty rule require value to be not empty, but it is';
            }
            return false;
        }
        return true;
    }

    /**
     * check if $value match range requirement
     * range need $value must be integer, we need to check it
     * @param array $arrMinMax the array with range info: [min, max]
     * @param integer $value the value to be checked
     * @param string &$err_msg error msg
     * @return boolean true indicates checking success and false indicates checking failed
     */
    private static function ruleRange($arrMinMax, $value, &$err_msg) {
        // check $value is integer
        $err_msg = '';
        $schema = array(
            'type' => 'integer',
        );
        $ret = Validator::validate($schema, $value, $err_msg);
        if (!$ret) {
            $err_msg = "Schema range require the checking value to be integer: {$err_msg}";
            return false;
        }
        if (!is_array($arrMinMax) || !isset($arrMinMax['min']) || !isset($arrMinMax['min'])) {
            $err_msg = 'Schema range rule require its value be array with two key: min and max';
            return false;
        }
        $ret = Validator::validate($schema, $arrMinMax['min'], $err_msg);
        if (!$ret) {
            $err_msg = 'Schema range min value must be integer';
            return false;
        }
        $ret = Validator::validate($schema, $arrMinMax['max'], $err_msg);
        if (!$ret) {
            $err_msg = 'Schema range max value must be integer';
            return false;
        }
        if (($value < $arrMinMax['min']) || ($value > $arrMinMax['max'])) {
            $err_msg = "Value: {$value} not in [{$arrMinMax['min']}, {$arrMinMax['max']}]";
            return false;
        }
        return true;
    }

    /**
     * to check if $value in array $arrIn with type stict mode
     * @param array $arrIn the array contains allowed values
     * @param mixed $value the value to be checked
     * @param string &$err_msg error msg
     * @return boolean true indicates checking success and false indicates checking failed
     */
    private static function ruleIn($arrIn, $value, &$err_msg) {
        // check $arrIn is array
        $err_msg = '';
        $schema = array(
            'type' => 'array',
            'empty' => false,
        );
        $ret = Validator::validate($schema, $arrIn, $err_msg);
        if (!$ret) {
            $err_msg = "Schema rule in value must be array and cannot be empty: {$err_msg}";
            return false;
        }
        $ret = in_array($value, $arrIn, true);
        if (!$ret) {
            $value = json_encode($value);
            $err_msg = "Value {$value} not in schema array";
            return false;
        }
        return true;
    }

    /**
     * check if string length in [min, max]
     * $arrMinMax must be array and min, max must be integer
     * @param array $arrMinMax has two key: min and max, specific the string min and max length
     * @param string $value the value to be checked
     * @param string &$err_msg error msg
     * @return boolean true indicates checking success and false indicates checking failed
     */
    private static function ruleStrlen($arrMinMax, $value, &$err_msg) {
        // check $value is string
        $err_msg = '';
        $schema = array(
            'type' => 'string',
        );
        $ret = Validator::validate($schema, $value, $err_msg);
        if (!$ret) {
            $err_msg = "Schema strlen rule require the checking value to be string: {$err_msg}";
            return false;
        }
        if (!is_array($arrMinMax) || !isset($arrMinMax['min']) || !isset($arrMinMax['min'])) {
            $err_msg = 'Schema strlen rule require its value be array with two key: min and max';
            return false;
        }
        $schema = array(
            'type' => 'integer',
        );
        $ret = Validator::validate($schema, $arrMinMax['min'], $err_msg);
        if (!$ret) {
            $err_msg = 'Schema strlen min value must be integer';
            return false;
        }
        $schema = array(
            'type' => 'integer',
        );
        $ret = Validator::validate($schema, $arrMinMax['max'], $err_msg);
        if (!$ret) {
            $err_msg = 'Schema strlen max value must be integer';
            return false;
        }
        $strlen = mb_strlen($value, 'utf-8');
        if (($strlen < $arrMinMax['min']) || ($strlen > $arrMinMax['max'])) {
            $err_msg = "Value: {$value} length not in [{$arrMinMax['min']}, {$arrMinMax['max']}]";
            return false;
        }
        return true;
    }

    /**
     * check if string match the pattern
     * $regExp must be string and cannot be empty
     * @param string $regExp the regular expression string
     * @param string $value the value to be checked
     * @param string &$err_msg error msg
     * @return boolean true indicates checking success and false indicates checking failed
     */
    private static function rulePregMatch($regExp, $value, &$err_msg) {
        // check $value is string
        $err_msg = '';
        $schema = array(
            'type' => 'string',
            'empty' => false,
        );
        $ret = Validator::validate($schema, $regExp, $err_msg);
        if (!$ret) {
            $err_msg = 'Schema preg_match require schema value to be string and ' .
                       "not be empty: {$err_msg}";
            return false;
        }
        $schema = array(
            'type' => 'string',
        );
        $ret = Validator::validate($schema, $value, $err_msg);
        if (!$ret) {
            $err_msg = "Schema preg_match require the checking value to be string: {$err_msg}";
            return false;
        }
        $ret = @preg_match($regExp, $value); // considered invalid regular expression
        if ($ret === false) {
            $err_msg = "Schema preg_match value {$regExp} is not a valid regular expression";
            return false;
        }
        if ($ret === 0) {
            $err_msg = "Value {$value} does not match regular expression: {$regExp}";
            return false;
        }
        return true;
    }

    /**
     * check array field type, its value range or equal to specific value
     * check array value type, if check type is integer then support range rule
     * @param array $arrRule the info for checking, need has key type, support range/value rule
     * @param mixed $value the value to be checked
     * @param string &$err_msg error msg
     * @return boolean true indicates checking success and false indicates checking failed
     */
    private static function ruleArray($arrRule, $value, &$err_msg) {
        // check $arrRule is array
        $err_msg = '';
        $schema = array(
            'type' => 'array',
        );
        $ret = Validator::validate($schema, $arrRule, $err_msg);
        if (!$ret) {
            $err_msg = "Schema array require value to be array: {$err_msg}";
            return false;
        }
        if (!isset($arrRule['type'])) {
            $err_msg = "Schema array require value has key: type";
            return false;
        }
        $strType = $arrRule['type'];
        $arrRange = array(); // if type is integer, then support range rule
        $mixedFieldValue = null; // else, then support value rule
        if (isset($arrRule['range'])) {
            $arrRange = $arrRule['range'];
        }
        // if value === null, then value rule will not take effect
        if (isset($arrRule['value'])) {
            $mixedFieldValue = $arrRule['value'];
        }
        $schema = array(
            'type' => 'array',
            'empty' => false,
        );
        $ret = Validator::validate($schema, $value, $err_msg);
        if (!$ret) {
            $err_msg = 'Schema array require the checking value to be array and not empty: ' .
                       "{$err_msg}";
            return false;
        }
        // check array type
        // if type is int, and range key exists, then check range rule
        $ret = Validator::checkArrRecursively($value, $strType, $arrRange, $err_msg,
                                              $mixedFieldValue);
        if (!$ret) {
            return false;
        }
        return true;
    }

    /**
     * check array specific field type, its value range or equal to specific value
     * check array value type, if check type is integer then support range rule,
     * if value rule exists, then check the value; if value rule exists, range rule will
     * be abandoned
     * @param array $arrRule the info for checking, need has key field/type, support range/value 
     *                       rule
     * @param mixed $value the value to be checked
     * @param string &$err_msg error msg
     * @return boolean true indicates checking success and false indicates checking failed
     */
    private static function ruleArrayField($arrRule, $value, &$err_msg) {
        // check $arrRule is array
        $err_msg = '';
        $schema = array(
            'type' => 'array',
        );
        $ret = Validator::validate($schema, $arrRule, $err_msg);
        if (!$ret) {
            $err_msg = "Schema array_field require value to be array: {$err_msg}";
            return false;
        }
        if (!isset($arrRule['field'])) {
            $err_msg = "Schema array_field require value has key: field";
            return false;
        }
        $strField = $arrRule['field'];
        if (!isset($arrRule['type'])) {
            $err_msg = "Schema array_field require value has key: type";
            return false;
        }
        $strType = $arrRule['type'];
        $arrRange = array(); // if type is integer, then support range rule
        $mixedFieldValue = null; // else, then support value rule
        if (isset($arrRule['range'])) {
            $arrRange = $arrRule['range'];
        }
        // if value === null, then value rule will not take effect
        if (isset($arrRule['value'])) {
            $mixedFieldValue = $arrRule['value'];
        }
        $schema = array(
            'type' => 'array',
            'empty' => false,
        );
        $ret = Validator::validate($schema, $value, $err_msg);
        if (!$ret) {
            $err_msg = 'Schema array_field require the checking value to be array and ' .
                       "not empty: {$err_msg}";
            return false;
        }
        // check array type
        // if type is integer, and range key exists, then check range rule
        // if mixedFieldValue is not null, check value only
        $ret = Validator::checkArrRecursively($value, $strType, $arrRange, $err_msg,
                                              $mixedFieldValue, $strField, false);
        if (!$ret) {
            return false;
        }
        return true;
    }

    /**
     * check array optional specific field type, its value range or equal to specific value
     * check array value type, if check type integer then support range rule
     * if has value rule, then check the value; if value rule exist, range rule will
     * be abandoned
     * if specific field not exist, then return true
     * @param array $arrRule the info for checking, need has key field/type, support range/value
     *                       rule
     * @param mixed $value the value to be checked
     * @param string &$err_msg error msg
     * @return boolean true indicates checking success and false indicates checking failed
     */
    private static function ruleArrayOptionalField($arrRule, $value, &$err_msg) {
        // check $arrRule is array
        $err_msg = '';
        $schema = array(
            'type' => 'array',
        );
        $ret = Validator::validate($schema, $arrRule, $err_msg);
        if (!$ret) {
            $err_msg = "Schema array_optional_field require value to be array: {$err_msg}";
            return false;
        }
        if (!isset($arrRule['field'])) {
            $err_msg = "Schema array_optional_field require value has key: field";
            return false;
        }
        $strField = $arrRule['field'];
        if (!isset($arrRule['type'])) {
            $err_msg = "Schema array_optional_field require value has key: type";
            return false;
        }
        $strType = $arrRule['type'];
        $arrRange = array(); // if type is integer, then support range rule
        $mixedFieldValue = null; // else, then support value rule
        if (isset($arrRule['range'])) {
            $arrRange = $arrRule['range'];
        }
        // if value === null, then value rule will not take effect
        if (isset($arrRule['value'])) {
            $mixedFieldValue = $arrRule['value'];
        }
        $schema = array(
            'type' => 'array',
            'empty' => false,
        );
        $ret = Validator::validate($schema, $value, $err_msg);
        if (!$ret) {
            $err_msg = 'Schema array_optional_field require the checking value to be array and ' .
                       "not empty: {$err_msg}";
            return false;
        }
        // check array type
        // if type is int, and range key exists, then check range rule, if value key e
        $ret = Validator::checkArrRecursively($value, $strType, $arrRange, $err_msg,
                                              $mixedFieldValue, $strField, true);
        if (!$ret) {
            return false;
        }
        return true;
    }

    /**
     * support user define function
     * custom_function: must be defined before and parameters list must be the type:
     * custom_function_name($value, &$err_msg);
     * @param string $funcName the user predefined function
     * @param mixed $value the value to be checked
     * @param string &$err_msg error msg
     * @return boolean true indicates checking success and false indicates checking failed
     */
    private static function ruleCustomFunction($funcName, $value, &$err_msg) {
        $err_msg = '';
        $schema = array(
            'type' => 'string',
            'empty' => false,
        );
        $ret = Validator::validate($schema, $funcName, $err_msg);
        if (!$ret) {
            $err_msg = "Function name must be a string and not empty: {$err_msg}";
            return false;
        }
        $commonErrMsg = 'Custom function must be defined as: ' .
                        'custom_function_name(mixed $value, string &$err_msg)';
        $arrFuncArgsInfo = self::getFuncArgsInfo($funcName);
        if (empty($arrFuncArgsInfo) || (2 !== count($arrFuncArgsInfo))) {
            $err_msg = $commonErrMsg;
            return false;
        }
        // as php5.4 not support getType(), so do not check type
        if (true !== $arrFuncArgsInfo[1]['isPassedByReference']) {
            $err_msg = $commonErrMsg;
            return false;
        }
        $funcRet = $funcName($value, $err_msg);
        $schema = array(
            'type' => 'boolean',
        );
        $ret = Validator::validate($schema, $funcRet, $err_msg);
        if (!$ret) {
            $err_msg = "Custom function return value type must be boolean: {$err_msg}";
            return false;
        }
        return $funcRet;
    }

    /**
     * check array recursively, including value type, range, or equals to the specific value
     * if value rule exist, then abandon the range rule
     * @param array $arr the array whoes value will be checked
     * @param string $type type string which specific array items type 
     * @param array $range specific array values range, if type is not int, then will be abandoned
     * @param string &$err_msg error mesg
     * @param mixed $fieldValue if $fieldValue is null, then value rule will not take effect
     * @param string $specificField if string not null, then only check the field
     * @param boolean $specificFieldOptional when $specificField not empty, whether array must
     *                has the key or not
     * @return boolean true indicates checking success and false indicates checking failed
     */
    private static function checkArrRecursively($arr, $type, $range, &$err_msg, $fieldValue = null,
                            $specificField = '', $specificFieldOptional = false) {
        $schema = array(
            'type' => $type,
        );
        // if type is not integer, abandon the range rule
        if ($type !== 'integer') {
            $range = array();
        }
        // if value rule exist, abandon the range rule
        if (null !== $fieldValue) {
            $range = array();
        }
        $schemaRange = array();
        if (!empty($range)) {
            $schemaRange = array(
                'range' => $range,
            );
        }
        // if $specificField empty or $specificFieldOptional equals to true, then specific
        // key can do not exist, otherwise must be exist: in foreach loop, must exist at
        // least once a time, if not, after foreach will return false
        if (($specificField === '') || $specificFieldOptional) {
            $specificFieldExist = true;
        } else {
            $specificFieldExist = false;
        }
        foreach ($arr as $key => $val) {
            if (is_array($val)) {
                $ret = Validator::checkArrRecursively($val, $type, $range, $err_msg, $fieldValue,
                                                      $specificField, $specificFieldOptional);
                if (!$ret) {
                    return false;
                }
                continue;
            }
            if (!empty($specificField) && ($key !== $specificField)) {
                continue; // if specific field is not empty, only check the specific key
            } else {
                $specificFieldExist = true;
            }
            if ($fieldValue !== null) { // specific all fileds value
                if ($val !== $fieldValue) {
                    $val = json_encode($val);
                    $fieldValue = json_encode($fieldValue);
                    $err_msg = "Schema rule value check failed, checking value: {$val} not equal " .
                               "to rule value: {$fieldValue}";
                    return false;
                }
            }
            $ret = Validator::validate($schema, $val, $err_msg);
            if (!$ret) {
                $err_msg = "The checking array has one value not the type: {$type}: {$err_msg}";
                return false;
            }
            if (!empty($schemaRange)) {
                $ret = Validator::validate($schemaRange, $val, $err_msg);
                if (!$ret) {
                    $err_msg = "Schema rule range check failed: {$err_msg}";
                    return false;
                }
            }
        }
        if (!$specificFieldExist) {
            $err_msg = "The checking array has not specific key: {$specificField}";
            return false;
        }
        return true;
    }

    /**
     * get function args info
     * php5.4 not support getType(), so do not return type info and not check it
     * @param string $funcName function name string
     * @return array an array which contains function parameters info
     */
    private function getFuncArgsInfo($funcName) {
        $func = new ReflectionFunction($funcName);
        $result = array();
        foreach ($func->getParameters() as $param) {
            $result[] = array(
                        'name' => $param->getName(),
                        'isPassedByReference' => $param->isPassedByReference(),
            );
        }
        return $result;
    }
}
