<?php

namespace Akyos\FormBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OrderBy;
use Gedmo\Mapping\Annotation as Gedmo;
use Akyos\FormBundle\Entity\ContactFormField;
use Akyos\FormBundle\Repository\ContactFormRepository;

#[ORM\Entity(repositoryClass: ContactFormRepository::class)]
class ContactForm
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $title;

    #[Gedmo\Slug(fields: ['title'])]
    #[ORM\Column(type: 'string', length: 255)]
    private $slug;

    #[ORM\Column(type: 'string', length: 999999999, nullable: true)]
    private $Mail;

    #[ORM\OneToMany(targetEntity: ContactFormField::class, mappedBy: 'contactForm', orphanRemoval: true)]
    #[OrderBy(['position' => 'ASC'])]
    private $contactFormFields;

    #[ORM\Column(type: 'string', length: 255)]
    private $formTo;

    #[ORM\Column(type: 'string', length: 255)]
    private $formObject;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $template;

    #[ORM\OneToMany(targetEntity: ContactFormSubmission::class, mappedBy: 'contactForm')]
    private $contactFormSubmissions;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $formTemplate;

    public function __construct()
    {
        $this->contactFormFields = new ArrayCollection();
        $this->contactFormSubmissions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->Mail;
    }

    public function setMail(?string $Mail): self
    {
        $this->Mail = $Mail;

        return $this;
    }

    /**
     * @return Collection|ContactFormField[]
     */
    public function getContactFormFields(): Collection
    {
        return $this->contactFormFields;
    }

    public function addContactFormField(ContactFormField $contactFormField): self
    {
        if (!$this->contactFormFields->contains($contactFormField)) {
            $this->contactFormFields[] = $contactFormField;
            $contactFormField->setContactForm($this);
        }

        return $this;
    }

    public function removeContactFormField(ContactFormField $contactFormField): self
    {
        if ($this->contactFormFields->contains($contactFormField)) {
            $this->contactFormFields->removeElement($contactFormField);
            // set the owning side to null (unless already changed)
            if ($contactFormField->getContactForm() === $this) {
                $contactFormField->setContactForm(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->title;
    }

    public function getFormTo(): ?string
    {
        return $this->formTo;
    }

    public function setFormTo(string $formTo): self
    {
        $this->formTo = $formTo;

        return $this;
    }

    public function getFormObject(): ?string
    {
        return $this->formObject;
    }

    public function setFormObject(string $formObject): self
    {
        $this->formObject = $formObject;

        return $this;
    }

    public function getTemplate(): ?string
    {
        return $this->template;
    }

    public function setTemplate(?string $template): self
    {
        $this->template = $template;

        return $this;
    }

    /**
     * @return Collection|ContactFormSubmission[]
     */
    public function getContactFormSubmissions(): Collection
    {
        return $this->contactFormSubmissions;
    }

    public function addContactFormSubmission(ContactFormSubmission $contactFormSubmission): self
    {
        if (!$this->contactFormSubmissions->contains($contactFormSubmission)) {
            $this->contactFormSubmissions[] = $contactFormSubmission;
            $contactFormSubmission->setContactForm($this);
        }

        return $this;
    }

    public function removeContactFormSubmission(ContactFormSubmission $contactFormSubmission): self
    {
        // set the owning side to null (unless already changed)
        if ($this->contactFormSubmissions->removeElement($contactFormSubmission) && $contactFormSubmission->getContactForm() === $this) {
            $contactFormSubmission->setContactForm(null);
        }

        return $this;
    }

    public function getFormTemplate(): ?string
    {
        return $this->formTemplate;
    }

    public function setFormTemplate(?string $formTemplate): self
    {
        $this->formTemplate = $formTemplate;

        return $this;
    }
}
