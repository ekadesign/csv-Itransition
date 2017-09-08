<?php

namespace AppBundle\Command;

use AppBundle\DTO\ProductDTO;
use AppBundle\Entity\Product;
use AppBundle\Validator\CorrectFieldsValidator;
use AppBundle\Validator\MaxPriceValidator;
use AppBundle\Validator\Validator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Console\Style\SymfonyStyle;
use Carbon\Carbon;

class CsvImportCommand extends ContainerAwareCommand
{
    public $fails = 0;

    public $results = [];

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();

        $this->em = $em;
    }

    protected function configure()
    {
        $this
            ->setName('csv:import')
            ->setDescription('...')
            ->addArgument('argument', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $io = new SymfonyStyle($input, $output);
        $io->title('Attempting import of Feed...');

        $serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);

        // decoding CSV contents
        $csv = $serializer->decode(file_get_contents('csv/stock.csv'), 'csv');

        $io->progressStart();

        foreach ($csv as $item) {
            $item = new ProductDTO($item);
            $validator = new Validator($item);
            $io->progressAdvance();

            if(!$validator->getValidate()){
                $this->fails++;
                $this->results[] = "Product code: ". $item->product_code . " not imported.";
                continue;
            }
            //Record
            //find product by Product Code (unique)
            $product = $this->em->getRepository(Product::class)->findOneBy(array('product_code' => $item->product_code));
            if(!$product) {
                $product = new Product();
                $product->setCreatedAt(Carbon::now());
            }
                $product->setName($item->name);
                $product->setProductCode($item->product_code);
                $product->setDescription($item->description);
                $product->setPrice((float)$item->price ? $item->price : 0);
                $product->setQty((int)$item->qty ? $item->qty : 0);
                $product->setUpdatedAt(Carbon::now());
                (bool)$item->discontinued_at ? $product->setDiscontinuedAt(Carbon::now()) : '';
            //Передаем менеджеру объект модели
            $this->em->persist($product);
            //Добавляем запись в таблицу
            $this->em->flush();
        }
        $io->progressFinish();
        $io->title("Import Complete...");
        if(!empty($this->results)) {
            $io->title("Please check failed rows...");
            foreach ($this->results as $result) {
                $output->writeln($result);
            }
        }
    }


}
