<?php

/**
 * StatusController.php file.
 *
 * @category   Frontend
 * @package    STA
 * @subpackage Controller
 * @author     Ricardo Canaletti <ricardo.canaletti@lacamaradelcrimen2016.com.sw>
 * @license    MIT
 */

namespace AppBundle\Controller;

use AppBundle\Service\StatusService;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * StatusController.php file.
 *
 * @category   Frontend
 * @package    STA
 * @subpackage Controller
 * @author     Ricardo Canaletti <ricardo.canaletti@lacamaradelcrimen2016.com.sw>
 * @license    MIT
 */
class StatusController extends Controller
{
    /**
     * Find action.
     *
     * @param Request $request - Request.
     *
     * @Route("/status", name="sta_find", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function findAction(Request $request) : JsonResponse
    {
        $statusService = $this->getStatusService();

        $statusCollection = $statusService->find(
            [
                'status'            => $request->query->get('q', null)
            ],
            [
                'page'              => $request->query->get('p', 1),
                'limit'             => $request->query->get('r', 20),
                'orderBy'           => [
                    ['created_at', 'DESC']
                ]
            ]
        );

        return new JsonResponse($statusCollection->toArray());
    }

    /**
     * Post action.
     *
     * @param Request $request - Request.
     *
     * @Route("/status", name="sta_post", methods={"POST"})
     *
     * @return JsonResponse
     */
    public function postAction(Request $request) : JsonResponse
    {
        $statusService = $this->getStatusService();
        $requestData = $this->getJsonRequest($request);

        $status = $statusService->create(
            [
                'email'                 => isset($requestData['email']) ?
                    $requestData['email'] :
                    null,
                'status'                => isset($requestData['status']) ?
                    $requestData['status'] :
                    null
            ]
        );

        return new JsonResponse($status->toArray(), Response::HTTP_CREATED);
    }

    /**
     * getLogger.
     *
     * @return LoggerInterface
     */
    protected function getLogger() : LoggerInterface
    {
        return $this->get('logger');
    }

    /**
     * getStatusService method.
     *
     * @return StatusService
     */
    protected function getStatusService() : StatusService
    {
        return $this->get('sta.service.status');
    }

    /**
     * getJsonRequest.
     *
     * @param Request $request - Request.
     *
     * @return array|null
     */
    protected function getJsonRequest(Request $request)
    {
        $this->getLogger()->info('Received request: '.print_r($request->getContent(), true));

        return json_decode($request->getContent(), true);
    }
}
