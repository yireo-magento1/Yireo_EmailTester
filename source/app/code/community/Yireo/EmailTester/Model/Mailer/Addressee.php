<?php
/**
 * Yireo EmailTester for Magento
 *
 * @package     Yireo_EmailTester
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2015 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

/**
 * EmailTester Core model
 */
class Yireo_EmailTester_Model_Mailer_Addressee
{
    /**
     * @var
     */
    protected $name;

    /**
     * @var
     */
    protected $email;

    /**
     * Yireo_EmailTester_Model_Mailer_Addressee constructor
     */
    public function __construct()
    {
        $this->name = Mage::getStoreConfig('trans_email/ident_general/name');
        $this->email = Mage::getStoreConfig('trans_email/ident_general/email');
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return array
     */
    public function getAsArray()
    {
        return array('name' => $this->name, 'email' => $this->email);
    }
}