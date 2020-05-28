<?php
namespace Msvdev\Bitrix\Forms;


use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class FormTypeExtension
 * @package Wn\Symfony\Forms
 * Расширение для добавления help сообщения
 * @see https://stackoverflow.com/questions/39944874/adding-help-option-to-symfony3-all-formtypes
 */
class FormTypeExtension extends AbstractTypeExtension
{
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['help'] = $options['help'];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'help' => null,
        ));
    }

    public function getExtendedType()
    {
        return FormType::class;
    }
}