<?php
declare(strict_types=1);

namespace Despark\PasswordPolicyBundle\Model;

use Doctrine\Common\Collections\Collection;

/**
 * Interface HasPasswordPolicyInterface
 * @package Despark\PasswordPolicyBundle\Model
 */
interface HasPasswordPolicyInterface
{
    /**
     * @return mixed
     */
    public function getId();

    /**
     * @return \DateTimeInterface
     */
    public function getPasswordChangedAt(): ?\DateTimeInterface;

    /**
     * @param \DateTimeInterface $dateTime
     */
    public function setPasswordChangedAt(\DateTimeInterface $dateTime): void;

    /**
     * @return Collection|PasswordHistoryInterface[]
     */
    public function getPasswordHistory(): Collection;

    /**
     * @param PasswordHistoryInterface $passwordHistory
     */
    public function addPasswordHistory(PasswordHistoryInterface $passwordHistory): void;

    /**
     * @return mixed
     */
    public function getPassword();

    /**
     * @return string|null The salt
     */
    public function getSalt(): ?string;

}
