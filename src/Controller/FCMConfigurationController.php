<?php

declare(strict_types=1);

namespace CodeBuds\SyliusFCMPlugin\Controller;

use CodeBuds\SyliusFCMPlugin\Entity\FCMConfiguration;
use CodeBuds\SyliusFCMPlugin\Form\Type\ImportType;
use CodeBuds\SyliusFCMPlugin\Form\Type\TestNotificationType;
use CodeBuds\SyliusFCMPlugin\Repository\FCMConfigurationRepositoryInterface;
use CodeBuds\SyliusFCMPlugin\Service\FirebaseMessaging;
use DateTime;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Exception\MessagingException;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Resource\ResourceActions;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolation;
use Twig\Environment;

class FCMConfigurationController extends ResourceController
{
    private ?string $credentialsFileLocation;
    private FormFactoryInterface $formFactory;
    private Environment $twig;
    private FirebaseMessaging $messaging;

    public function showAction(Request $request): Response
    {
        $requestConfiguration = $this->requestConfigurationFactory->create($this->metadata, $request);
        /** @var FCMConfigurationRepositoryInterface $configurationRepository */
        $configurationRepository = $this->manager->getRepository(FCMConfiguration::class);

        $configuration = $configurationRepository->findFirstConfiguration() ?: new FCMConfiguration();

        $this->eventDispatcher->dispatch(ResourceActions::SHOW, $requestConfiguration, $configuration);

        $importForm = $this->formFactory->create(ImportType::class);
        $testNotificationForm = $this->formFactory->create(TestNotificationType::class);

        if ($requestConfiguration->isHtmlRequest()) {
            return $this->render($requestConfiguration->getTemplate(ResourceActions::SHOW . '.html'), [
                'configuration' => $requestConfiguration,
                'metadata' => $this->metadata,
                'resource' => $configuration,
                'credentials' => null,
                'importForm' => $importForm->createView(),
                'testNotificationForm' => $testNotificationForm->createView(),
                'credentialsInformation' => $this->getFileInformation(),
                $this->metadata->getName() => $configuration,
            ]);
        }

        return $this->createRestView($requestConfiguration, $configuration);
    }

    private function getFileInformation(): ?array
    {
        $fileSystem = new Filesystem();

        $credentialsExist = $fileSystem->exists($this->credentialsFileLocation);
        if ($credentialsExist) {
            $credentialsInformation = [
                'filename' => $this->credentialsFileLocation,
                'modified' => (new DateTime())->setTimestamp(filemtime($this->credentialsFileLocation))
            ];
        }
        return $credentialsInformation ?? null;
    }

    public function uploadAction(Request $request): Response
    {
        $form = $this->formFactory->create(ImportType::class);
        $form->submit($request->request->get($form->getName()));
        $validator = $this->get('validator');

        if ($form->isSubmitted()) {
            $file = $request->files->get('import')['file'];
            $violations = $validator->validate(
                $file,
                [
                    new NotBlank(),
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'application/json',
                        ]
                    ])
                ]
            );
            if (count($violations) > 0) {
                /** @var ConstraintViolation $violation */
                foreach ($violations as $violation) {
                    $form->addError(new FormError($violation->getMessage()));
                }
                $status = 422;
            } else {
                $this->uploadFile($file);
            }
        }
        $formRendered = $this->twig->render('@CodeBudsSyliusFCMPlugin/Grid/Form/_importForm.html.twig', [
            'form' => $form->createView(),
            'credentialsInformation' => $this->getFileInformation(),
        ]);
        return new JsonResponse(['form' => $formRendered], $status ?? 200);
    }

    private function uploadFile(UploadedFile $file): void
    {
        $fileSystem = new Filesystem();
        $fileSystem->copy($file->getRealPath(), $this->credentialsFileLocation, true);
    }

    public function testNotificationAction(Request $request): Response
    {
        $form = $this->formFactory->create(TestNotificationType::class);
        $form->submit($request->request->get($form->getName()));

        if ($form->isSubmitted() && $form->isValid()) {
            $target = $form['target']->getData();
            $targetType = $form['targetType']->getData();
            $title = $form['title']->getData();
            $body = $form['body']->getData();
            $dataString = $form['data']->getData();
            $data = $dataString ? json_decode($dataString, true, 512, JSON_THROW_ON_ERROR) : [];

            try {
                $this->messaging->sendTestMessage($target, $title, $body, $data, $targetType);
            } catch (FirebaseException|MessagingException $e) {
                $error = $e->getMessage();
            }
        }

        $formRendered = $this->twig->render('@CodeBudsSyliusFCMPlugin/Grid/Form/_testNotificationForm.html.twig', [
            'form' => $form->createView()
        ]);
        return new JsonResponse(['form' => $formRendered, 'error' => $error ?? null]);
    }

    public function setCredentialsFileLocation(string $location): void
    {
        $this->credentialsFileLocation = $location;
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
