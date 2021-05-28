<?php


namespace Source\Support;
use Source\Core\Session;


class Message
{
    private string $text;
    private string $type;

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }
    public function __toString() : string
    {
        return $this->render();
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    public function success(string $message):Message
    {
        $this->type = CONF_MESSAGE_SUCCESS;
        $this->text = $this->filter($message);
        return $this;
    }
    public function info(string $message):Message
    {
        $this->type = CONF_MESSAGE_INFO;
        $this->text = $this->filter($message);
        return $this;
    }
    public function warning(string $message):Message
    {
        $this->type = CONF_MESSAGE_WARNING;
        $this->text = $this->filter($message);
        return $this;
    }
    public function error(string $message):Message
    {
        $this->type = CONF_MESSAGE_ERROR;
        $this->text = $this->filter($message);
        return $this;
    }


    public function render() :string
    {
        return "<div class='".CONF_MESSAGE_CLASS ." {$this->getType()}'>{$this->getText()}</div>";
    }

    public  function json():string
    {
        return json_encode(["error" => $this->getText()]);
    }

    public function flash()
    {
        (new Session())->set("flash",$this);
    }

    public function filter(string $message)
    {
        return filter_var($message, FILTER_SANITIZE_SPECIAL_CHARS);
    }
}