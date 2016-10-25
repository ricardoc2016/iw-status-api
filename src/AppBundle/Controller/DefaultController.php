<?php

/**
 * DefaultController.php file.
 *
 * @category   Frontend
 * @package    STA
 * @subpackage Controller
 * @author     Ricardo Canaletti <ricardo.canaletti@lacamaradelcrimen2016.com.sw>
 * @license    MIT
 */

namespace AppBundle\Controller;

use AppBundle\Service\StatusService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * DefaultController.php file.
 *
 * @category   Frontend
 * @package    STA
 * @subpackage Controller
 * @author     Ricardo Canaletti <ricardo.canaletti@lacamaradelcrimen2016.com.sw>
 * @license    MIT
 */
class DefaultController extends Controller
{
    /**
     * @Route("/", name="sta_find", methods={"GET"})
     */
    public function findAction(Request $request)
    {
        $statusService = $this->getStatusService();

        $res = $statusService->find(
            [
                'status'            => $request->query->get('q', null)
            ],
            [
                'page'              => $request->query->get('p', 1),
                'limit'             => $request->query->get('r', 20)
            ]
        );

        return new JsonResponse($res);
    }


    /**
     * getStatusService method.
     *
     * @return StatusService
     */
    protected function getStatusService()
    {
        return $this->get('sta.service.status');
    }
}
