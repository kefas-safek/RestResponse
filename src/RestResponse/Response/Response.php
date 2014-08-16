<?php

/**
 * @author kefas <kefas@g.pl>
 */

namespace RestResponse\Response;

class Response {

    const RESOURCE_NOT_FOUND = 404;
    const IS_NOT_VALID = 422;
    const CREATED = 201;
    const OK = 200;
    const FORBIDDEN = 403;
    const BAD_REQUEST = 400;
    const INTERNAL_SERVER_ERROR = 500;

    /**
     * @var \RestResponse\Collection\Message
     */
    protected $messages;

    /**
     * @var boolean 
     */
    protected $isValid = true;

    /**
     * @var array
     */
    protected $info;

    /**
     * @var int 
     */
    protected $code = 200;

    /**
     * set response to not found
     * @param string $message
     * @return \RestResponse\Response\Response
     */
    public function setNotFound($message = null) {
        if ($message) {
            $message = (string) $message;
        } else {
            $message = 'resource not found';
        }
        $this->isValid = false;
        $this->setCode(self::RESOURCE_NOT_FOUND);
        $this->getMessages()->clear()->addMessage($message);
        return $this;
    }

    /**
     * 
     * @param type $message
     * @return \RestResponse\Response\Response
     */
    public function setBadRequest($message = null) {
        if ($message) {
            $message = (string) $message;
        } else {
            $message = 'bad request';
        }
        $this->isValid = false;
        $this->setCode(self::BAD_REQUEST);
        $this->getMessages()->clear()->addMessage($message);
        return $this;
    }

    /**
     * @param string $message
     * @return \RestResponse\Response\Response
     */
    public function setForbidden($message = null) {
        if ($message) {
            $message = (string) $message;
        } else {
            $message = 'access denied';
        }
        $this->isValid = false;
        $this->setCode(self::FORBIDDEN);
        $this->getMessages()->clear()->addMessage($message);
        return $this;
    }

    public function setInternalServerError($message = null) {
        if ($message) {
            $message = (string) $message;
        } else {
            $message = 'internal server error';
        }
        $this->isValid = false;
        $this->setCode(self::INTERNAL_SERVER_ERROR);
        $this->getMessages()->clear()->addMessage($message);
        return $this;
    }

    /**
     * @param string $property
     * @return \RestResponse\Response\Response
     */
    public function setPropertyRequired($property) {
        $property = (string) $property;
        $this->isValid = false;
        $this->setCode(self::IS_NOT_VALID);
        //       $this->getMessages()->setValid(false);
        $this->getMessages()->addMessage('property required', $property, true);
        return $this;
    }

    /**
     * @return \RestResponse\Response\Response
     */
    public function setNotValid() {
        $this->isValid = false;
        $this->setCode(self::IS_NOT_VALID);
        return $this;
    }

    /**
     * @param string
     * @return \RestResponse\Response\Response
     */
    public function setCreated($id = null) {
        $this->setCode(\Bms\Response\Response::CREATED);
        if ($id) {
            $this->setInfo(array('id' => $id));
        }
        return $this;
    }

    public function reset() {
        $this->getMessages()->clear();
        $this->code = 200;
        $this->isValid = true;
        $this->info = null;
    }

    public function getCode() {
        return $this->code;
    }

    /**
     * @return \RestResponse\Collection\Message
     */
    public function getMessages() {
        if (!$this->messages) {
            $this->messages = new \RestResponse\Collection\Message();
        }
        return $this->messages;
    }

    public function isValid() {
        return $this->isValid;
    }

    public function getInfo() {
        return $this->info;
    }

    /**
     * @param int $status
     * @return \RestResponse\Response\Response
     */
    public function setStatus($status, $message = null, $path = null, $relative = false) {
        switch ($status) {
            case self::CREATED:
                $this->setCreated($message);
                break;
            case self::IS_NOT_VALID:
                $this->setValid(false);
                $this->getMessages()->setValid(false);
                if ($message) {
                    $this->getMessages()->addMessage($message, $path, $relative);
                }
                break;
            case self::OK:
                $this->setValid(true);
                break;
            case self::RESOURCE_NOT_FOUND:
                $this->setNotFound($message);
                break;
            case self::FORBIDDEN:
                $this->setForbidden($message);
                break;
            case self::INTERNAL_SERVER_ERROR:
                $this->setInternalServerError($message);
                break;
            default:
                break;
        }
        $this->setCode($status);
        return $this;
    }

    /**
     * @param array $info
     * @return \RestResponse\Response\Response
     */
    public function setInfo(array $info) {
        $this->info = $info;
        return $this;
    }

    /**
     * @param int $code
     * @return \RestResponse\Response\Response
     */
    public function setCode($code) {
        $this->code = $code;
        return $this;
    }

    /**
     * @param boolean $isValid
     * @return \RestResponse\Response\Response
     */
    public function setIsValid($isValid) {
        $this->isValid = $isValid == true ? true : false;
        return $this;
    }

}
