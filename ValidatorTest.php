<?php

/**
 * class Validator unittest
 * @author lisanmiao(lisanmiao@baidu.com)
 */
require_once 'Validator.class.php';

class ValidatorTest extends PHPUnit_Framework_TestCase {

    /**
     * test ruleType
     * @param null
     * @return null
     */
    public function testRuleType() {
        $err_msg = '';
        $schema = array(
            'type' => 'boolean',
        );
        $ret = Validator::validate($schema, true, $err_msg);
        $this->assertEquals(true, $ret);
        $this->assertEquals('', $err_msg);

        $schema = array(
            'type' => 'integer',
        );
        $ret = Validator::validate($schema, 123, $err_msg);
        $this->assertEquals(true, $ret);
        $this->assertEquals('', $err_msg);

        $schema = array(
            'type' => 'double',
        );
        $ret = Validator::validate($schema, 123.456, $err_msg);
        $this->assertEquals(true, $ret);
        $this->assertEquals('', $err_msg);

        $schema = array(
            'type' => 'string',
        );
        $ret = Validator::validate($schema, 'abc', $err_msg);
        $this->assertEquals(true, $ret);
        $this->assertEquals('', $err_msg);

        $schema = array(
            'type' => 'array',
        );
        $value = array('abc');
        $ret = Validator::validate($schema, $value, $err_msg);
        $this->assertEquals(true, $ret);
        $this->assertEquals('', $err_msg);

        $schema = array(
            'type' => 'object',
        );
        $value = new stdClass();
        $ret = Validator::validate($schema, $value, $err_msg);
        $this->assertEquals(true, $ret);
        $this->assertEquals('', $err_msg);

        $schema = array(
            'type' => 'resource',
        );
        $value = fopen("http://www.baidu.com/", "r");
        $ret = Validator::validate($schema, $value, $err_msg);
        $this->assertEquals(true, $ret);
        $this->assertEquals('', $err_msg);

        $schema = array(
            'type' => 'NULL',
        );
        $value = null;
        $ret = Validator::validate($schema, null, $err_msg);
        $this->assertEquals(true, $ret);
        $this->assertEquals('', $err_msg);
    }

    /**
     * test ruleEmpty
     * @param null
     * @return null
     */
    public function testRuleEmpty() {
        $err_msg = '';
        $schema = array(
            'empty' => true,
        );
        $ret = Validator::validate($schema, '', $err_msg);
        $this->assertEquals(true, $ret);
        $this->assertEquals('', $err_msg);

        $schema = array(
            'empty' => false,
        );
        $ret = Validator::validate($schema, 'abc', $err_msg);
        $this->assertEquals(true, $ret);
        $this->assertEquals('', $err_msg);

        $schema = array(
            'empty' => true,
        );
        $ret = Validator::validate($schema, 'abc', $err_msg);
        $this->assertEquals(false, $ret);
        $this->assertEquals('Empty rule require value to be empty, but it is not', $err_msg);

        $schema = array(
            'empty' => false,
        );
        $ret = Validator::validate($schema, 0, $err_msg);
        $this->assertEquals(false, $ret);
        $this->assertEquals('Empty rule require value to be not empty, but it is', $err_msg);
    }

    /**
     * test ruleRange
     * @param null
     * @return null
     */
    public function testRuleRange() {
        $err_msg = '';
        $schema = array(
            'range' => array(
                'min' => 1,
                'max' => 10,
            ),
        );
        $ret = Validator::validate($schema, 8, $err_msg);
        $this->assertEquals(true, $ret);
        $this->assertEquals('', $err_msg);

        $err_msg = '';
        $schema = array(
            'range' => array(
                'min' => 1,
                'max' => 10,
            ),
        );
        $ret = Validator::validate($schema, 20, $err_msg);
        $this->assertEquals(false, $ret);
        $this->assertEquals('Value: 20 not in [1, 10]', $err_msg);

        $err_msg = '';
        $schema = array(
            'range' => array(
                'min' => 1,
                'max' => 10,
            ),
        );
        $ret = Validator::validate($schema, 20.12, $err_msg);
        $this->assertEquals(false, $ret);
        $this->assertEquals('Schema range require the checking value to be integer: Value type ' .
                            'is double, not the schema type integer you need', $err_msg);

        $err_msg = '';
        $schema = array(
            'range' => array(
                'min' => 1.23,
                'max' => 10,
            ),
        );
        $ret = Validator::validate($schema, 20, $err_msg);
        $this->assertEquals(false, $ret);
        $this->assertEquals('Schema range min value must be integer', $err_msg);
    }

