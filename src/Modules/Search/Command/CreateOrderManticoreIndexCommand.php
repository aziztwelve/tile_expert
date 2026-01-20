<?php

declare(strict_types=1);

namespace App\Modules\Search\Command;

use Manticoresearch\Client;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'manticore:create-order-index',
    description: 'Create Order Manticore search index for orders'
)]
class CreateOrderManticoreIndexCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $client = new Client(['host' => 'manticore', 'port' => 9308]);

        try {
            // Удаляем старый индекс если существует
            $client->table('orders')->drop();
            $output->writeln('Old index dropped');

            // Создаем новый индекс
            $client->table('orders')->create([
                'id' => ['type' => 'bigint'],
                'hash' => ['type' => 'string'],
                'number' => ['type' => 'string'],
                'name' => ['type' => 'text', 'options' => ['indexed']],
                'description' => ['type' => 'text', 'options' => ['indexed']],
                'status' => ['type' => 'string', 'options' => ['attribute']],
                'locale' => ['type' => 'string', 'options' => ['attribute']],
                'currency' => ['type' => 'string', 'options' => ['attribute']],
                'measure' => ['type' => 'string', 'options' => ['attribute']],
                'discount_percent' => ['type' => 'integer'],
                'manager_id' => ['type' => 'bigint'],
                'created_at' => ['type' => 'timestamp'],
                'updated_at' => ['type' => 'timestamp'],
                'manager_name' => ['type' => 'text', 'options' => ['indexed']],
                'items_count' => ['type' => 'integer'],
                'articles_names' => ['type' => 'text', 'options' => ['indexed']],
                'articles_skus' => ['type' => 'text', 'options' => ['indexed']],
            ], [
                'min_infix_len' => '3',
                'charset_table' => '0..9, a..z, A..Z->a..z, _, -, .',
            ]);

            $output->writeln('<info>Index "orders" created successfully!</info>');
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $output->writeln('<error>Failed to create index: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }
    }
}
