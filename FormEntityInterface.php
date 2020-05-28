<?php
namespace Msvdev\Bitrix\Forms;


interface FormEntityInterface
{
    public function save();

    public function beforeSave();

    public function afterSave();

    public function fieldMapping();
}