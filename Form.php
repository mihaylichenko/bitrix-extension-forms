<?php
namespace Msvdev\Bitrix\Forms;

use Bitrix\Main\Localization\Loc;
use CBitrixComponent;
use Symfony\Component\Form\Extension\Templating\TemplatingRendererEngine;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\Form\Forms;
use Symfony\Component\Templating\PhpEngine;
use Symfony\Component\Translation\Loader\ArrayLoader;
use Symfony\Component\Translation\Loader\XliffFileLoader;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Msvdev\Bitrix\Component;

class Form
{

    /**
     * @var CBitrixComponent
     */
    protected $component;

    /**
     * @var PhpEngine
     */
    protected $view;

    /**
     * @var FormFactory
     */
    protected $formBuilder;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @var array
     */
    public $errors = [];

    /**
     * Form constructor.
     * @param Component $component
     */
    public function __construct(Component $component)
    {
        $this->component = $component;
        $this->initFormComponent();
    }

    /**
     * @return FormFactory
     */
    public function getFormBuilder(){
        return $this->formBuilder;
    }

    /**
     * @return PhpEngine
     */
    public function getView(){
        return $this->view;
    }

    /**
     * @return mixed
     */
    public function getValidator(){
        return $this->validator;
    }


    /**
     * Здесь мы заставляем работать компонент форм symfony без фреймворка,
     * дружим языковые файлы битиркса и symfony и т.д.
     * Примеры: https://gist.github.com/ahukkanen/bd9c4c337492677d9327
     * Примеры: https://github.com/lostedboy/symfony-form-standalone
     */
    protected function initFormComponent(){
        $viewEngine = $this->component->getView();
        $formEngine = $this->getFormEngine($viewEngine);
        $formRenderer = new FormRenderer($formEngine);
        $formHelper = new FormHelper($formRenderer);
        $translator = $this->getTranslator();
        $translatorHelper = new TranslatorHelper($translator);
        $viewEngine->addHelpers(array($formHelper, $translatorHelper));

        $formFactory = Forms::createFormFactoryBuilder();
        $this->validator = $this->getValidatorComponent($translator);
        $formFactory->addExtension(new ValidatorExtension($this->validator));
        $formFactory->addTypeExtension(new FormTypeExtension());
        $this->formBuilder = $formFactory->getFormFactory();
    }

    /**
     * @param PhpEngine $viewEngine
     * @return TemplatingRendererEngine
     */
    protected function getFormEngine(PhpEngine $viewEngine){
        $templateDir = $this->getTemplateDir();
        $templates = [
            __DIR__ . '/Resources/views/Form',
            $this->getDefaultTemplateFormDir(),
            $templateDir . '/form_part'
        ];
        $templates = array_unique($templates);
        $formEngine = new TemplatingRendererEngine($viewEngine, $templates);
        return $formEngine;
    }

    /**
     * @param string $locale
     * @return Translator
     */
    protected function getTranslator($locale = 'ru'){
        $translator = new Translator($locale);
        // Add validators default messages
        $translator->addLoader('xlf', new XliffFileLoader());
        $translator->addResource('xlf', __DIR__ . '/Resources/translations/validators.ru.xlf', $locale, 'validators');
        // Add template lang file
        $translator->addLoader('array', new ArrayLoader());
        $translator->addResource('array',$this->getTemplateTranslationsArray(),$locale);
        // Add additional validator messages
        $translator->addResource('array',$this->getTemplateTranslationsArray(),$locale, 'validators');
        return $translator;
    }

    /**
     * @param Translator $translator
     * @return ValidatorInterface
     */
    protected function getValidatorComponent(Translator $translator){
        $validator = Validation::createValidatorBuilder();
        $validator->addMethodMapping('loadValidatorMetadata');
        $validator->setTranslator($translator);
        $validator->setTranslationDomain('validators');
        return $validator->getValidator();
    }

    /**
     * Get template dir
     * @return string
     */
    protected function getTemplateDir(){
        $this->component->initComponentTemplate('template');
        $template = & $this->component->GetTemplate();
        $templateDir = $template->GetFolder();
        return $_SERVER['DOCUMENT_ROOT'].$templateDir;
    }

    /**
     * @return string
     */
    protected function getDefaultTemplateFormDir(){
        return $_SERVER['DOCUMENT_ROOT'].$this->component->getPath().'/templates/.default/form_part';
    }

    /**
     * @return array
     */
    protected function getTemplateTranslationsArray(){
        $file = $_SERVER['DOCUMENT_ROOT'].$this->component->GetTemplate()->GetFile();
        $messages = Loc::loadLanguageFile($file);
        return $messages;
    }


    /**
     * @param FormErrorIterator $formErrors
     * @return array
     */
    public function getErrorsArray(FormErrorIterator $formErrors){
        $errors = array();
        foreach ($formErrors as $key => $error) {
            $name = $error->getOrigin()->getName();
            $errors[$name][] = $error->getMessage();
        }
        return $errors;
    }
}