    /**
     * test ruleIn
     * @param null
     * @return null
     */
    public function testRuleIn() {
        $err_msg = '';
        $schema = array(
            'in' => array(
                123, 123.456, true, 'abc', null
            ),
        );
        $ret = Validator::validate($schema, 123, $err_msg);
        $this->assertEquals(true, $ret);
        $this->assertEquals('', $err_msg);

        $schema = array(
            'in' => array(
                123, 123.456, true, 'abc', null
            ),
        );
        $ret = Validator::validate($schema, '123', $err_msg);
        $this->assertEquals(false, $ret);
        $this->assertEquals('Value "123" not in schema array', $err_msg);

        $ret = Validator::validate($schema, null, $err_msg);
        $this->assertEquals(true, $ret);
        $this->assertEquals('', $err_msg);

        $ret = Validator::validate($schema, 'null', $err_msg);
        $this->assertEquals(false, $ret);
        $this->assertEquals('Value "null" not in schema array', $err_msg);
    }

    /**
     * test ruleStrlen
     * @param null
     * @return null
     */
    public function testRuleStrlen() {
        $err_msg = '';
        $schema = array(
            'strlen' => array(
                'min' => 3,
                'max' => 6,
            ),
        );
        $ret = Validator::validate($schema, '123456', $err_msg);
        $this->assertEquals(true, $ret);
        $this->assertEquals('', $err_msg);

        $ret = Validator::validate($schema, '123', $err_msg);
        $this->assertEquals(true, $ret);
        $this->assertEquals('', $err_msg);

        $ret = Validator::validate($schema, '12', $err_msg);
        $this->assertEquals(false, $ret);
        $this->assertEquals('Value: 12 length not in [3, 6]', $err_msg);

        $ret = Validator::validate($schema, '1234567', $err_msg);
        $this->assertEquals(false, $ret);
        $this->assertEquals('Value: 1234567 length not in [3, 6]', $err_msg);

        $ret = Validator::validate($schema, '中文', $err_msg);
        $this->assertEquals(false, $ret);
        $this->assertEquals('Value: 中文 length not in [3, 6]', $err_msg);
    }

    /**
     * test rulePregMatch
     * @param null
     * @return null
     */
    public function testRulePregMatch() {
        $err_msg = '';
        $schema = array(
            'preg_match' => '/\d{3}/i',
        );
        $ret = Validator::validate($schema, '123', $err_msg);
        $this->assertEquals(true, $ret);
        $this->assertEquals('', $err_msg);

        $ret = Validator::validate($schema, '12a', $err_msg);
        $this->assertEquals(false, $ret);
        $this->assertEquals('Value 12a does not match regular expression: /\d{3}/i', $err_msg);

        $ret = Validator::validate($schema, 123, $err_msg);
        $this->assertEquals(false, $ret);
        $this->assertEquals('Schema preg_match require the checking value to be string: Value ' .
                            'type is integer, not the schema type string you need', $err_msg);

        $schema = array(
            'preg_match' => false,
        );
        $ret = Validator::validate($schema, '123', $err_msg);
        $this->assertEquals(false, $ret);
        $this->assertEquals('Schema preg_match require schema value to be string and not be ' .
                            'empty: Value type is boolean, not the schema type string you need',
                            $err_msg);
    }

    /**
     * test ruleArray
     * @param null
     * @return null
     */
    public function testRuleArray() {
        $err_msg = '';
        $schema = array(
            'array' => array(
                'type' => 'integer',
                'range' => array(
                    'min' => 1,
                    'max' => 16,
                ),
            ),
        );
        $arr = array(
            1, 2, 3, array(
                4, 5, 6
            ),
        );
        $ret = Validator::validate($schema, $arr, $err_msg);
        $this->assertEquals(true, $ret);
        $this->assertEquals('', $err_msg);

        $arr = array(
            1, 2, 3, array(
                4, 5, 26
            ),
        );
        $ret = Validator::validate($schema, $arr, $err_msg);
        $this->assertEquals(false, $ret);
        $this->assertEquals('Schema rule range check failed: Value: 26 not in [1, 16]', $err_msg);

        $arr = array(
            1, 2, 3, array(
                4, 5, '8'
            ),
        );
        $ret = Validator::validate($schema, $arr, $err_msg);
        $this->assertEquals(false, $ret);
        $this->assertEquals('The checking array has one value not the type: integer: Value type ' .
                            'is string, not the schema type integer you need', $err_msg);

        $schema = array(
            'array' => array(
                'type' => 'boolean',
                'value' => false,
            ),
        );
        $arr = array(
            false, false, false, array(
                false, false, false
            ),
        );
        $ret = Validator::validate($schema, $arr, $err_msg);
        $this->assertEquals(true, $ret);
        $this->assertEquals('', $err_msg);

        $arr = array(
            false, false, false, array(
                false, false, true
            ),
        );
        $ret = Validator::validate($schema, $arr, $err_msg);
        $this->assertEquals(false, $ret);
        $this->assertEquals('Schema rule value check failed, checking value: true not equal ' .
                            'to rule value: false', $err_msg);
    }

