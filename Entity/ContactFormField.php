<?php

namespace Akyos\FormBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="Akyos\FormBundle\Repository\ContactFormFieldRepository")
 */
class ContactFormField
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Slug(fields={"title"})
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="Akyos\FormBundle\Entity\ContactForm", inversedBy="contactFormFields")
     */
    private $contactForm;

    /**
     * @ORM\Column(type="integer")
     */
    private $position;

    /**
     * @ORM\Column(type="integer")
     */
    private $col;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isRequired;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $options;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $className;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $excludeRegex;

    /**
     * @ORM\OneToMany(targetEntity=ContactFormSubmissionValue::class, mappedBy="contactFormField")
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
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

    public function __toString()
    {
        return $this->title;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getCol(): ?int
    {
        return $this->col;
    }

    public function setCol(int $col): self
    {
        $this->col = $col;

        return $this;
    }

    public function getIsRequired(): ?bool
    {
        return $this->isRequired;
    }

    public function setIsRequired(bool $isRequired): self
    {
        $this->isRequired = $isRequired;

        return $this;
    }

    public function getOptions(): ?string
    {
        return $this->options;
    }

    public function setOptions(?string $options): self
    {
        $this->options = $options;

        return $this;
    }

    public function getClassName(): ?string
    {
        return $this->className;
    }

    public function setClassName(?string $className): self
    {
        $this->className = $className;

        return $this;
    }

    public function getExcludeRegex(): ?string
    {
        return $this->excludeRegex;
    }

    public function setExcludeRegex(?string $excludeRegex): self
    {
        $this->excludeRegex = $excludeRegex;

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
            $contactFormSubmissionValue->setContactFormField($this);
        }

        return $this;
    }

    public function removeContactFormSubmissionValue(ContactFormSubmissionValue $contactFormSubmissionValue): self
    {
        if ($this->contactFormSubmissionValues->removeElement($contactFormSubmissionValue)) {
            // set the owning side to null (unless already changed)
            if ($contactFormSubmissionValue->getContactFormField() === $this) {
                $contactFormSubmissionValue->setContactFormField(null);
            }
        }

        return $this;
    }
}
