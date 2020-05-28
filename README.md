# Хелпер для использования symfony форм в битиркс

Установка <code>composer require msvdev/bitrix-extension-forms</code>

##Пример
```php

<?php
namespace Components\User\Login;

use Modules\User\Entities\Login;
use Modules\User\Forms\LoginType;
use Msvdev\Bitrix\Forms\Form;

class Component extends \Msvdev\Bitrix\Component\Component
{
            
    public function executeComponent()
    {
        global $APPLICATION;
        global $USER;
        $entity = new Login($USER);       
        $symfonyForm = new Form($this);
        $form = $symfonyForm->getFormBuilder()->create(LoginType::class, $entity);
        $form->handleRequest();
        if ($this->request->isAjaxRequest() && $form->isSubmitted()) {
            $APPLICATION->RestartBuffer();
            $result = [
                'result' => false,
                'errors' => [],
                'redirectUrl' => null
            ];
            if($form->isValid()){
                $entity->auth();
                $result['result'] = true;
                die();
            } else { // Get ajax errors
                $result['errors'] = $symfonyForm->getErrorsArray($form->getErrors(true));
            }
            echo json_encode($result);
            die();
        }
        $this->arResult['formView'] = $form->createView();
        $this->arResult['entity'] = $entity;
        $this->includeComponentTemplate();
    }

}

```