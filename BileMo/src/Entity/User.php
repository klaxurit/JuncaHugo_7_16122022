<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "detailUser",
 *          parameters = { "id" = "expr(object.getId())" }
 *      ),
 *      exclusion = @Hateoas\Exclusion(groups="getUsers")
 * )
 * 
 * @Hateoas\Relation(
 *      "deleteUser",
 *      href = @Hateoas\Route(
 *          "deleteUser",
 *          parameters = { "id" = "expr(object.getId())" },
 *      ),
 *      exclusion = @Hateoas\Exclusion(groups="getUsers", excludeIf = "expr(not is_granted('ROLE_USER'))"),
 * )
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(["getUsers"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["getUsers", "createUser"])]
    #[Assert\NotBlank(message: "Le prénom de l'utilisateur est obligatoire")]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    #[Groups(["getUsers", "createUser"])]
    #[Assert\NotBlank(message: "Le nom de l'utilisateur est obligatoire")]
    private ?string $lastname = null;

    #[ORM\Column(length: 255)]
    #[Groups(["getUsers", "createUser"])]
    #[Assert\Email(
        message: 'The email {{ value }} is not a valid email.',
    )]
    #[Assert\NotBlank(message: "L'email de l'utilisateur est obligatoire")]
    private ?string $email = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    #[Groups(["getUsers"])]
    private ?Company $company = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }
}
