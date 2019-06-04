<?php


namespace Despark\PasswordPolicyBundle\Service;


use Despark\PasswordPolicyBundle\Model\HasPasswordPolicyInterface;
use Despark\PasswordPolicyBundle\Model\PasswordHistoryInterface;
use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
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
     * @param \Despark\PasswordPolicyBundle\Model\HasPasswordPolicyInterface $entity
     * @return \Despark\PasswordPolicyBundle\Model\PasswordHistoryInterface|null
     */
    public function getHistoryByPassword(
        string $password,
        HasPasswordPolicyInterface $entity
    ): ?PasswordHistoryInterface {
        $history = $entity->getPasswordHistory();

        //Creating current password as history item to check against it too.
        $currentPasswordHistoryItem = new ParticipantPasswordHistory();
        $currentPasswordHistoryItem->setParticipant($entity);
        $currentPasswordHistoryItem->setPassword($entity->getPassword());
        $currentPasswordHistoryItem->setCreatedAt($entity->getPasswordChangedAt());

        $encoder = $this->getEncoder($entity);

        foreach (array_merge($history, [$currentPasswordHistoryItem]) as $passwordHistory) {
            if ($encoder->isPasswordValid($passwordHistory->getPassword(), $password, $passwordHistory->getSalt())) {
                return $passwordHistory;
            }
        }

        return null;
    }

    /**
     * @param \Despark\PasswordPolicyBundle\Model\HasPasswordPolicyInterface $entity
     * @return \Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface
     */
    public function getEncoder(HasPasswordPolicyInterface $entity): PasswordEncoderInterface
    {
        if ($entity instanceof UserInterface) {
            return $this->encoderFactory->getEncoder($entity);
        } else {
            return new BCryptPasswordEncoder(3);
        }
    }

}
