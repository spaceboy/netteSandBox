<?php
namespace App\Model;

use Nette;
use Nette\Database\SqlPreprocessor;

/**
 * Base model for all (or most) application models.
 */
class BaseManager extends Nette\Object {

    /** @var Nette\Database\Context */
    protected $database;

    /** @var Nette\DI\Container */
    protected $context;

    protected $preprocessor;

    protected $table;

    protected $tableName;

    public function __construct (Nette\DI\Container $context, Nette\Database\Context $database) {
        $this->context = $context;
        $this->database = $database;
        if ($this->tableName) {
            $this->table = $database->table($this->tableName);
        }
    }

    protected function fetchAsArray ($resource) {
        $out = array();
        while ($row = $resource->fetch()) {
            $out[] = $row;
        }
        return $out;
    }

    protected function getPreprocessor () {
        if (!$this->preprocessor) {
            $this->preprocessor = new Nette\Database\SqlPreprocessor($this->database->connection);
        }
        return $this->preprocessor;
    }

    protected function nonEmpty () {
        $args = func_get_args();
        if (!sizeof($args)) {
            return '';
        }
        foreach ($args as $arg) {
            if (!$arg) {
                return '';
            }
            if (is_array($arg) && !sizeof($arg)) {
                return '';
            }
        }
        $ret = $this->getPreprocessor()->process($args);
        if (sizeof($ret[1])) {
            throw new Exception('nonEmpty: Unresolved query');
        }
        return $ret[0];
    }

}