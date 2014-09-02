<?php

/**
 * @author kefas <kefas@g.pl>
 */

namespace RestResponse\Collection;

class Message {

    /**
     * @var array 
     */
    protected $cursor = array(); 

    /**
     * @var array 
     */
    protected $savedCursors = array();

    /**
     * @var array 
     */
    protected $messages = array();

    /**
     * @return array
     */
    public function getCursor($alias = null) {
        if ($alias) {
            if ($this->isValidMessage($alias)) {
                return isset($this->savedCursors[$alias]) ? $this->savedCursors[$alias] : array();
            }
        }
        return $this->cursor;
    }

    /**
     * @return array
     */
    public function getMessages() {
        return $this->messages;
    }

    /**
     * alias to getMessages
     * @return array
     */
    public function toArray() {
        return $this->messages;
    }

    public function saveCursor($alias, $path = null) {
        if (!$this->isValidMessage($alias)) {
            throw new \Exception('wrong alias');
        }
        if (!$path) {
            $this->savedCursors[$alias] = $this->cursor;
            return $this;
        }
        if (!$this->isPathValid($path)) {
            throw new \Exception('wrong path');
        }
        $this->savedCursors[$alias] = $path;
        return $this;
    }

    /**
     * @param array|string|int $cursor
     * @return \RestResponse\Collection\Message
     */
    public function setCursor($cursor) {
        if (is_string($cursor) || is_int($cursor)) {
            if (!$this->isValidMessage($cursor)) {
                throw new \Exception('wrong alias format, string or int expected');
            }
            if (!array_key_exists($cursor, $this->savedCursors)) {
                throw new \Exception('cursor of given alias not found');
            }
            $this->setCursor($this->savedCursors[$cursor]);
            return $this;
        }
        if (is_array($cursor)) {
            if ($this->isPathValid($cursor)) {
                $this->cursor = $cursor;
                return $this;
            }
        }
        throw new \Exception('cursor is invalid, wrong format');
    }

    /**
     * delete all messages
     * @return \RestResponse\Collection\Message
     */
    public function clear() {
        $this->messages = array();
        return $this;
    }

    /**
     * @param string $message
     * @param array|null $path
     * @param boolean $reliative
     * @return \RestResponse\Collection\Message
     * @throws \Exception
     */
    public function addMessage($message, $path = null, $relative = false) {
        if ($path === null) {
            $path = $this->cursor;
        } else {
            if (is_string($path) || is_int($path)) {
                $path = $this->computePath($path);
            }
            if ($relative == true) {
                $path = array_merge($this->cursor, $path);
            }
        }

        if (!$this->isPathValid($path)) {
            throw new \Exception('path is invalid, wrong format');
        }
        $node = &$this->getOrCreateNode($path);
        if (is_array($message)) {
            foreach ($message as $key => $itemMessage) {
                if (!$this->isValidMessage($itemMessage)) {
                    throw new \Exception('message is not valid!');
                }
                if (is_string($key)) {
                    $node[$key] = $itemMessage;
                } else {
                    $node[] = $itemMessage;
                }
            }
        } else {
            if (!$this->isValidMessage($message)) {
                throw new \Exception('message is not valid!');
            }
            $node[] = $message;
        }

        return $this;
    }

    /**
     * @param boolean $valid
     * @param array|null $path
     * @param boolean $relative
     * @return \RestResponse\Collection\Message
     * @throws \Exception
     */
    public function setValid($valid, $path = null, $relative = false) {
        $valid = $valid === true || $valid === 'true' ? true : false;
        if ($path === null) {
            $path = $this->cursor;
        } else {
            if (is_string($path) || is_int($path)) {
                $path = $this->computePath($path);
            }
            if ($relative == true) {
                $path = array_merge($this->cursor, $path);
            }
        }

        if (!$this->isPathValid($path)) {
            throw new \Exception('path is invalid, wrong format');
        }
        $node = &$this->getOrCreateNode($path);

        $node['isValid'] = $valid;

        return $this;
    }

    /**
     * @param array|string $path
     * @param boolean $relative
     * @return boolean|null
     * @throws \Exception
     */
    public function isValid($path = null, $relative = false) {
        if ($path === null) {
            $path = $this->cursor;
        } else {
            if (is_string($path) || is_int($path)) {
                $path = $this->computePath($path);
            }
            if ($relative == true) {
                $path = array_merge($this->cursor, $path);
            }
        }

        if (!$this->isPathValid($path)) {
            throw new \Exception('path is invalid, wrong format');
        }
        $path[] = 'isValid';
        return $this->getNode($path) == true ? true : false;
    }

