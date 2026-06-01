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
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. Add any helpers to this array
     * that you want to load on every controller.
     */
    protected $helpers = ['url', 'form', 'html', 'text'];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;

    /**
     * @return void
     */
    public function initController(
        RequestInterface $request,
        ResponseInterface $response,
        LoggerInterface $logger
    ) {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
        // E.g.: $this->session = \Config\Services::session();
    }

    /**
     * Get the currently logged-in user's data from session.
     */
    protected function getCurrentUser(): ?array
    {
        if (! session()->has('user_id')) {
            return null;
        }

        return [
            'id'    => session()->get('user_id'),
            'name'  => session()->get('user_name'),
            'email' => session()->get('user_email'),
            'role'  => session()->get('user_role'),
        ];
    }

    /**
     * Check if the current user has the given role.
     */
    protected function hasRole(string ...$roles): bool
    {
        $userRole = session()->get('user_role');
        return in_array($userRole, $roles, true);
    }

    /**
     * Return a JSON response.
     */
    protected function jsonResponse(array $data, int $statusCode = 200): ResponseInterface
    {
        return $this->response
            ->setStatusCode($statusCode)
            ->setContentType('application/json')
            ->setBody(json_encode($data));
    }
}
