<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{

    protected $request;
    protected $helpers = ['url', 'form', 'html', 'inventory'];
    protected $data = [];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Global data yang bisa diakses di semua view
        $this->data = [
            'title' => 'SIMATIK',
            'page_title' => '',
            'page_subtitle' => '',
        ];
        
    }
    
    //set page data
    protected function setPageData($title, $subtitle = '')
    {
        $this->data['title'] = $title . ' | SIMATIK';
        $this->data['page_title'] = $title;
        $this->data['page_subtitle'] = $subtitle;
    }

    //set Flash Message
    protected function setFlash($type, $message)
    {
        session()->setFlashdata($type, $message);
    }

    //Check if request is AJAX
    protected function isAjax()
    {
        return $this->request->isAJAX();
    }

    //Return JSON response
    protected function jsonResponse($data, $code = 200)
    {
        return $this->response->setStatusCode($code)->setJSON($data);
    }

    protected function render($view, $data = [])
    {
        $data = array_merge($this->data, $data);
        return view($view, $data);
    }
}
