<?php

namespace CodeBuds\SyliusFCMPlugin\Controller;

use App\Entity\User\ShopUser;
use CodeBuds\SyliusFCMPlugin\Entity\ProductFCMNotification;
use CodeBuds\SyliusFCMPlugin\Entity\ProductFCMTopic;
use CodeBuds\SyliusFCMPlugin\Entity\ShopUserFCMNotification;
use CodeBuds\SyliusFCMPlugin\Form\Type\NotificationType;
use CodeBuds\SyliusFCMPlugin\Service\FirebaseMessaging;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Resource\ResourceActions;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class FCMNotificationController extends ResourceController
{
    private FormFactoryInterface $formFactory;
    private Environment $twig;
    private FirebaseMessaging $messaging;

    public function createAction(Request $request): Response
    {
        $requestConfiguration = $this->requestConfigurationFactory->create($this->metadata, $request);
        $notificationForm = $this->formFactory->create(NotificationType::class, new ShopUserFCMNotification());
        if ($requestConfiguration->isHtmlRequest()) {
            return $this->render($requestConfiguration->getTemplate(ResourceActions::CREATE . '.html'), [
                'configuration' => $requestConfiguration,
                'metadata' => $this->metadata,
                'notificationForm' => $notificationForm->createView(),
                'credentials' => null,
            ]);
        }
        return $this->createRestView($requestConfiguration, []);
    }

    public function submitAction(Request $request): Response
    {

        $type = $request->request->get('notification')['targetType'];

        $entity = null;
        switch ($type) {
            case ShopUser::class:
                $entity = new ShopUserFCMNotification();
                break;
            case ProductFCMTopic::class:
                $entity = new ProductFCMNotification();
                break;
        }

        $notificationForm = $this->formFactory->create(NotificationType::class, $entity);

        $notificationForm->handleRequest($request);
        if ($notificationForm->isSubmitted() && $notificationForm->isValid()) {
            // ... save the meetup, redirect etc.
        }

        return $this->render('', [
            'form' => $notificationForm->createView(),
        ]);
    }

    public function setFormFactory(FormFactoryInterface $formFactory): void
    {
        $this->formFactory = $formFactory;
    }

    public function setTwigEnvironment(Environment $twig): void
    {
        $this->twig = $twig;
    }

    public function setFCMService(FirebaseMessaging $messaging): void
    {
        $this->messaging = $messaging;
    }
}
