<?php


namespace Despark\PasswordPolicyBundle\Traits;


use Doctrine\ORM\Mapping as ORM;

trait PasswordHistoryTrait
{

    /**
     * @var string
     * @ORM\Column(type="string")
     * @ORM\Id()
     */
    private $password;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    private $salt;

    /**
     * @var \DateTimeInterface|null
     * @ORM\Column(name="created_at", type="datetimetz_immutable")
     */
    private $createdAt;

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @return null|string
     */
    public function getSalt(): ?string
    {
        return $this->salt;
    }

    /**
     * @param null|string $salt
     */
    public function setSalt(?string $salt): void
    {
        $this->salt = $salt;
    }

    /**
     * @param \DateTimeInterface $dateTime
     */
    public function setCreatedAt(\DateTimeInterface $dateTime): void
    {
        $this->createdAt = $dateTime;
    }
}
