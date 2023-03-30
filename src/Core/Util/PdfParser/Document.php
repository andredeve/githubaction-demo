<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Core\Util\PdfParser;

use Smalot\PdfParser\Pages;

/**
 * Description of Document
 *
 * @author victorcanario
 */
class Document extends \Smalot\PdfParser\Document {
    public function getPages()
    {
        if (isset($this->dictionary['Catalog'])) {
            // Search for catalog to list pages.
            $id = reset($this->dictionary['Catalog']);
            if((is_object($id) || is_string($id)) && method_exists($this->objects[$id],"get")){
                $object = $this->objects[$id]->get('Pages');
                if (method_exists($object, 'getPages')) {
                    return $object->getPages(true);
                }
            }
        }

        if (isset($this->dictionary['Pages'])) {
            // Search for pages to list kids.
            $pages = [];

            /** @var Pages[] $objects */
            $objects = $this->getObjectsByType('Pages');
            foreach ($objects as $object) {
                $pages = array_merge($pages, $object->getPages(true));
            }

            return $pages;
        }

        if (isset($this->dictionary['Page'])) {
            // Search for 'page' (unordered pages).
            $pages = $this->getObjectsByType('Page');

            return array_values($pages);
        }
        return array();
//        throw new \Exception('Missing catalog.');
    }
}
