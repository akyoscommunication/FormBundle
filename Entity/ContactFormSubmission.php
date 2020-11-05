<?php

namespace Akyos\FormBundle\Entity;

use Akyos\FormBundle\Repository\ContactFormSubmissionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass=ContactFormSubmissionRepository::class)
 */
class ContactFormSubmission
{
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=ContactForm::class, inversedBy="contactFormSubmissions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $contactForm;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $sentFrom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $sentTo;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $object;

    /**
     * @ORM\Column(type="text", length=99999)
     */
    private $body;

    /**
     * @ORM\Column(type="simple_array", nullable=true)
     */
    private $files = [];

    /**
     * @ORM\OneToMany(targetEntity=ContactFormSubmissionValue::class, mappedBy="contactFormSubmission", orphanRemoval=true)
     */
    private $contactFormSubmissionValues;

    public function __construct()
    {
        $this->contactFormSubmissionValues = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContactForm(): ?ContactForm
    {
        return $this->contactForm;
    }

    public function setContactForm(?ContactForm $contactForm): self
    {
        $this->contactForm = $contactForm;

        return $this;
    }

    public function getSentFrom(): ?string
    {
        return $this->sentFrom;
    }

    public function setSentFrom(string $sentFrom): self
    {
        $this->sentFrom = $sentFrom;

        return $this;
    }

    public function getSentTo(): ?string
    {
        return $this->sentTo;
    }

    public function setSentTo(string $sentTo): self
    {
        $this->sentTo = $sentTo;

        return $this;
    }

    public function getObject(): ?string
    {
        return $this->object;
    }

    public function setObject(string $object): self
    {
        $this->object = $object;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function getFiles(): ?array
    {
        return $this->files;
    }

    public function setFiles(array $files): self
    {
        $this->files = $files;

        return $this;
    }

    /**
     * @return Collection|ContactFormSubmissionValue[]
     */
    public function getContactFormSubmissionValues(): Collection
    {
        return $this->contactFormSubmissionValues;
    }

    public function addContactFormSubmissionValue(ContactFormSubmissionValue $contactFormSubmissionValue): self
    {
        if (!$this->contactFormSubmissionValues->contains($contactFormSubmissionValue)) {
            $this->contactFormSubmissionValues[] = $contactFormSubmissionValue;
            $contactFormSubmissionValue->setContactFormSubmission($this);
        }

        return $this;
    }

    public function removeContactFormSubmissionValue(ContactFormSubmissionValue $contactFormSubmissionValue): self
    {
        if ($this->contactFormSubmissionValues->removeElement($contactFormSubmissionValue)) {
            // set the owning side to null (unless already changed)
            if ($contactFormSubmissionValue->getContactFormSubmission() === $this) {
                $contactFormSubmissionValue->setContactFormSubmission(null);
            }
        }

        return $this;
    }
}
