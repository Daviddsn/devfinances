<?php


namespace Source\Core;


use Source\Support\Message;
use Source\Core\View;

/**
 * Class Controller
 * @package Source\Core
 */
class Controller
{

    protected View $view;
    protected Message $message;



    /**
     * Controller constructor.
     * @param string|null $pathToViews
     */
    public function __construct(string $pathToViews = null)
    {
        $this->view = new View($pathToViews);
        $this->message = new Message();
    }
}