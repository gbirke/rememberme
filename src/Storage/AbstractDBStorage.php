<?php

/**
 * @license MIT
 */

namespace Birke\Rememberme\Storage;

/**
 * This abstract class contains properties with getters and setters for all
 * database storage classes
 *
 * @author Gabriel Birke
 */
abstract class AbstractDBStorage extends AbstractStorage
{
    /**
     *
     * @var string
     */
    protected $tableName = "";

    /**
     *
     * @var string
     */
    protected $credentialColumn = "";

    /**
     *
     * @var string
     */
    protected $tokenColumn = "";

    /**
     *
     * @var string
     */
    protected $persistentTokenColumn = "";

    /**
     *
     * @var string
     */
    protected $expiresColumn = "";

    /**
     * @param array $options
     */
    public function __construct($options)
    {
        foreach ($options as $prop => $value) {
            $setter = "set".ucfirst($prop);
            if (method_exists($this, $setter)) {
                $this->$setter($value);
            }
        }
    }

    /**
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * @param string $tableName
     *
     * @return $this
     */
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;

        return $this;
    }

    /**
     * @return string
     */
    public function getCredentialColumn()
    {
        return $this->credentialColumn;
    }

    /**
     * @param string $credentialColumn
     *
     * @return $this
     */
    public function setCredentialColumn($credentialColumn)
    {
        $this->credentialColumn = $credentialColumn;

        return $this;
    }

    /**
     * @return string
     */
    public function getTokenColumn()
    {
        return $this->tokenColumn;
    }

    /**
     * @param string $tokenColumn
     *
     * @return $this
     */
    public function setTokenColumn($tokenColumn)
    {
        $this->tokenColumn = $tokenColumn;

        return $this;
    }

    /**
     * @return string
     */
    public function getPersistentTokenColumn()
    {
        return $this->persistentTokenColumn;
    }

    /**
     * @param string $persistentTokenColumn
     *
     * @return $this
     */
    public function setPersistentTokenColumn($persistentTokenColumn)
    {
        $this->persistentTokenColumn = $persistentTokenColumn;

        return $this;
    }

    /**
     * @return string
     */
    public function getExpiresColumn()
    {
        return $this->expiresColumn;
    }

    /**
     * @param string $expiresColumn
     *
     * @return $this
     */
    public function setExpiresColumn($expiresColumn)
    {
        $this->expiresColumn = $expiresColumn;

        return $this;
    }
}
