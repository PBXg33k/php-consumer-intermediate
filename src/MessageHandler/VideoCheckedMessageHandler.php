<?php


namespace App\MessageHandler;


use Pbxg33k\MessagePack\Message\CalculateFileHashesMessage;
use Pbxg33k\MessagePack\Message\GenerateThumbnailMessage;
use Pbxg33k\MessagePack\Message\PersistVideoStatusMessage;
use Pbxg33k\MessagePack\Message\VideoCheckedMessage;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class VideoCheckedMessageHandler implements MessageHandlerInterface
{
    /**
     * @var MessageBusInterface
     */
    private $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function __invoke(VideoCheckedMessage $message)
    {
        $this->messageBus->dispatch(new PersistVideoStatusMessage($message->getPath(), $message->isChecked(), $message->isConsistent()));
        if($message->isChecked() && $message->isConsistent()) {
            $this->messageBus->dispatch(new GenerateThumbnailMessage($message->getPath()));
            $this->messageBus->dispatch(new CalculateFileHashesMessage($message->getPath(), CalculateFileHashesMessage::HASH_XXHASH | CalculateFileHashesMessage::HASH_MD5));
        }

        return true;
    }
}
