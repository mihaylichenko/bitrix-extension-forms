<?php
namespace Msvdev\Bitrix\Forms;


interface FormEntityInterface
{
    public function save();

    public function beforeSave(&$arFields);

    public function afterSave();

    public function fieldMapping();
}
