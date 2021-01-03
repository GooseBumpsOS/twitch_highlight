<?php

namespace App\Command;

use DateTime;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class FatalErrorNotificationCommand extends Command
{
    protected static $defaultName = 'notification:telegram';

    /**
     * @var array
     */
    private $telegramCredentials;

    public function __construct(array $telegramCredentials)
    {
        parent::__construct();
        $this->telegramCredentials = $telegramCredentials;
    }

    protected function configure()
    {
        $this
            ->setDescription('Command send notification to Telegram')
            ->addArgument('message', InputArgument::REQUIRED, 'Message to send');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $message = $input->getArgument('message');
        $io = new SymfonyStyle($input, $output);

        $this->sendMessage(
            $this->telegramCredentials['token'],
            $this->telegramCredentials['chat_id'],
            $message
        );

        $io->writeln('Send new message to telegram at ' .   (new DateTime())->format('d/m/Y H:i:s') . ' Message is: ' . $message);

        return Command::SUCCESS;
    }

    /**
     * @param string $token
     * @param string $msg
     * @param string $chatId
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    private function sendMessage(string $token, string $chatId, string $msg): void
    {
        $client = HttpClient::create();
        $response = $client->request('GET',
            "https://api.telegram.org/bot{$token}/sendMessage?chat_id={$chatId}&text=Twich%20project: $msg");

        $response->getContent();
    }
}
