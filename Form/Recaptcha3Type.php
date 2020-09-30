<?php declare(strict_types=1);

namespace Akyos\FormBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class Recaptcha3Type extends AbstractType
{
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['site_key'] = $options['site_key'];
        $view->vars['enabled'] = $options['enabled'];
        $view->vars['action_name'] = $options['action_name'];
        $view->vars['script_nonce_csp'] = $options['script_nonce_csp'] ?? '';
    }

    public function getParent(): string
    {
        return HiddenType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'karser_recaptcha3';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'mapped' => false,
            'site_key' => null,
            'enabled' => true,
            'action_name' => 'homepage',
            'script_nonce_csp' => '',
        ]);
    }
}
