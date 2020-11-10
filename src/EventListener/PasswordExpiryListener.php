<?php
declare(strict_types=1);

namespace Despark\PasswordPolicyBundle\EventListener;

use Despark\PasswordPolicyBundle\Service\PasswordExpiryServiceInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class PasswordExpiryListener
{

    /**
     * @var PasswordExpiryServiceInterface
     */
    private $passwordExpiryService;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var string
     */
    private $errorMessageType;

    /**
     * @var string
     */
    private $errorMessage;

    /**
     * PasswordExpiryListener constructor.
     * @param PasswordExpiryServiceInterface $passwordExpiryService
     * @param SessionInterface $session
     * @param string $errorMessageType
     * @param string $errorMessage
     */
    public function __construct(
        PasswordExpiryServiceInterface $passwordExpiryService,
        SessionInterface $session,
        string $errorMessageType,
        string $errorMessage
    ) {
        $this->passwordExpiryService = $passwordExpiryService;
        $this->session = $session;
        $this->errorMessageType = $errorMessageType;
        $this->errorMessage = $errorMessage;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        $route = $request->get('_route');

        $lockedUrl = $this->passwordExpiryService->generateLockedRoute();
        $lockedPath = parse_url($lockedUrl, PHP_URL_PATH);

        if ($request->getPathInfo() === $lockedPath) {
            return;
        }

        if (!in_array($route, $this->passwordExpiryService->getExcludedRoutes())
            && $this->passwordExpiryService->isPasswordExpired()) {
            if ($this->session instanceof Session) {
                $this->session->getFlashBag()->add($this->errorMessageType, $this->errorMessage);
            }
            $event->setResponse(new RedirectResponse($lockedUrl));
        }
    }


}
