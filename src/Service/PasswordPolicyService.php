<?php
declare(strict_types=1);

namespace Despark\PasswordPolicyBundle\Service;

use App\Entity\UserPasswordHistory;
use Despark\PasswordPolicyBundle\Model\HasPasswordPolicyInterface;
use Despark\PasswordPolicyBundle\Model\PasswordHistoryInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Encoder\NativePasswordEncoder;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class PasswordPolicyService implements PasswordPolicyServiceInterface
{

    /**
     * @var \Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface
     */
    private $encoderFactory;

    /**
     * PasswordPolicyEnforcerService constructor.
     * @param \Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface $encoderFactory
     */
    public function __construct(EncoderFactoryInterface $encoderFactory)
    {
        $this->encoderFactory = $encoderFactory;
    }

    /**
     * @param string $password
     * @param HasPasswordPolicyInterface $entity
     * @return \Despark\PasswordPolicyBundle\Model\PasswordHistoryInterface|null
     */
    public function getHistoryByPassword(
        string $password,
        HasPasswordPolicyInterface $entity
    ): ?PasswordHistoryInterface {
        $history = $entity->getPasswordHistory();

        //Creating current password as history item to check against it too.
        $currentPasswordHistoryItem = new UserPasswordHistory();
        $currentPasswordHistoryItem->setUser($entity);
        $currentPasswordHistoryItem->setPassword($entity->getPassword());

        $encoder = $this->getEncoder($entity);

        foreach (array_merge($history->toArray(), [$currentPasswordHistoryItem]) as $passwordHistory) {
            if ($encoder->isPasswordValid($passwordHistory->getPassword(), $password, $passwordHistory->getSalt())) {
                return $passwordHistory;
            }
        }

        return null;
    }

    /**
     * @param HasPasswordPolicyInterface $entity
     * @return PasswordEncoderInterface
     */
    public function getEncoder(HasPasswordPolicyInterface $entity)
    {
        if ($entity instanceof UserInterface) {
            return $this->encoderFactory->getEncoder($entity);
        }

        return new NativePasswordEncoder(3);
    }

}
