<?php
namespace kcal;

class Util{
    
    /**
     * valid_array(): ensures elements of $array are from $domain
     *   if $array is not empty. Otherwise returns the $domain itself      
     */
    public static function valid_array($array, $domain)
    {
        if (empty($array)) return $domain;
        if (!is_array($array)) $array=[$array];
        return array_intersect($array, $domain);
    } 

    /** 
     * product(): cartesion product of uniqie element from two arrays
     */
    public static function product($array1, $array2)
    {
        $product = [];
        $array1 = array_values(array_unique($array1));
        $array2 = array_values(array_unique($array2));
        foreach ($array1 as $e1)
            foreach ($array2 as $e2)
                $product[] = [$e1, $e2];
        return $product;
    } 
}