    /**
     * @param array $messages
     * @return \RestResponse\Collection\Message
     */
    public function setMessages(array $messages) {
        $this->messages = $messages;
        return $this;
    }

    /**
     * @param array $path
     * @return mixed
     */
    protected function getNode(array $path) {
        $node = $this->messages;
        foreach ($path as $pathItem) {
            $node = isset($node[$pathItem]) ? $node[$pathItem] : null;
            if ($node === null) {
                return null;
            }
        }
        return $node;
    }

    /**
     * @return \RestResponse\Collection\Message
     */
    public function unsetLastCursorValue() {
        array_pop($this->cursor);
        return $this;
    }

    /**
     * @param type $value
     * @return \RestResponse\Collection\Message
     * @throws \Exception
     */
    public function setNextCursorValue($value) {
        $value = $this->computePath($value);
        if (!$this->isValidPathValues($value)) {
            throw new \Exception('value is invalid, wrong format');
        }
        foreach ($value as $valueItem) { 
            $this->cursor[] = $valueItem;
        }
        return $this;
    }

    /**
     * @param string $path
     * @return \RestResponse\Collection\Message
     */
    public function setPath($path) {
        $this->setCursor($this->computePath($path));
        return $this;
    }

    /**
     * @param string $alias
     * @return string
     */
    public function getPath($alias) {
        return implode('/', $this->getCursor($alias));
    }

    /**
     * 
     * @param array $messages
     * @param type $path
     * @param type $relative
     * @throws \Exception
     * @return \RestResponse\Collection\Message
     */
    public function addMessages(array $messages, $path = null, $relative = false) {
        if ($path === null) {
            $path = $this->cursor;
        } else {
            if (is_string($path) || is_int($path)) {
                $path = $this->computePath($path);
            }
            if ($relative == true) {
                $path = array_merge($this->cursor, $path);
            }
        }

        if (!$this->isPathValid($path)) {
            throw new \Exception('path is invalid, wrong format');
        }
        foreach ($messages as $msgKey => $msgValue) {
            $path[] = $msgKey;
            if (is_array($msgValue)) {
                $this->addMessages($msgValue, $path, false);
            } elseif ($this->isValidMessage($msgValue)) {
                $this->addMessage($msgValue, $path, false);
            }
        }
        return $this;
    }

    /**
     * @param array $path
     * @return type
     */
    protected function &getOrCreateNode(array $path) {
        $node = &$this->messages;
        foreach ($path as $pathItem) {
            if (!isset($node[$pathItem])) {
                $node[$pathItem] = array();
            }
            $node = &$node[$pathItem];
        }
        return $node;
    }

    /**
     * @param array $path
     * @return boolean
     */
    protected function isPathValid($path) {
        if (!is_array($path) || !$this->isValidPathValues($path)) {
            return false;
        }
        return true;
    }

    /**
     * @param mixed $path
     * @return boolean
     */
    protected function isValidPathValues($path) {
        foreach ($path as $value) {
            if (!$this->isValidPathValue($value)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param mixed $value
     * @return boolean
     */
    protected function isValidPathValue($value) {
        if ((!is_string($value) && !is_int($value)) || $value === 'isValid') {
            return false;
        }
        return true;
    }

    /**
     * @param mixed $key
     * @param mixed $node
     * @return mixed
     */
    protected function getItem($key, &$node) {
        return isset($node[$key]) ? $node[$key] : null;
    }

    /**
     * 
     * @param mixed $key
     * @param mixed $node
     * @return mixed
     */
    protected function getOrCreateItem($key, &$node) {
        if (!isset($node[$key])) {
            $node[$key] = null;
        }
        return $node[$key];
    }

    /**
     * @param mixed $message
     * @return boolean
     */
    protected function isValidMessage($message) {
        if (!is_string($message) && is_int($message)) {
            return false;
        }
        return true;
    }

    /**
     * @param string $path
     * @return array
     */
    protected function computePath($path) {
        $path = trim($path);
        $path = rtrim($path, '/');
        if (!is_array($path) && !is_string($path)) {
            throw new \Exception('wrong path format');
        }

        $arrayPath = array();
        if (is_string($path)) {
            $arrayPath = explode('/', $path);
        }
        if (is_array($path)) {
            $arrayPath = $path;
        }
        return $arrayPath;
    }

}
