<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Entity\Trait\TimestampableTrait;
use App\Interface\TimestampableEntityInterface;
use App\Repository\InvitationRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity(repositoryClass: InvitationRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            security: 'user.isAdmin()'
        ),
        new Get(
            security: 'user.isAdmin() 
                or (user.isCompanyAdmin() and object.getCompany() == user.getCompany())
                or (object.getReceiver() == user)'
        ),
        new Post(
            security: 'user.isAdmin() 
                or (user.isCompanyAdmin() and object.getCompany() == user.getCompany())'
        ),
        new Delete(
            security: 'user.isAdmin() 
                or (user.isCompanyAdmin() and object.getCompany() == user.getCompany())'
        )
    ],
    normalizationContext: ['groups' => ['invitation:read']],
    denormalizationContext: ['groups' => ['invitation:write']],
    order: ['createdAt' => 'DESC'],
)]
class Invitation implements TimestampableEntityInterface
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(class: 'Ramsey\Uuid\Doctrine\UuidOrderedTimeGenerator')]
    #[ApiProperty(identifier: true)]
    private ?UuidInterface $id = null;

    #[ORM\ManyToOne(inversedBy: 'invitations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?user $receiver = null;

    #[ORM\ManyToOne(inversedBy: 'invitations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?company $company = null;

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getReceiver(): ?user
    {
        return $this->receiver;
    }

    public function setReceiver(?user $receiver): static
    {
        $this->receiver = $receiver;

        return $this;
    }

    public function getCompany(): ?company
    {
        return $this->company;
    }

    public function setCompany(?company $company): static
    {
        $this->company = $company;

        return $this;
    }
}