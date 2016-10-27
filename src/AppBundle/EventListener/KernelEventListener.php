<?php
/**
 * KernelEventListener.php file.
 *
 * @category   Frontend
 * @package    STA
 * @subpackage EventListener
 * @author     Ricardo Canaletti <ricardo.canaletti@lacamaradelcrimen2016.com.sw>
 * @license    MIT
 */

namespace AppBundle\EventListener;

use AppBundle\Exception\ApiException;
use AppBundle\Model\ErrorResponse;
use AppBundle\Service\ErrorCodes;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\FinishRequestEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


/**
 * KernelEventListener.php file.
 *
 * @category   Frontend
 * @package    STA
 * @subpackage EventListener
 * @author     Ricardo Canaletti <ricardo.canaletti@lacamaradelcrimen2016.com.sw>
 * @license    MIT
 */
class KernelEventListener
{
    /**
     * Field _logger.
     *
     * @var LoggerInterface
     */
    private $_logger;

    /**
     * Field _siteUrl.
     *
     * @var string
     */
    private $_siteUrl;


    /**
     * StatusService constructor.
     *
     * @param LoggerInterface $logger  - Logger.
     * @param string          $siteUrl - Site URL.
     */
    public function __construct(LoggerInterface $logger, $siteUrl)
    {
        $this->_logger = $logger;
        $this->_siteUrl = $siteUrl;
    }

    /**
     * onKernelRequest.
     *
     * @param GetResponseEvent $event - Event.
     *
     * @return void
     */
    public function onKernelRequest(GetResponseEvent $event)
    {

    }

    /**
     * onKernelController.
     *
     * @param FilterControllerEvent $event - Event.
     *
     * @return void
     */
    public function onKernelController(FilterControllerEvent $event)
    {

    }

    /**
     * onKernelResponse.
     *
     * @param FilterResponseEvent $event - Event.
     *
     * @return void
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {

    }

    /**
     * onKernelFinishRequest.
     *
     * @param FinishRequestEvent $event - Event.
     *
     * @return void
     */
    public function onKernelFinishRequest(FinishRequestEvent $event)
    {

    }

    /**
     * onKernelTerminate.
     *
     * @param PostResponseEvent $event - Event.
     *
     * @return void
     */
    public function onKernelTerminate(PostResponseEvent $event)
    {

    }

    /**
     * onKernelException.
     *
     * @param GetResponseForExceptionEvent $event - Event.
     *
     * @return void
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        $this->_logger->error(
            'Exception ('.get_class($exception).') - Code: '.$exception->getCode().' - Message: '.
            $exception->getMessage().' - Stack Trace: '.$exception->getTraceAsString()
        );

        // Simple exception handling to transform the exception in a valid API response...

        $errorResponse = new ErrorResponse(
            [
                'code'              => $exception->getCode() ?
                    $exception->getCode() :
                    ErrorCodes::ERR_UNKNOWN,
                'message'           => $exception->getMessage(),
                'link'              => $this->_siteUrl.'/docs'
            ]
        );

        $this->_logger->error('Returning Error Response', $errorResponse->toArray());

        if ($exception instanceof ApiException) {
            $httpStatus = $exception->getHttpStatusCode();
        } else if ($exception instanceof NotFoundHttpException) {
            $httpStatus = 404;
            $errorResponse->setCode(ErrorCodes::ERR_RESOURCE_NOT_FOUND)
                ->setMessage(ErrorCodes::getMessage(ErrorCodes::ERR_RESOURCE_NOT_FOUND));
        } else {
            $httpStatus = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        $response = new JsonResponse($errorResponse->toArray(), $httpStatus);

        $event->setResponse($response);
    }
}