    /**
     * test ruleArrayField
     * @param null
     * @return null
     */
    public function testRuleArrayField() {
        $err_msg = '';
        $schema = array(
            'array_field' => array(
                'field' => 'user_id',
                'type' => 'integer',
                'range' => array(
                    'min' => 1,
                    'max' => 16,
                ),
            ),
        );
        $arr = array(
            'user_id' => 1, 230, 330,
            array(
                'user_id' => 4, 530, 160
            ),
        );
        $ret = Validator::validate($schema, $arr, $err_msg);
        $this->assertEquals(true, $ret);
        $this->assertEquals('', $err_msg);

        $arr = array(
            'user_id' => 1, 2, 3,
            array(
                'user_id' => 17, 5, 16
            ),
        );
        $ret = Validator::validate($schema, $arr, $err_msg);
        $this->assertEquals(false, $ret);
        $this->assertEquals('Schema rule range check failed: Value: 17 not in [1, 16]', $err_msg);

        $arr = array(
            'user_id' => 1, 2, 3,
            array(
                'user_id' => '4', 5, 16
            ),
        );
        $ret = Validator::validate($schema, $arr, $err_msg);
        $this->assertEquals(false, $ret);
        $this->assertEquals('The checking array has one value not the type: integer: Value type ' .
                            'is string, not the schema type integer you need', $err_msg);

        $schema = array(
            'array_field' => array(
                'field' => 'user_id',
                'type' => 'integer',
                'value' => 66,
            ),
        );
        $arr = array(
            'user_id' => 66, 2, 3,
            array(
                'user_id' => 66, 5, 16
            ),
        );
        $ret = Validator::validate($schema, $arr, $err_msg);
        $this->assertEquals(true, $ret);
        $this->assertEquals('', $err_msg);

        $arr = array(
            'user_id' => 66, 2, 3,
            array(
                'user_id' => 67, 5, 16
            ),
        );
        $ret = Validator::validate($schema, $arr, $err_msg);
        $this->assertEquals(false, $ret);
        $this->assertEquals('Schema rule value check failed, checking value: 67 not equal to ' .
                            'rule value: 66', $err_msg);
    }

    /**
     * test ruleArrayOptionalField
     * @param null
     * @return null
     */
    public function testRuleArrayOptionalField() {
        $err_msg = '';
        $schema = array(
            'array_optional_field' => array(
                'field' => 'enable',
                'type' => 'boolean',
                'value' => false,
            ),
        );
        $arr = array(
            'switch_name' => 'close_door',
            'has_keys' => 'false',
        );
        $ret = Validator::validate($schema, $arr, $err_msg);
        $this->assertEquals(true, $ret);
        $this->assertEquals('', $err_msg);

        $arr = array(
            'switch_name' => 'close_door',
            'enable' => false,
        );
        $ret = Validator::validate($schema, $arr, $err_msg);
        $this->assertEquals(true, $ret);
        $this->assertEquals('', $err_msg);

        $arr = array(
            'switch_name' => 'close_door',
            'enable' => 'false'
        );
        $ret = Validator::validate($schema, $arr, $err_msg);
        $this->assertEquals(false, $ret);
        $this->assertEquals('Schema rule value check failed, checking value: "false" not ' .
                            'equal to rule value: false', $err_msg);
        $arr = array(
            'switch_name' => 'close_door',
            'sub_info' => array(
                'enable' => 'false',
            ),
        );
        $ret = Validator::validate($schema, $arr, $err_msg);
        $this->assertEquals(false, $ret);
        $this->assertEquals('Schema rule value check failed, checking value: "false" not ' . 
                            'equal to rule value: false', $err_msg);
    }

    /**
     * test ruleArrayOptionalField
     * @param null
     * @return null
     */
    public function testRuleCustomFunction() {
        /**
         * user define function
         * @param mixed $value
         * @param string &$err_msg
         * @return boolean
         */
        function udf1($value, &$err_msg) {
            if ($value < 100) {
                $err_msg = 'Value must be larger than 100.';
                return false;
            }
            return true;
        }

        /**
         * user define function
         * @param mixed $value
         * @param string $err_msg
         * @return boolean
         */
        function udf2($value, $err_msg) {
            if ($value < 100) {
                $err_msg = 'Value must be larger than 100.';
                return false;
            }
            return true;
        }

        /**
         * user define function
         * @param mixed $value
         * @return boolean
         */
        function udf3($value) {
            if ($value < 100) {
                return false;
            }
            return true;
        }
        $err_msg = '';
        $schema = array(
            'custom_function' => 'udf1',
        );
        $ret = Validator::validate($schema, 123, $err_msg);
        $this->assertEquals(true, $ret);
        $this->assertEquals('', $err_msg);

        $schema = array(
            'custom_function' => 'udf2',
        );
        $ret = Validator::validate($schema, 123, $err_msg);
        $this->assertEquals(false, $ret);
        $this->assertEquals('Custom function must be defined as: ' .
                            'custom_function_name(mixed $value, string &$err_msg)', $err_msg);

        $schema = array(
            'custom_function' => 'udf3',
        );
        $ret = Validator::validate($schema, 123, $err_msg);
        $this->assertEquals(false, $ret);
        $this->assertEquals('Custom function must be defined as: ' .
                            'custom_function_name(mixed $value, string &$err_msg)', $err_msg);
    }
}
