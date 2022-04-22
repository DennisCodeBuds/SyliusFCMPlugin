<?php

namespace CodeBuds\SyliusFCMPlugin\Command;

use CodeBuds\SyliusFCMPlugin\Entity\ProductFCMTopic;
use CodeBuds\SyliusFCMPlugin\Repository\EntityTopicRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Product\Repository\ProductRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FcmProductTopicsGenerateCommand extends Command
{
    protected static $defaultName = 'fcm:product-topics:generate';
    protected static $defaultDescription = 'Generate topics for products';
    private ProductRepositoryInterface $productRepository;
    private EntityTopicRepository $FCMTopicRepository;
    private EntityManagerInterface $manager;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        EntityTopicRepository      $FCMTopicRepository,
        EntityManagerInterface     $manager
    )
    {
        parent::__construct();
        $this->productRepository = $productRepository;
        $this->FCMTopicRepository = $FCMTopicRepository;
        $this->manager = $manager;
    }


    protected function configure(): void
    {
        $this
            ->addOption('force', null, InputOption::VALUE_NONE, 'Set this to create the topics');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $products = $this->productRepository->findAll();

        $productsNoTopic = $this->FCMTopicRepository->getProductsWithoutTopic();

        $amountOfProducts = count($products);

        $amountOfProductsNoTopic = count($productsNoTopic);

        $io->note(sprintf('There are %d products', $amountOfProducts));

        $io->note(sprintf('There are %d products without topics', $amountOfProductsNoTopic));

        $progressBar = new ProgressBar($output, $amountOfProductsNoTopic);


        if ($input->getOption('force')) {
            foreach ($productsNoTopic as $product) {
                $progressBar->advance();
                $productTopic = (new ProductFCMTopic())
                    ->setProduct($product)
                    ->generateTopicId();
                $this->manager->persist($productTopic);
            }
            $this->manager->flush();
        } else {
            $io->note('Set the --force option to generate the product topics');
        }
        $progressBar->finish();

        return Command::SUCCESS;
    }
}
