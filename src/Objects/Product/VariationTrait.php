<?php
/**
 * This file is part of SplashSync Project.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 *  @author    Splash Sync <www.splashsync.com>
 *  @copyright 2015-2017 Splash Sync
 *  @license   GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007
 *
 **/

namespace Splash\Local\Objects\Product;

use Splash\Core\SplashCore      as Splash;

/**
 * WooCommerce Product Variation Data Access
 */
trait VariationTrait
{
    
    //====================================================================//
    // Fields Generation Functions
    //====================================================================//

    /**
    *   @abstract     Build Variation Fields using FieldFactory
    */
    private function buildVariationFields()
    {
        
        //====================================================================//
        // CHILD PRODUCTS INFORMATIONS
        //====================================================================//
        
        //====================================================================//
        // Product Variation List - Product Link
        $this->fieldsFactory()->Create(self::objects()->Encode("Product", SPL_T_ID))
                ->Identifier("id")
                ->Name(__("Children"))
                ->InList("children")
                ->MicroData("http://schema.org/Product", "Variation")
                ->isReadOnly();
        
        //====================================================================//
        // Product Variation List - Product SKU
        $this->fieldsFactory()->Create(SPL_T_VARCHAR)
                ->Identifier("sku")
                ->Name(__("SKU"))
                ->InList("children")
                ->MicroData("http://schema.org/Product", "VariationName")
                ->isReadOnly();
        
        //====================================================================//
        // Product Variation List - Variation Attribute
        $this->fieldsFactory()->Create(SPL_T_VARCHAR)
                ->Identifier("attribute")
                ->Name(__("Attribute"))
                ->InList("children")
                ->MicroData("http://schema.org/Product", "VariationAttribute")
                ->isReadOnly();
    }

    //====================================================================//
    // Fields Reading Functions
    //====================================================================//
    
    /**
     *  @abstract     Read requested Field
     *
     *  @param        string    $Key                    Input List Key
     *  @param        string    $FieldName              Field Identifier / Name
     *
     *  @return       void
     */
    private function getVariationsFields($Key, $FieldName)
    {
        //====================================================================//
        // Check if List field & Init List Array
        $FieldId = self::lists()->InitOutput($this->out, "children", $FieldName);
        if (!$FieldId) {
            return;
        }
        //====================================================================//
        // READ Fields
        foreach ($this->Product->get_children() as $Index => $Id) {
            switch ($FieldId) {
                case 'id':
                    self::lists()
                        ->Insert($this->out, "children", $FieldId, $Index, self::objects()->Encode("Product", $Id));
                    break;

                case 'sku':
                    self::lists()
                        ->Insert($this->out, "children", $FieldId, $Index, get_post_meta($Id, "_sku", true));
                    break;

                case 'attribute':
                    self::lists()->Insert(
                        $this->out,
                        "children",
                        $FieldId,
                        $Index,
                        implode(" | ", wc_get_product($Id)->get_attributes())
                    );
                    break;

                default:
                    return;
            }
        }
        unset($this->in[$Key]);
    }
}
