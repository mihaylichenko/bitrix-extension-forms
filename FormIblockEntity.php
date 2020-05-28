<?php
namespace Msvdev\Bitrix\Forms;
//MAKEME: сделать вывод ошибок сохранения формы
abstract class FormIblockEntity implements FormEntityInterface
{
    /**
     * @var integer
     */
    protected $id;
    /**
     * @var integer
     */
    protected $iblockId;
    /**
     * @return integer
     */
    abstract protected function getIblockId();
    /**
     * @return string
     */
    abstract protected function getElementName();

    /**
     * Before save event
     * @return bool
     */
    public function beforeSave(){
        return true;
    }

    /**
     * After save event
     */
    public function afterSave(){

    }

    /**
     * Save entity to bitrix Iblock
     * @return bool
     */
    public function save(){
        \CModule::IncludeModule("iblock");
        $fields = [];
        if(!$this->beforeSave()){
            return false;
        }
        $arFields = [
            "ACTIVE" => "Y",
            "IBLOCK_ID" => $this->getIblockId(),
            "NAME" => $this->getElementName()
        ];
        foreach ($this->fieldMapping() as $property => $field){
            $method = "get{$property}";
            if(method_exists($this,$method)){
                $value = $this->$method();
            }
            else{
                $value = $this->$property;
            }
            if($field == 'PREVIEW_TEXT' || $field == 'DETAIL_TEXT'){
                $arFields[$field] = $value;
            } else {
                $fields[$field] = $value;
            }
        }
        $arFields['PROPERTY_VALUES'] = $fields;
        $oElement = new \CIBlockElement();
        $idElement = $oElement->Add($arFields, false, false, true);
        if($idElement){
            $this->id = $idElement;
            $this->afterSave();
            return true;
        }
        return false;
    }
}