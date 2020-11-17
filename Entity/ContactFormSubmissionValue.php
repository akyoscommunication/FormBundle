<?php

namespace Akyos\FormBundle\Entity;

use Akyos\FormBundle\Repository\ContactFormSubmissionValueRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass=ContactFormSubmissionValueRepository::class)
 */
class ContactFormSubmissionValue
{
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=ContactFormSubmission::class, inversedBy="contactFormSubmissionValues")
     * @ORM\JoinColumn(nullable=false)
     */
    private $contactFormSubmission;

    /**
     * @ORM\ManyToOne(targetEntity=ContactFormField::class, inversedBy="contactFormSubmissionValues")
     * @ORM\JoinColumn(nullable=false)
     */
    private $contactFormField;

    /**
     * @ORM\Column(type="text", length=99999, nullable=true)
     */
    private $value;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContactFormSubmission(): ?ContactFormSubmission
    {
        return $this->contactFormSubmission;
    }

    public function setContactFormSubmission(?ContactFormSubmission $contactFormSubmission): self
    {
        $this->contactFormSubmission = $contactFormSubmission;

        return $this;
    }

    public function getContactFormField(): ?ContactFormField
    {
        return $this->contactFormField;
    }

    public function setContactFormField(?ContactFormField $contactFormField): self
    {
        $this->contactFormField = $contactFormField;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }
}
