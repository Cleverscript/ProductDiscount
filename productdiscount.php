<?php
/**
 * Class for receiving all products to which discounts apply
 * Author: Dokukin Vyacheslav Olegovich <toorrp4@gmail.com>
 * Date: 23.11.2023
 */
class ProductDiscount
{
    private static $result;

    private static function CondIBName(array $data)
    {
        $str = match ($data['logic']) {
            'Contain' => "`NAME` like upper('%" . $data['value'] . "%')",
            'Equal' => "`NAME`=upper('%" . $data['value'] . "%')",
        };
    
        return $str;
    }
    
    private static function CondIBElement(array $data)
    {
        $str = match ($data['logic']) {
            'Contain' => "`ID` IN (" . explode(',', $data['value']) . ")",
            'Equal' => "`ID` IN (" . implode(',', $data['value']) . ")",
        };
    
        return $str;
    }
    
    private static function CondBsktFldProduct(array $data)
    {
        $str = "`ID` IN (" . explode(',', $data['value']) . ")";
        return $str;
    }

    public static function getDiscounProducts()
    {
        global $DB;

        $arOr = array();
        $date = date($DB->DateFormatToPHP('Y-m-d'), mktime(0, 0, 0, date("n"), date("d"), date("Y")));
        $results = $DB->Query("SELECT `ID`, `CONDITIONS`, `ACTIONS` FROM `b_sale_discount` WHERE `ACTIVE_TO` >= '" . $date . "' AND `ACTIVE` ='Y'");

        while($row = $results->Fetch())
        {
            $return_value = array();
            $ACTIONS = unserialize($row['ACTIONS']);

            foreach($ACTIONS['CHILDREN'] as $children)
            {

                foreach($children['CHILDREN'] as $child)
                {
                    if(isset($child['CHILDREN']))
                    {
                        foreach($child['CHILDREN'] as $cond)
                        {

                            if(!$cond['DATA'])
                                continue;

                            $return_value[] = match ($cond['CLASS_ID']) {
                                'CondIBName' => self::CondIBName($cond['DATA']),
                                'CondIBElement' => self::CondIBElement($cond['DATA']),
                                'CondBsktFldProduct' => self::CondBsktFldProduct($cond['DATA']),
                                default => null
                            };
                        }
                    }else{

                        if(!$child['DATA'])
                            continue;

                        $return_value[] = match ($child['CLASS_ID']) {
                            'CondIBName' => self::CondIBName($child['DATA']),
                            'CondIBElement' => self::CondIBElement($child['DATA']),
                            'CondBsktFldProduct' => self::CondBsktFldProduct($child['DATA']),
                            default => null
                        };
                    }
                }
            }
        
            if($return_value)
                $arOr[] = implode(' OR ', $return_value);
        
        }

        if(count($arOr))
        {
            $res = $DB->Query("SELECT `ID` FROM `b_iblock_element` WHERE " . implode(' OR ', $arOr) . " AND `ACTIVE` = 'Y';");
            
            while($el = $res->Fetch())
            {
                self::$result[] = $el['ID'];
            }

            self::$result = array_unique(self::$result);

            return self::$result;
        }

        return array();
    }
}
?>